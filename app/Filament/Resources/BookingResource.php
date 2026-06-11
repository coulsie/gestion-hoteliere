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
                    // SÉCURITÉ : N'affiche dans la liste que les chambres propres et prêtes
                    modifyQueryUsing: fn ($query) => $query->where('housekeeping_status', 'propre')
                )
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, $set, $get) {
                    static::calculerTarifDynamique($get, $set);
                }),


            Forms\Components\DatePicker::make('check_in')
                ->label('Date d\'arrivée')
                ->default(now())
                ->required()
                ->live()
                ->afterStateUpdated(fn ($get, $set) => static::calculerTarifDynamique($get, $set)),

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
                    if (! $roomId) return false;
                    $room = \App\Models\Room::with('roomType')->find($roomId);
                    $type = strtolower($room?->roomType?->name ?? '');
                    return str_contains($type, 'passage') || str_contains($type, 'heure');
                })
                ->afterStateUpdated(function ($state, $get, $set) {
                    if ($get('check_in')) {
                        $set('check_out', $get('check_in'));
                    }
                    static::calculerTarifDynamique($get, $set);
                }),

            Forms\Components\DatePicker::make('check_out')
                ->label('Date de départ')
                ->default(now()->addDay())
                ->required()
                ->live()
                ->dehydrated()
                ->readOnly(function ($get) {
                    $roomId = $get('room_id');
                    if (! $roomId) return false;
                    $room = \App\Models\Room::with('roomType')->find($roomId);
                    $type = strtolower($room?->roomType?->name ?? '');
                    return str_contains($type, 'passage') || str_contains($type, 'heure');
                })
                ->afterStateUpdated(fn ($get, $set) => static::calculerTarifDynamique($get, $set)),

            Forms\Components\TextInput::make('total_price')
                ->label('Prix Total')
                ->numeric()
                ->required()
                ->prefix('FCFA')
                ->dehydrated()
                ->readOnly(function ($get) {
                    $roomId = $get('room_id');
                    if (! $roomId) return false;
                    $room = \App\Models\Room::with('roomType')->find($roomId);
                    $type = strtolower($room?->roomType?->name ?? '');
                    return str_contains($type, 'passage') || str_contains($type, 'heure');
                }),

            // AJOUT : Association de la carte magnétique d'accès (RFID)
            Forms\Components\Select::make('key_card_id')
                ->label('Attribuer une Carte Magnétique')
                ->relationship('keyCard', 'uid')
                ->placeholder('Sélectionnez ou scannez une carte RFID')
                ->searchable()
                ->preload()
                // Charge uniquement les cartes au statut 'active'
                ->options(function () {
                    return \App\Models\KeyCard::where('status', 'active')
                        ->get()
                        ->mapWithKeys(function ($card) {
                            $texteAffichage = $card->label ? "{$card->uid} ({$card->label})" : $card->uid;
                            return [$card->id => $texteAffichage];
                        });
                })
                ->helperText('Optionnel. Cliquez dans le champ puis passez la carte sur le lecteur USB pour la sélectionner automatiquement.')
                ->columnSpanFull(),
        ]);
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

            // Bouton d'encaissement direct et intelligent
            \Filament\Actions\Action::make('passer_au_paiement')
                ->label(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return (($record->total_price ?? 0) - $dejaPaye) <= 0 ? 'Soldé' : 'Passer au paiement';
                })
                ->icon('heroicon-o-banknotes')
                ->color(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return (($record->total_price ?? 0) - $dejaPaye) <= 0 ? 'gray' : 'success';
                })
                ->disabled(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourReservation($record->id);
                    return (($record->total_price ?? 0) - $dejaPaye) <= 0;
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

                    Select::make('payment_method')
                        ->label('Mode de règlement')
                        ->options([
                            'cash' => 'Espèces / Cash',
                            'card' => 'Carte Bancaire',
                            'mobile_money' => 'Mobile Money',
                            'bank_transfer' => 'Virement Bancaire',
                        ])
                        ->required(),
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
                    // 1. Enregistrement du paiement en Base de Données
                    $payment = \App\Models\Payment::create([
                        'receipt_number'    => $data['receipt_number'],
                        'event_booking_id'  => $record->id,
                        'amount'            => $data['amount'],
                        'payment_method'    => $data['payment_method'],
                        'status'            => 'validé / encaissé',
                        'date_encaissement' => now(),
                    ]);

                    $url = route('payments.receipt', ['payment' => $payment->id]);

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
                ->modalHeading('Créer un paiement direct')
                ->modalSubmitActionLabel('Valider l\'encaissement'),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                // L'Observer s'occupe automatiquement de libérer les cartes et salir les chambres
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
}
