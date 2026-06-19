<?php

namespace App\Filament\Resources\CateringItemResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction; // 🔥 IMPORTATION UNIFIÉE DES ACTIONS GLOBALES V5
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class CateringItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Conserve vos colonnes visuelles actuelles qui sont parfaites
            ->columns([
                TextColumn::make('name')
                    ->label('Désignation')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge(),

                TextColumn::make('price')
                    ->label('Prix Unitaire')
                    ->money('XOF')
                    ->sortable(),
            ])
            ->filters([
                // Vos filtres de carte si nécessaires
            ])
            // 🔥 RETRAIT DE L'INTRUS : On écrase l'affichage en ne déclarant QUE le bouton de modification
            ->actions([
                EditAction::make(),
            ])
            // Sécurité de clic sur la ligne entière
            ->recordActions([
                EditAction::make(),
            ])
            // Suppression groupée dans la barre d'outils
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
