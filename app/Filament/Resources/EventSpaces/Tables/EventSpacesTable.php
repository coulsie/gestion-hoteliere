<?php

namespace App\Filament\Resources\EventSpaces\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table; // Retour à l'import original

class EventSpacesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Vos colonnes ici (ex: \Filament\Tables\Columns\TextColumn::make('name'))
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
