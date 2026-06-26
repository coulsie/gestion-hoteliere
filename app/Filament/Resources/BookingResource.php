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

                // 🔥 FORCE l'affichage personnalisé : Numéro + Nom du type de chambre
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Room $record) => "{$record->number} ({$record->roomType?->name})")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_price', 0); // Nettoyage initial
                        static::synchroniserDatesPassage($get, $set);
                        static::forcerHeureSortieMidi($get, $set);
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


                // Champ informatif : Prix Unitaire (insérer sous le Select de la chambre)
                Forms\Components\TextInput::make('prix_unitaire_info')
                    ->label('Prix de base / Nuitée')
                    ->numeric()
                    ->prefix('FCFA')
                    ->readOnly() // Empêche la modification manuelle
                    ->dehydrated(false), // Ne s'enregistre pas en base de données (champ purement visuel)



                    // 1. COMPOSANT DATE D'ARRIVÉE (INCHANGÉ)
                Forms\Components\DateTimePicker::make('check_in')
                    ->label('Date & Heure d\'arrivée')
                    ->default(now()->startOfMinute())
                    ->required()

                    ->live(onBlur: true) // 🔥 Indispensable pour envoyer la date au serveur
                    ->seconds(false)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::synchroniserDatesPassage($get, $set);
                        static::forcerHeureSortieMidi($get, $set); // 🔥 CORRECTION
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

                // 2. DURÉE HORAIRE (Affiché uniquement pour la caisse Passage)
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

                // 3. DATE & HEURE DE SORTIE (Pour les nuitées classiques)
               Forms\Components\DateTimePicker::make('check_out')
                    ->label('Date & Heure de Sortie')
                    ->default(now()->addDay()->startOfMinute())
                    ->required()
                    ->live(onBlur: true)
                    ->seconds(false)

                    // 🔥 LES DEUX LIGNES DE LA VICTOIRE :
                    // On ne le cache plus, on le désactive pour l'utilisateur, mais on FORCE l'envoi SQL
                    ->disabled(function ($get) {
                        $roomId = $get('room_id');
                        if (!$roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        return str_contains(strtolower($room?->roomType?->name ?? ''), 'passage');
                    })
                    ->dehydrated(true) // Force l'envoi vers MariaDB même si le champ est grisé/disabled

                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::forcerHeureSortieMidi($get, $set);
                        static::calculerTarifDynamique($get, $set);
                    })
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $checkIn = $get('check_in');
                                if ($checkIn && $value && \Carbon\Carbon::parse($value)->isBefore(\Carbon\Carbon::parse($checkIn))) {
                                    $fail("La date et heure de sortie doivent être supérieures à l'arrivée.");
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



                // 5. ZONE DU TARIF FINAL SÉCURISÉ (Lecture seule totale)
                Forms\Components\TextInput::make('total_price')
                    ->label('Montant Total Facturé')
                    ->numeric()
                    ->prefix('FCFA')
                    ->readOnly()
                    ->required()
                    ->columnSpanFull()
            ]);
    }

    /**
     * 🛡️ COMPOSANT DE VERROUILLAGE : RE-ECRIT L'HEURE DE SORTIE À 12H00 PILE
     */
    protected static function forcerHeureSortieMidi($get, $set): void
    {
        $roomId = $get('room_id');
        if (!$roomId) return;

        $room = \App\Models\Room::with('roomType')->find($roomId);
        $isPassage = str_contains(strtolower($room?->roomType?->name ?? ''), 'passage');

        // Si ce n'est pas un passage (chambre normale, suite...), on écrase l'heure de sortie par 12h00
        if (!$isPassage && $get('check_out')) {
            $dateBrute = \Illuminate\Support\Carbon::parse($get('check_out'))->format('Y-m-d');
            $set('check_out', $dateBrute . ' 12:00:00'); // Bloque la sortie à midi pile
        }
    }

    /**
     * 📊 ALGORITHME COMPTABLE AUX JOURS PURS
     */
