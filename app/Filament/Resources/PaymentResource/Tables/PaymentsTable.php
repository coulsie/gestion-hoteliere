<?php

namespace App\Filament\Resources\PaymentResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('N° Reçu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'restauration' => 'warning',
                        'salle' => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'restauration' => '🍽️ RESTO',
                        'salle' => '🏢 SALLE',
                        default => '🏨 CHAMBRE',
                    }),

                TextColumn::make('amount')
                    ->label('Montant Encaissé')
                    ->money('XOF')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->badge()
                    ->textTransformToUpper(),

                TextColumn::make('paid_at')
                    ->label('Date d\'encaissement')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                // ❌ LE FILTRE DE CAISSE DANS L'ENTONNOIR A ÉTÉ SUPPRIMÉ POUR ÉVITER LES CONFLITS AVEC LES ONGLETS

                // ⏳ SEUL LE FILTRE TEMPORELEST CONSERVÉ (Il fonctionne parfaitement sur paid_at)
                Filter::make('periode_comptable')
                    ->label('Période de la Recette')
                    ->form([
                        Select::make('choix_temps')
                            ->label('Filtre Temporel')
                            ->options([
                                'tout' => '🌐 Afficher tout (Historique complet)',
                                'aujourdhui' => '📅 Aujourd\'hui (Recette Journalière)',
                                'semaine' => '📆 Cette semaine (7 derniers jours)',
                                'personnalise' => '🔧 Période personnalisée (Choisir dates)',
                            ])
                            ->default('tout')
                            ->selectablePlaceholder(false)
                            ->live(),

                        DatePicker::make('date_debut')
                            ->label('Date de Début')
                            ->visible(fn ($get) => $get('choix_temps') === 'personnalise'),

                        DatePicker::make('date_fin')
                            ->label('Date de Fin')
                            ->visible(fn ($get) => $get('choix_temps') === 'personnalise'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['choix_temps']) || $data['choix_temps'] === 'tout') {
                            return $query;
                        }

                        return $query->where(function ($subQuery) use ($data) {
                            $colonneDate = 'paid_at';

                            switch ($data['choix_temps']) {
                                case 'aujourdhui':
                                    return $subQuery->whereDate($colonneDate, Carbon::today());

                                case 'semaine':
                                    return $subQuery->whereDate($colonneDate, '>=', Carbon::today()->subDays(7));

                                case 'personnalise':
                                    if (!empty($data['date_debut'])) {
                                        $subQuery->whereDate($colonneDate, '>=', $data['date_debut']);
                                    }
                                    if (!empty($data['date_fin'])) {
                                        $subQuery->whereDate($colonneDate, '<=', $data['date_fin']);
                                    }
                                    return $subQuery;
                            }
                        });
                    })
            ])

            ->recordActions([
                EditAction::make(),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('success')
                    ->url(fn ($record): string => route('receipt.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
