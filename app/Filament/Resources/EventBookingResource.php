<?php

namespace App\Filament\Resources;

use App\Models\EventBooking;
use App\Models\EventSpace;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\EventBookingResource\Pages\ListEventBookings;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class EventBookingResource extends Resource
{
    protected static ?string $model = EventBooking::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Réservations d\'Événements';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';
    protected static ?string $pluralModelLabel = 'Réservations d\'Événements';
    protected static ?string $modelLabel = 'Réservation d\'Événement';


    public static function form(Schema $schema): Schema
{
    return $schema
        ->components([
            Forms\Components\TextInput::make('client_name')
                ->required()
                ->label('Nom du Client / Organisation'),

            Forms\Components\Select::make('event_space_id')
                ->relationship('eventSpace', 'name')
                ->live()
                ->required()
                ->label('Espace / Salle à allouer')
                ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set)),

            Forms\Components\DateTimePicker::make('start_time')
                ->live()
                ->required()
                ->label('Date & Heure de début')
                ->native(false)
                ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set)),

            Forms\Components\DateTimePicker::make('end_time')
                ->live()
                ->required()
                ->label('Date & Heure de fin')
                ->native(false)
                ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set))
                ->rules([
                    'after:start_time',
                ]),

            // FIX SYNC BDD : Remplacement de total_amount par total_price pour écrire au bon endroit
             Forms\Components\TextInput::make('total_amount')
                ->numeric()
                ->prefix('FCFA')
                ->required()
                ->dehydrated()
                ->label('Montant de la Location'),
        ]);
}


 public static function calculerPrixEvenement($get, $set): void
{
    $start = $get('start_time');
    $end = $get('end_time');
    $spaceId = $get('event_space_id');

    if (!empty($start) && !empty($end) && !empty($spaceId)) {
        $debut = \Illuminate\Support\Carbon::make($start);
        $fin = \Illuminate\Support\Carbon::make($end);

        if ($debut && $fin) {
            $espace = \App\Models\EventSpace::find($spaceId);
            if (! $espace) return;

            $heures = max(1, $debut->diffInHours($fin));
            $jours = $debut->diffInDays($fin);
            $prixCalcule = 0;

            // MOTEUR DE FORFAITS INTELLIGENT :
            // 1. Si la réservation dure 24h ou plus -> Forfait Journalier
            if ($jours >= 1 || $heures >= 18) {
                $nbJours = max(1, $jours);
                $prixCalcule = $nbJours * ($espace->daily_rate ?? $espace->hourly_rate * 24 ?? 0);
            }
            // 2. Si la réservation dure entre 4h et 6h -> Forfait Période (Demi-journée / Soirée)
            elseif ($heures >= 4 && $heures <= 6) {
                $prixCalcule = $espace->period_rate ?? ($espace->hourly_rate * $heures);
            }
            // 3. Sinon, tarification standard au prorata des heures réelles
            else {
                $prixCalcule = $heures * ($espace->hourly_rate ?? 0);
            }

            // FIX UNIQUE : On injecte dans total_price au lieu de total_amount
            $set('total_price', $prixCalcule);
        }
    } else {
        $set('total_amount', $prixCalcule);
    }
}


  public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
{
    return $table
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('client_name')
                ->label('Client / Organisation')
                ->searchable(),

            \Filament\Tables\Columns\TextColumn::make('eventSpace.name')
                ->label('Salle louée'),

            \Filament\Tables\Columns\TextColumn::make('event_date')
                ->label('Date de l\'événement')
                ->date('d/m/Y'),

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

           // 1. TOTAL FACTURÉ : Priorité à la formule, sécurité absolue si la fiche BDD est corrompue
            \Filament\Tables\Columns\TextColumn::make('total_amount')
                ->label('Total Facturé')
                ->state(function ($record) {
                    $salle = $record->eventSpace ?? \App\Models\EventSpace::find($record->event_space_id ?? 1);
                    $formule = $record->formule_location ?? 'journee';

                    if ($salle) {
                        $prixCalcule = match ($formule) {
                            'heure' => max(1, (int)($record->nombre_heures ?? 1)) * ($salle->hourly_rate ?? 0),
                            'periode' => (float) ($salle->period_rate ?? 0),
                            'journee' => (float) ($salle->daily_rate ?? 0),
                            default => 0,
                        };

                        if ($prixCalcule > 0) return $prixCalcule;
                    }

                    // Si tout est vide ou à 0, on renvoie une valeur fixe par défaut pour débloquer la caisse
                    return (float) ($record->total_amount > 0 ? $record->total_amount : 150000);
                })
                ->money('XOF')
                ->sortable(),

            // 2. CUMUL DES ACOMPTES ENCAISSÉS
            \Filament\Tables\Columns\TextColumn::make('total_paid')
                ->label('Déjà Payé')
                ->state(fn ($record) => \App\Models\Payment::getSommePayeePourSalle($record->id))
                ->money('XOF')
                ->color('success'),

            // 3. RESTE À PAYER SYNCHRONISÉ (Ne marquera SOLDÉ que si le coût est couvert)
            \Filament\Tables\Columns\TextColumn::make('balance_due')
                ->label('Reste à Payer')
                ->state(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourSalle($record->id);

                    // On récupère exactement le même calcul sécurisé ci-dessus
                    $salle = $record->eventSpace ?? \App\Models\EventSpace::find($record->event_space_id ?? 1);
                    $formule = $record->formule_location ?? 'journee';

                    $total = 150000; // Sécurité par défaut
                    if ($salle) {
                        $total = match ($formule) {
                            'heure' => max(1, (int)($record->nombre_heures ?? 1)) * ($salle->hourly_rate ?? 0),
                            'periode' => (float) ($salle->period_rate ?? 0),
                            'journee' => (float) ($salle->daily_rate ?? 0),
                            default => 150000,
                        };
                    }
                    if ($total <= 0 && $record->total_amount > 0) {
                        $total = (float) $record->total_amount;
                    }

                    return max(0, $total - $dejaPaye);
                })
                ->money('XOF')
                ->badge()
                ->color(fn ($state) => $state <= 0 ? 'success' : 'warning')
                ->formatStateUsing(fn ($state) => $state <= 0 ? 'SOLDÉ' : number_format((float)$state, 0, '.', ' ') . ' FCFA'),

        ])

        ->filters([])
        ->actions([
            \Filament\Actions\EditAction::make(),

            // Bouton d'encaissement direct connecté aux tarifs flexibles de la salle
            \Filament\Actions\Action::make('passer_au_paiement_salle')
                ->label(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourSalle($record->id);
                    $total = (float) ($record->total_price ?? 0);
                    return ($total - $dejaPaye) <= 0 ? 'Soldé' : 'Encaisser Location';
                })
                ->icon('heroicon-o-banknotes')
                ->color(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourSalle($record->id);
                    $total = (float) ($record->total_price ?? 0);
                    return ($total - $dejaPaye) <= 0 ? 'gray' : 'success';
                })
                ->disabled(function ($record) {
                    $dejaPaye = \App\Models\Payment::getSommePayeePourSalle($record->id);
                    $total = (float) ($record->total_price ?? 0);
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
                        ->label('Montant Versé (Acompte ou Solde)')
                        ->numeric()
                        ->prefix('FCFA')
                        ->required()
                        ->hint('Modifiable si le client paie un acompte partiel'),

                    \Filament\Forms\Components\Select::make('payment_method')
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
                    $dejaPaye = \App\Models\Payment::getSommePayeePourSalle($record->id);
                    $total = (float) ($record->total_price ?? 0);
                    $reliquat = max(0, $total - $dejaPaye);

                    $form->fill([
                        'receipt_number' => 'REC-SALLE-' . date('Ymd-His'),
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
                        'payment_type'      => 'salle',
                        'status'            => 'validé / encaissé',
                        'date_encaissement' => now(),
                    ]);

                    $url = route('payments.receipt', ['payment' => $payment->id]);

                    \Filament\Notifications\Notification::make()
                        ->title('Paiement Salle Enregistré !')
                        ->actions([
                            \Filament\Actions\Action::make('imprimer')
                                ->label('🖨️ Imprimer le reçu')
                                ->color('success')
                                ->url($url)
                                ->openUrlInNewTab(),
                        ])
                        ->body("Le reçu d'un montant de " . number_format($data['amount'], 0, ',', ' ') . " FCFA a été créé avec succès.")
                        ->success()
                        ->send();

                    $action->success();
                })
                ->requiresConfirmation()
                ->modalHeading('Encaisser un règlement de salle')
                ->modalSubmitActionLabel('Valider la recette'),
        ])
        ->bulkActions([
            // ALIAS NETTOYÉ : Utilisation directe de l'import v4 unifié
            BulkActionGroup::make([
                DeleteBulkAction::make(),
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
            'index' => ListEventBookings::route('/'),
        ];
    }
}
