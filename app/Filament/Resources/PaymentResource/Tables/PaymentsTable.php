<?php

namespace App\Filament\Resources\PaymentResource\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action; // 🔥 IMPORTATION FILAMENT v5 GLOBALE
use Filament\Actions\EditAction; // 🔥 IMPORTATION FILAMENT v5 GLOBALE
use Filament\Actions\BulkActionGroup; // 🔥 IMPORTATION FILAMENT v5 GLOBALE
use Filament\Actions\DeleteBulkAction; // 🔥 IMPORTATION FILAMENT v5 GLOBALE
use Filament\Support\Icons\Heroicon;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Vos colonnes existantes...
            ])
            ->filters([
                // Vos filtres existants...
            ])
            // 🔥 CONFIGURATION V5 : Les actions individuelles se déclarent dans recordActions
            ->recordActions([
                EditAction::make(),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon(Heroicon::OutlinedPrinter) // Image colorée v5
                    ->color('success') // Vert hôtelier
                    ->url(fn ($record): string => route('receipt.print', $record))
                    ->openUrlInNewTab(),
            ])
            // 🔥 CONFIGURATION V5 : Les actions de groupe se déclarent dans toolbarActions
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
