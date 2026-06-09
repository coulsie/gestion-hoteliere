<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Comptabilité & Reçus';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Financière';

    protected static ?string $pluralModelLabel = 'Paiements & Recettes';

    protected static ?string $modelLabel = 'Paiement';

    // Formulaire de saisie d'un paiement (Création du reçu)
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_booking_id')
                    ->label('Réservation concernée')
                    // Correction : On utilise 'eventBooking' (le nom exact de la fonction dans votre modèle Payment)
                    ->relationship('eventBooking', 'id')
                    ->required()
                    ->searchable()
                    ->preload()
                    // Affiche une ligne claire contenant l'ID et le numéro de chambre pour le caissier
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Réservation N° {$record->id} - Chambre " . ($record->room?->number ?? 'N/A')),

                TextInput::make('receipt_number')
                    ->label('Numéro de Reçu')
                    ->default('REC-' . date('Ymd-His'))
                    ->required()
                    ->readOnly(),

                TextInput::make('amount')
                    ->label('Montant Encaissé')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required(),

                Select::make('payment_method')
                    ->label('Mode de règlement')
                    ->options([
                        'cash' => 'Espèces / Cash',
                        'card' => 'Carte Bancaire',
                        'mobile_money' => 'Mobile Money (Orange/MTN/Wave)',
                        'bank_transfer' => 'Virement Bancaire',
                    ])
                    ->required(),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'completed' => 'Validé / Encaissé',
                        'pending' => 'En attente',
                        'refunded' => 'Remboursé',
                    ])
                    ->default('completed')
                    ->required(),

                DateTimePicker::make('paid_at')
                    ->label('Date d\'encaissement')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Remarques / Notes')
                    ->columnSpanFull(),
            ]);
    }

    // Vue en Liste : Tableau de bord comptable (Journalier, Hebdo, Mensuel)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('N° Reçu')
                    ->searchable()
                    ->sortable(),

                // Alignement Filament v4 sur la clé étrangère directe
                TextColumn::make('event_booking_id')
                    ->label('Réf. Réservation')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label('Recette Totale Période')
                            ->money('XOF')
                    ),

                TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('État')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('paid_at')
                    ->label('Date Encaissé')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('paid_at_period')
                    ->label('Période Comptable')
                    ->form([
                        Select::make('period')
                            ->label('Filtrer par Fréquence')
                            ->options([
                                'today' => 'Comptabilité Journalière (Aujourd\'hui)',
                                'week' => 'Comptabilité Hebdomadaire (Cette semaine)',
                                'month' => 'Comptabilité Mensuelle (Ce mois-ci)',
                                'year' => 'Bilan Annuel (Cette année)',
                            ])
                            ->default('today'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['period'],
                            fn (Builder $query, $period): Builder => match ($period) {
                                'today' => $query->whereDate('paid_at', Carbon::today()),
                                'week' => $query->whereBetween('paid_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                                'month' => $query->whereMonth('paid_at', Carbon::now()->month)->whereYear('paid_at', Carbon::now()->year),
                                'year' => $query->whereYear('paid_at', Carbon::now()->year),
                                default => $query,
                            },
                        );
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),

                \Filament\Actions\Action::make('print_receipt')
                    ->label('Imprimer Reçu')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(fn ($record) => redirect()->route('payment.receipt.download', $record)),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
