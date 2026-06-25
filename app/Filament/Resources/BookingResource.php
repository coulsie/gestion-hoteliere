<?php

namespace App\Filament\Resources;

use App\Models\Booking;
use App\Models\Room;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\BookingResource\Pages\ListBookings;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Réservations de Chambres';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Hôtelière';
    protected static ?string $pluralModelLabel = 'Réservations de Chambres';
    protected static ?string $modelLabel = 'Réservation';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('customer_name')
                    ->label('Nom du client')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('room_id')
                    ->label('Chambre N°')
                    ->relationship(
                        name: 'room',
                        titleAttribute: 'number',
                        modifyQueryUsing: fn($query) => $query->where('housekeeping_status', 'propre')
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::synchroniserDatesPassage($get, $set);
                        static::calculerTarifDynamique($get, $set);
                    })
                    ->rules([
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $checkIn = $get('check_in');
                                $checkOut = $get('check_out');
                                $bookingId = $record?->id ?? $get('id');

                                if ($value && $checkIn && $checkOut) {
                                    if (static::verifierOccupationChambre($value, $checkIn, $checkOut, $bookingId)) {
                                        $fail(static::obtenirMessageErreurOccupation($value, $checkIn, $checkOut, $bookingId));
                                    }
                                }
                            };
                        },
                    ]),

                // 1. ARRIVÉE
                Forms\Components\DateTimePicker::make('check_in')
                    ->label('Date & Heure d\'arrivée')
                    ->default(now()->startOfMinute())
                    ->required()
                    ->live()
                    ->seconds(false)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::synchroniserDatesPassage($get, $set);
                        static::calculerTarifDynamique($get, $set);
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if ($value && \Carbon\Carbon::parse($value)->isBefore(now()->subMinutes(15))) {
                                    $fail("L'heure d'arrivée ne peut pas être située dans le passé.");
                                }
                            };
                        },
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $roomId = $get('room_id');
                                $checkOut = $get('check_out');
                                $bookingId = $record?->id ?? $get('id');

                                if ($roomId && $value && $checkOut) {
                                    if (static::verifierOccupationChambre($roomId, $value, $checkOut, $bookingId)) {
                                        $fail(static::obtenirMessageErreurOccupation($roomId, $value, $checkOut, $bookingId));
                                    }
                                }
                            };
                        },
                    ]),

                // 2. NOMBRE HEURES
                Forms\Components\Select::make('nombre_heures')
                    ->label('Durée du passage (Heures)')
                    ->options([
                        1 => '1 Heure de passage',
                        2 => '2 Heures de passage',
                        3 => '3 Heures de passage',
                        4 => '4 Heures de passage',
                    ])
                    ->default(1)
                    ->required()
                    ->live()
                    ->dehydrated(false)
                    ->visible(function ($get) {
                        $roomId = $get('room_id');
                        if (!$roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        return str_contains(strtolower($room?->roomType?->name ?? ''), 'passage');
                    })
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::synchroniserDatesPassage($get, $set);
                        static::calculerTarifDynamique($get, $set);
                    }),

                // 3. DÉPART POUR CHAMBRE NORMALE (Sélecteur classique actif)
                Forms\Components\DateTimePicker::make('check_out')
                    ->label('Date & Heure de départ')
                    ->default(now()->addDay()->startOfMinute())
                    ->required()
                    ->live()
                    ->seconds(false)
                    ->dehydrated()
                    ->visible(function ($get) {
                        $roomId = $get('room_id');
                        if (!$roomId) return true;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        return !str_contains(strtolower($room?->roomType?->name ?? ''), 'passage');
                    })
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::calculerTarifDynamique($get, $set);
                    })
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $checkIn = $get('check_in');
                                if ($checkIn && $value && \Carbon\Carbon::parse($value)->isBefore(\Carbon\Carbon::parse($checkIn))) {
                                    $fail("La date et heure de départ doivent être supérieures à l'arrivée.");
                                }
                            };
                        },
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $roomId = $get('room_id');
                                $checkIn = $get('check_in');
                                $bookingId = $record?->id ?? $get('id');

                                if ($roomId && $checkIn && $value) {
                                    if (static::verifierOccupationChambre($roomId, $checkIn, $value, $bookingId)) {
                                        $fail(static::obtenirMessageErreurOccupation($roomId, $checkIn, $value, $bookingId));
                                    }
                                }
                            };
                        },
                    ]),

                // 4. AFFICHAGE DU DÉPART POUR LES PASSAGES (Lecture seule textuelle, sans blocage JS)
                Forms\Components\TextInput::make('check_out_display')
                    ->label('Date & Heure de départ (Calculé)')
                    ->disabled() // Bloqué proprement à l'écran
                    ->dehydrated(false) // Non enregistré en BDD (purement visuel)
                    ->visible(function ($get) {
                        $roomId = $get('room_id');
                        if (!$roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        return str_contains(strtolower($room?->roomType?->name ?? ''), 'passage');
                    }),

                // 5. CHAMP TECHNIQUE CACHÉ (Reçoit la valeur exacte et exécute la validation anti-chevauchement)
                Forms\Components\Hidden::make('check_out')
                    ->dehydrated()
                    ->live()
                    ->rules([
                        function ($get, $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $roomId = $get('room_id');
                                $checkIn = $get('check_in');
                                $bookingId = $record?->id ?? $get('id');

                                // Double vérification si c'est un passage
                                if ($roomId && $checkIn && $value) {
                                    if (static::verifierOccupationChambre($roomId, $checkIn, $value, $bookingId)) {
                                        $fail(static::obtenirMessageErreurOccupation($roomId, $checkIn, $value, $bookingId));
                                    }
                                }
                            };
                        },
                    ]),

                Forms\Components\TextInput::make('total_price')
                    ->label('Prix Total')
                    ->numeric()
                    ->required()
                    ->prefix('FCFA')
                    ->dehydrated()
                    ->readOnly(),

                Forms\Components\Select::make('key_card_id')
                    ->label('Attribuer une Carte Magnétique')
                    ->relationship('keyCard', 'uid')
                    ->placeholder('Sélectionnez ou scannez une carte RFID')
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return \App\Models\KeyCard::where('status', 'active')
                            ->get()
                            ->mapWithKeys(function ($card) {
                                $texteAffichage = $card->label ? "{$card->uid} ({$card->label})" : $card->uid;
                                return [$card->id => $texteAffichage];
                            })->toArray();
                    }),
            ]);
    }


    public static function getValidationRules(): array
    {
        return [
            'room_id' => [
                'required',
                function (string $attribute, $value, \Closure $fail) {
                    // Extraction directe des données brutes envoyées par le navigateur
                    $data = request()->input('components.0.updates')
                        ?? request()->input('serverMemo.data')
                        ?? request()->all();

                    // Extraction de secours via Filament global state
                    $checkIn = data_get($data, 'check_in') ?? request()->input('check_in');
                    $checkOut = data_get($data, 'check_out') ?? request()->input('check_out');
                    $bookingId = request()->route('record'); // ID si édition

                    // Si les données en direct manquent, on intercepte via le container global
                    if ($value && $checkIn && $checkOut) {
                        if (static::verifierOccupationChambre($value, $checkIn, $checkOut, $bookingId)) {
                            $fail(static::obtenirMessageErreurOccupation($value, $checkIn, $checkOut, $bookingId));
                        }
                    }
                }
            ],
        ];
    }



    /**
     * Algorithme de vérification des chevauchements de dates/heures
     */
