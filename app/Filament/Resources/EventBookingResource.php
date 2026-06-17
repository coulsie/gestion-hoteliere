<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventBookingResource\Pages;
use App\Models\EventBooking;
use App\Models\EventSpace;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventBookingResource extends Resource
{
    protected static ?string $model = \App\Models\EventBooking::class;

    // Force Filament à inscrire le menu dans la barre de navigation
    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    // FIX DE TYPE EXACT : Remplacement de BackedEnum par UnitEnum pour stopper le crash
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';

    protected static ?string $navigationLabel = 'Réservations d\'Événements';
    protected static ?string $pluralModelLabel = 'Réservations d\'Événements';
    protected static ?string $modelLabel = 'Réservation Salle';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('client_name')
                    ->label('Client / Organisation')
                    ->required(),

                Forms\Components\Select::make('event_space_id')
                    ->label('Sélectionner la Salle')
                    ->options(EventSpace::all()->pluck('name', 'id'))
                    ->required()
                    ->live(),

                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Date & Heure de Début')
                    ->required()
                    ->live(),

                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Date & Heure de Fin')
                    ->required()
                    ->live(),

                Forms\Components\Select::make('formule_location')
                    ->label('Formule de Location')
                    ->options([
                        'journee' => '📅 Forfait Journée complète',
                        'heure' => '⏳ Tarif Horaire spécifique',
                    ])
                    ->default('journee')
                    ->required()
                    ->live(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('client_name')
                    ->label('Client / Organisation')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('eventSpace.name')
                    ->label('Salle louée'),

                \Filament\Tables\Columns\TextColumn::make('start_time')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i'),

                \Filament\Tables\Columns\TextColumn::make('formule_location')
                    ->label('Formule')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'heure' => '⏳ À l\'heure',
                        'periode' => '🌅 Période',
                        'journee' => '📅 Journée',
                        default => $state
                    }),

                // 1. TOTAL FACTURÉ (Calculateur forfaitaire ou BDD)
                \Filament\Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Facturé')
                    ->state(function ($record) {
                        $montantBdd = (float) ($record->total_amount ?? 0);
                        if ($montantBdd <= 0 && $record->eventSpace) {
                            $salle = $record->eventSpace;
                            $debut = \Illuminate\Support\Carbon::make($record->start_time);
                            $fin = \Illuminate\Support\Carbon::make($record->end_time);
                            $nbJours = $debut && $fin ? max(1, $debut->diffInDays($fin)) : 1;

                            return $nbJours * (float)($salle->daily_rate ?? 0);
                        }
                        return $montantBdd;
                    })
                    ->money('XOF'),

                // 2. CUMUL DÉJÀ PAYÉ STRICTEMENT CLOISONNÉ PAR ID
                            // 2. CUMUL DÉJÀ PAYÉ EN DIRECT CLOISONNÉ (AVEC PARENTHÈSES DE SÉCURITÉ SQL)
            \Filament\Tables\Columns\TextColumn::make('total_paid')
                ->label('Déjà Payé')
                ->state(0)
                ->badge()
                ->color('success')
                ->formatStateUsing(function ($record) {
                    // Les parenthèses forcent SQL à chercher uniquement CET événement ET (salle OU préfixe)
                    $cumul = (float) \Illuminate\Support\Facades\DB::table('payments')
                        ->where('event_booking_id', $record->id)
                        ->where(function ($query) {
                            $query->where('payment_type', 'salle')
                                  ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%');
                        })
                        ->sum('amount');

                    return number_format($cumul, 0, '.', ' ') . ' F CFA';
                }),

            // 3. RESTE À PAYER MATHÉMATIQUE PARFAIT
            \Filament\Tables\Columns\TextColumn::make('balance_due')
                ->label('Reste à Payer')
                ->state(0)
                ->badge()
                ->color(function ($record) {
                    $total = (float) ($record->total_amount ?? 0);
                    if ($total <= 0) return 'gray';

                    $cumul = (float) \Illuminate\Support\Facades\DB::table('payments')
                        ->where('event_booking_id', $record->id)
                        ->where(function ($query) {
                            $query->where('payment_type', 'salle')
                                  ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%');
                        })
                        ->sum('amount');

                    return ($total - $cumul) <= 0 ? 'success' : 'warning';
                })
                ->formatStateUsing(function ($record) {
                    $total = (float) ($record->total_amount ?? 0);
                    if ($total <= 0) return 'En attente de tarif';

                    $cumul = (float) \Illuminate\Support\Facades\DB::table('payments')
                        ->where('event_booking_id', $record->id)
                        ->where(function ($query) {
                            $query->where('payment_type', 'salle')
                                  ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%');
                        })
                        ->sum('amount');

                    $reste = max(0, $total - $cumul);

                    return $reste <= 0 ? 'SOLDÉ' : number_format($reste, 0, '.', ' ') . ' FCFA';
                }),

            ])
            ->actions([
                \Filament\Actions\EditAction::make(),

                // ENCAISSEMENT DES TRANCHES DE SALLES (INTELLIGENT)
                \Filament\Actions\Action::make('passer_au_paiement_salle')
                    ->label(function ($record) {
                        $total = (float) ($record->total_amount ?? 0);
                        if ($total <= 0) return 'Tarif non défini';
                        $dejaPaye = (float) \Illuminate\Support\Facades\DB::table('payments')->where('event_booking_id', $record->id)->sum('amount');
                        return ($total - $dejaPaye) <= 0 ? 'Soldé' : 'Encaisser Location';
                    })
                    ->icon('heroicon-o-banknotes')
                    ->color(fn($record) => (float)($record->total_amount ?? 0) <= 0 ? 'gray' : 'success')
                    ->disabled(function ($record) {
                        $total = (float) ($record->total_amount ?? 0);
                        if ($total <= 0) return true;
                        $dejaPaye = (float) \Illuminate\Support\Facades\DB::table('payments')->where('event_booking_id', $record->id)->sum('amount');
                        return ($total - $dejaPaye) <= 0;
                    })
                    ->form([
                        \Filament\Forms\Components\TextInput::make('receipt_number')
                            ->label('Numéro de Reçu')
                            ->default('REC-SALLE-' . date('Ymd-His'))
                            ->required()
                            ->readOnly(),
                        \Filament\Forms\Components\TextInput::make('amount_to_pay')
                            ->label('Reste à recouvrer sur la salle')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly(),
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Montant à Encaisser Actuellement')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),
                        \Filament\Forms\Components\Select::make('payment_method')
                            ->label('Mode de règlement')
                            ->options([
                                'cash' => '💵 Espèces / Cash',
                                'wave' => '🌊 Wave',
                                'orange_money' => '🍊 Orange Money',
                                'mtn_momo' => '💛 MTN Mobile Money',
                                'moov_money' => '💙 Moov Money',
                                'card' => '💳 Carte Bancaire',
                            ])
                            ->required()
                            ->default('cash'),
                    ])
                            ->mountUsing(function ($form, $record) {
                                $dejaPaye = (float) \Illuminate\Support\Facades\DB::table('payments')
                                    ->where('event_booking_id', $record->id)
                                    ->where(function ($query) {
                                        $query->where('payment_type', 'salle')
                                            ->orWhere('receipt_number', 'LIKE', 'REC-SALLE-%');
                                    })
                                    ->sum('amount');

                                $total = (float) ($record->total_amount ?? 0);
                                $reliquat = max(0, $total - $dejaPaye);

                                $form->fill([
                                    'receipt_number' => 'REC-SALLE-' . date('Ymd-His'),
                                    'amount_to_pay' => $reliquat,
                                    'amount' => $reliquat,
                                ]);
                            })

                                   ->action(function (array $data, $record, \Filament\Actions\Action $action): void {
                    // 1. Enregistrement comptable de la tranche de paiement
                    $payment = \App\Models\Payment::create([
                        'receipt_number'    => $data['receipt_number'],
                        'event_booking_id'  => $record->id,
                        'amount'            => $data['amount'],
                        'payment_method'    => $data['payment_method'],
                        'payment_type'      => 'salle',
                        'status'            => 'validé / encaissé',
                        'date_encaissement' => now(),
                    ]);

                    // 🔥 ALERTE INSTANTANÉE PROPRIÉTAIRE (SALLES)
                    \App\Services\TelegramService::notifierAlerteEncaissment(
                        caisse: 'salle',
                        client: $record->client_name ?? 'Organisation',
                        montant: (float) $data['amount'],
                        methode: $data['payment_method'],
                        numRecu: $data['receipt_number']
                    );

                  
                    // 2. Lien d'impression du reçu PDF
                    $url = route('payment.receipt.download', ['record' => $payment->id]);

                    // 3. Déclenchement de la notification avec impression instantanée
                    \Filament\Notifications\Notification::make()
                        ->title('Règlement enregistré avec succès !')
                        ->actions([
                            \Filament\Actions\Action::make('imprimer')
                                ->label('🖨️ Imprimer ce reçu')
                                ->color('success')
                                ->url($url)
                                ->openUrlInNewTab()
                        ])
                        ->body("Le versement de " . number_format($data['amount'], 0, ',', ' ') . " FCFA a été validé.")
                        ->success()
                        ->send();

                    $action->success();
                })
                ->requiresConfirmation()
                ->modalHeading('Encaisser un règlement de salle')
                ->modalSubmitActionLabel('Valider la recette'),
        ])
        // ACTIONS DE MASSE UNIFIÉES SANS DOSSIER TABLES
               // ACTIONS DE MASSE UNIFIÉES SANS DOSSIER TABLES
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
} // <-- Ferme la méthode table()

    /**
     * FIX REAPPARITION MENU : Déclare les pages obligatoires pour que
     * Filament accepte d'afficher le bouton dans le menu latéral.
     */
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\EventBookingResource\Pages\ListEventBookings::route('/'),
            'create' => \App\Filament\Resources\EventBookingResource\Pages\CreateEventBooking::route('/create'),
            'edit' => \App\Filament\Resources\EventBookingResource\Pages\EditEventBooking::route('/{record}/edit'),
        ];
    }



} // <-- Ferme la classe EventBookingResource