protected static function calculerTarifDynamique($get, $set): void
{
    $roomId = $get('room_id');

    if (!$roomId) {
        $set('total_price', 0);
        $set('prix_unitaire_info', 0); // Remise à zéro
        return;
    }

    $checkIn = $get('check_in') ?? now()->toDateTimeString();
    $checkOut = $get('check_out') ?? now()->addDay()->setHour(12)->setMinute(0)->toDateTimeString();

    $room = \App\Models\Room::with('roomType')->find($roomId);
    if (!$room || !$room->roomType) {
        return;
    }

    $prixBase = (float) $room->roomType->base_price;

    // 🔥 ACTION : On affiche instantanément le prix unitaire dans le nouveau champ info
    $set('prix_unitaire_info', $prixBase);

    $nomTypeChambre = strtolower($room->roomType->name ?? '');
    $isPassage = str_contains($nomTypeChambre, 'passage');

    // ====================================================================
    // CAS 1 — SÉJOUR CLASSIQUE (NUITÉES)
    // ====================================================================
    if (!$isPassage) {
        $dateArrivee = \Illuminate\Support\Carbon::parse($checkIn)->floorDay();
        $dateSortie = \Illuminate\Support\Carbon::parse($checkOut)->floorDay();

        $nbNuitees = (int) $dateArrivee->diffInDays($dateSortie);

        if ($nbNuitees <= 0) {
            $nbNuitees = 1;
        }

        $set('total_price', $nbNuitees * $prixBase);
    }
    // ====================================================================
    // CAS 2 — PASSAGE HORAIRE
    // ====================================================================
    else {
        $heures = (int) ($get('nombre_heures') ?? 1);
        $set('total_price', $heures * $prixBase);
    }
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

    $isPassage = str_contains(strtolower($room->roomType->name ?? ''), 'passage');

    if ($isPassage) {
        // 1. Récupération brute de l'option (ex: "3 Heures de passage" ou 3)
        $valeurHeures = $get('nombre_heures') ?? 1;

        // 2. Extraction du premier nombre trouvé dans le texte s'il s'agit d'une chaîne
        preg_match('/\d+/', (string)$valeurHeures, $matches);
        $heuresAPasser = isset($matches[0]) ? (int)$matches[0] : 1;

        // 3. Calcul de la date de sortie exacte : Arrivée + X heures
        $dateSortieCalculee = \Illuminate\Support\Carbon::parse($checkIn)
            ->addHours($heuresAPasser)
            ->toDateTimeString();

        // 4. On injecte la date de sortie calculée directement dans le SEUL champ officiel
        $set('check_out', $dateSortieCalculee);
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

    /**
     * 🧠 CALCULATEUR AUTOMATIQUE ET ÉTANCHE DE LA TARIFICATION HÔTELIÈRE
     */
 public static function calculerPrixTotal($set, $get): void
{
    $roomId = $get('room_id');
    $checkIn = $get('check_in');
    $checkOut = $get('check_out');
    $typeSejour = $get('type_sejour') ?? 'sejour';

    if (!$roomId || !$checkIn || !$checkOut) {
        $set('total_price', 0);
        return;
    }

    // Chargement de la chambre avec sa relation de type de chambre
    $chambre = Room::with('roomType')->find($roomId);
    if (!$chambre) return;

    // Détermination dynamique du prix (vérifie 'price' ou 'prix' sur le type ou la chambre directement)
    $prixUnitaire = (float) (
        $chambre->roomType->price ??
        $chambre->roomType->prix ??
        $chambre->price ??
        $chambre->prix ??
        25000
    );

    // Extraction stricte des dates pour éliminer les décalages horaires
    $dateArrivee = \Carbon\Carbon::parse($checkIn)->startOfDay();
    $dateSortie = \Carbon\Carbon::parse($checkOut)->startOfDay();
    $nbJours = (int) $dateArrivee->diffInDays($dateSortie);

    if ($nbJours <= 0) {
        $nbJours = 1;
    }

    // 🛡️ Cas 1 : Séjour classique (Nuitée)
    if ($typeSejour === 'sejour') {
        $set('total_price', $nbJours * $prixUnitaire);
    }
    // ⏳ Cas 2 : Heures de passage
    else {
        $debut = \Carbon\Carbon::parse($checkIn);
        $fin = \Carbon\Carbon::parse($checkOut);
        $nbHeures = $debut->diffInHours($fin);

        if ($nbHeures <= 0) $nbHeures = 1;

        $prixHoraire = $prixUnitaire / 4;
        $set('total_price', round($nbHeures * $prixHoraire));
    }
}



}