/**
 * Algorithme de vérification des chevauchements de dates/heures (Version sans colonne 'status')
 */
    /**
     * Algorithme de détection des chevauchements horaires (Minutes incluses)
     */
    public static function verifierOccupationChambre($roomId, $checkIn, $checkOut, $currentBookingId = null): bool
    {
        $checkInFormatted = Carbon::parse($checkIn)->toDateTimeString();
        $checkOutFormatted = Carbon::parse($checkOut)->toDateTimeString();

        $query = Booking::where('room_id', $roomId)
            ->where(function ($q) use ($checkInFormatted, $checkOutFormatted) {
                $q->where('check_in', '<', $checkOutFormatted)
                    ->where('check_out', '>', $checkInFormatted);
            });

        if ($currentBookingId) {
            $query->where('id', '!=', $currentBookingId);
        }

        return $query->exists();
    }

    /**
     * Génère un message d'erreur précis contenant les dates et heures du conflit
     */
    public static function obtenirMessageErreurOccupation($roomId, $checkIn, $checkOut, $currentBookingId = null): string
    {
        $checkInFormatted = Carbon::parse($checkIn)->toDateTimeString();
        $checkOutFormatted = Carbon::parse($checkOut)->toDateTimeString();

        $conflit = Booking::where('room_id', $roomId)
            ->where(function ($q) use ($checkInFormatted, $checkOutFormatted) {
                $q->where('check_in', '<', $checkOutFormatted)
                    ->where('check_out', '>', $checkInFormatted);
            });

        if ($currentBookingId) {
            $conflit->where('id', '!=', $currentBookingId);
        }

        $reservationExistante = $conflit->first();

        if ($reservationExistante) {
            $debutConflit = Carbon::parse($reservationExistante->check_in)->format('d/m/Y à H:i');
            $finConflit = Carbon::parse($reservationExistante->check_out)->format('d/m/Y à H:i');
            return "La chambre sélectionnée est déjà occupée du {$debutConflit} au {$finConflit}.";
        }

        return "La chambre sélectionnée est déjà occupée pour ce créneau horaire.";
    }




    /**
     * Alignement complet des calculs sur la structure de votre base de données
     */

 public static function calculerTarifDynamique($get, $set): void
    {
        $roomId = $get('room_id');
        $start = $get('check_in');

        if (!empty($roomId) && !empty($start)) {
            $room = Room::with('roomType')->find($roomId);
            $typeChambre = strtolower($room?->roomType?->name ?? '');
            $prixUnitaire = $room?->roomType?->base_price ?? 0;

            if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
                $heures = (int) ($get('nombre_heures') ?? 1);
                $set('check_out', $start);
                $set('total_price', $heures * $prixUnitaire);
            } else {
                $end = $get('check_out');
                if ($end) {
                    $debut = Carbon::make($start);
                    $fin = Carbon::make($end);
                    $jours = max(1, $debut->diffInDays($fin));
                    $set('total_price', $jours * $prixUnitaire);
                }
            }
        }
    }


    public static function calculerPrixTotal($get, $set): void
    {
        $checkIn = $get('check_in');
        $checkOut = $get('check_out');
        $roomId = $get('room_id');

        if ($checkIn && $checkOut && $roomId) {
            $debut = Carbon::parse($checkIn);
            $fin = Carbon::parse($checkOut);
            $nuits = $debut->diffInDays($fin);

            if ($nuits > 0) {
                // Récupère la chambre avec son type pour avoir le prix de base
                $chambre = Room::with('roomType')->find($roomId);
                $prixBase = $chambre?->roomType?->base_price ?? 0;

                // Assigne la valeur calculée au champ total_price
                $set('total_price', $nuits * $prixBase);
            } else {
                $set('total_price', 0);
            }
        }
    }
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('customer_name')->label('Client')->searchable(),
            Tables\Columns\TextColumn::make('room.number')->label('Chambre'),

            // INCLUSION : Colonne visuelle pour la carte magnétique d'accès RFID
            Tables\Columns\TextColumn::make('keyCard.uid')
                ->label('Carte d\'accès')
                ->placeholder('🚫 Aucune carte')
                ->badge()
                ->color(fn ($state) => $state ? 'info' : 'gray')
                ->searchable(),

            Tables\Columns\TextColumn::make('check_in')->label('Arrivée')->date(),
            Tables\Columns\TextColumn::make('check_out')->label('Départ')->date(),

            Tables\Columns\TextColumn::make('total_price')
                ->label('Total')
                ->sortable()
                ->money(fn ($record) => $record->room?->roomType?->currency ?? 'XOF'),

            // FIX : Utilisation du chemin absolu \App\Models\Payment
            Tables\Columns\TextColumn::make('total_paid')
                ->label('Déjà Payé')
                ->state(fn ($record) => \App\Models\Payment::getSommePayeePourReservation($record->id))
                ->money(fn ($record) => $record->room?->roomType?->currency ?? 'XOF')
                ->color('success'),

            // FIX RELLIKAT : Passage strict à 4 arguments pour la fonction number_format()
            Tables\Columns\TextColumn::make('balance_due')
                ->label('Reste à Payer')
                ->state(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return max(0, ($record->total_price ?? 0) - $dejaPaye);
                })
                ->money(fn ($record) => $record->room?->roomType?->currency ?? 'XOF')
                ->badge()
                ->color(fn ($state) => $state <= 0 ? 'success' : 'warning')
                // CORRECTION ICI : Ajout du 4ème argument (décimales, séparateur décimal, séparateur milliers)
                ->formatStateUsing(fn ($state) => $state <= 0 ? 'SOLDÉ' : number_format((float)$state, 0, '.', ' ') . ' XOF'),
        ])
        ->filters([])
        ->actions([
            EditAction::make(),

            // 🔥 COMPOSANT AJOUTÉ : Bouton de suppression individuelle sécurisé pour Filament v5
            \Filament\Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Annuler et Supprimer la réservation')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cette réservation ? La chambre associée sera de nouveau disponible.')
                ->modalSubmitActionLabel('Confirmer la suppression'),

            // Bouton d'encaissement direct et intelligent hôtel sécurisé
            \Filament\Actions\Action::make('passer_au_paiement')
                ->label(function ($record) {
                    $total = (float) ($record->total_price ?? 0);
                    if ($total <= 0) {
                        return 'Tarif non défini';
                    }

                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return ($total - $dejaPaye) <= 0 ? 'Soldé' : 'Passer au paiement';
                })
                ->icon('heroicon-o-banknotes')
                ->color(function ($record) {
                    $total = (float) ($record->total_price ?? 0);
                    if ($total <= 0) {
                        return 'gray';
                    }

                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return ($total - $dejaPaye) <= 0 ? 'gray' : 'success';
                })
                ->disabled(function ($record) {
                    $total = (float) ($record->total_price ?? 0);
                    if ($total <= 0) {
                        return true;
                    }

                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return ($total - $dejaPaye) <= 0;
                })
                ->form([
                    TextInput::make('receipt_number')
                        ->label('Numéro de Reçu')
                        ->default('REC-' . date('Ymd-His'))
                        ->required()
                        ->readOnly(),

                    TextInput::make('amount_to_pay')
                        ->label('Reste à payer')
                        ->numeric()
                        ->prefix('FCFA')
                        ->readOnly(),

                    TextInput::make('amount')
                        ->label('Montant à Encaisser')
                        ->numeric()
                        ->prefix('FCFA')
                        ->required()
                        ->hint('Modifiable si paiement partiel'),

                    // INTERFACE COMPLÈTE HARMONISÉE AVEC LES ÉMOJIS
                    Select::make('payment_method')
                        ->label('Mode de règlement')
                        ->options([
                            'cash'          => '💵 Espèces / Cash',
                            'wave'          => '🌊 Wave',
                            'orange_money'  => '🍊 Orange Money',
                            'mtn_momo'      => '💛 MTN Mobile Money',
                            'moov_money'    => '💙 Moov Money',
                            'card'          => '💳 Carte Bancaire',
                            'bank_transfer' => '🏦 Virement Bancaire',
                        ])
                        ->required()
                        ->default('cash'),
                ])
                ->mountUsing(function ($form, $record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    $reliquat = max(0, ($record->total_price ?? 0) - $dejaPaye);

                    $form->fill([
                        'receipt_number' => 'REC-' . date('Ymd-His'),
                        'amount_to_pay' => $reliquat,
                        'amount' => $reliquat,
                    ]);
                })
                ->action(function (array $data, $record, \Filament\Actions\Action $action): void {
                   $payment = \App\Models\Payment::create([
                        'receipt_number'    => $data['receipt_number'],
                        'event_booking_id'  => $record->id,
                        'amount'            => $data['amount'],
                        'payment_method'    => $data['payment_method'],
                        'payment_type'      => 'chambre',
                        'status'            => 'validé / encaissé',
                        'date_encaissement' => now(),
                    ]);

                    \App\Services\TelegramService::notifierAlerteEncaissment(
                        caisse: 'chambre',
                        client: $record->customer_name ?? 'Client Hôtel',
                        montant: (float) $data['amount'],
                        methode: $data['payment_method'],
                        numRecu: $data['receipt_number']
                    );

                    $url = route('payment.receipt.download', ['record' => $payment->id]);

                    \Filament\Notifications\Notification::make()
                        ->title('Paiement enregistré !')
                        ->actions([
                            \Filament\Actions\Action::make('imprimer')
                                ->label('🖨️ Imprimer le reçu')
                                ->color('success')
                                ->url($url)
                                ->openUrlInNewTab(),
                        ])
                        ->body("Le reçu {$data['receipt_number']} d'un montant de " . number_format($data['amount'], 0, ',', ' ') . " FCFA a été créé avec succès.")
                        ->success()
                        ->send();

                    $action->success();
                })
                ->requiresConfirmation()
                ->modalHeading('Créer un paiement direct'),
        ])
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
        ];
    }

    protected static function synchroniserDatesPassage($get, $set): void
    {
        $roomId = $get('room_id');
        $checkIn = $get('check_in');

        if (!$roomId || !$checkIn) return;

        $room = \App\Models\Room::with('roomType')->find($roomId);
        if (!$room) return;

        $type = strtolower($room->roomType?->name ?? '');

        if (str_contains($type, 'passage')) {
            $heures = (int) $get('nombre_heures', 1);

            // Calcul de Carbon (Gère parfaitement le basculement au jour d'après)
            $dateDepart = \Illuminate\Support\Carbon::parse($checkIn)->addHours($heures)->startOfMinute();

            // 1. On pousse la valeur brute en BDD via le champ caché (Lu par le validateur de chevauchement)
            $set('check_out', $dateDepart->format('Y-m-d H:i:s'));

            // 2. On pousse la valeur lisible pour le réceptionniste à l'écran
            $set('check_out_display', $dateDepart->format('d/m/Y à H:i'));
        } else {
            // Si on rebascule sur une chambre normale, on réinitialise à demain par défaut
            $set('check_out', \Carbon\Carbon::parse($checkIn)->addDay()->startOfMinute()->format('Y-m-d H:i:s'));
        }
    }

    // Fonction d'aide pour éviter la duplication de code dans votre fichier
    protected static function isPassageChambre($get): bool
    {
        $roomId = $get('room_id');
        if (!$roomId) return false;

        $room = \App\Models\Room::with('roomType')->find($roomId);
        $type = strtolower($room?->roomType?->name ?? '');

        return str_contains($type, 'passage') || str_contains($type, 'heure');
    }
}
