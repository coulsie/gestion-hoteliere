<?php

namespace App\Filament\Resources\PaymentResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter; // 🔥 UTILISATION D'UN FILTRE DE REQUETE DE BASE
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
                // 🔥 LA SOLUTION ULTIME : On filtre sur le TEXTE REEL du numéro de reçu pour contourner le bug du groupe
                Filter::make('choix_de_caisse')
                    ->form([
                        Select::make('caisse')
                            ->label('Filtrer par Caisse')
                            ->options([
                                'chambre' => '🏨 Caisse Hébergement / Hôtel',
                                'restauration' => '🍽️ Caisse Restaurant & Comptoir',
                                'salle' => '🏢 Caisse Salle Événementielle',
                            ])
                            ->placeholder('Toutes les caisses mélangées')
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['caisse'])) {
                            return $query;
                        }

                        // On force la base de données à découper les lignes selon l'écriture de vos reçus
                        return match ($data['caisse']) {
                            // Le restaurant contient obligatoirement "REC-RESTO-"
                            'restauration' => $query->where('receipt_number', 'LIKE', '%RESTO%'),

                            // La salle contient obligatoirement "REC-SALLE-"
                            'salle' => $query->where('receipt_number', 'LIKE', '%SALLE%'),

                            // L'hébergement contient REC- mais ne contient NI resto NI salle
                            'chambre' => $query->where('receipt_number', 'LIKE', 'REC-%')
                                               ->where('receipt_number', 'NOT LIKE', '%RESTO%')
                                               ->where('receipt_number', 'NOT LIKE', '%SALLE%'),

                            default => $query,
                        };
                    }),

                // 2. LES FILTRES TEMPORELS OBLIGATOIRES (Pointent sur paid_at)
                Filter::make('periode_comptable')
                    ->label('Période de la Recette')
                    ->form([
                        Select::make('choix_temps')
                            ->label('Filtre Temporel')
                            ->options([
                                'tout' => '🌐 Afficher tout (Historique complet)',
                                'aujourdhui' => '📅 Aujourd\'hui (Recette Journalière)',
                                'semaine' => '📆 Cette semaine (7 derniers locations)',
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
