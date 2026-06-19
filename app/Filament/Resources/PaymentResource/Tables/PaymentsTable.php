<?php

namespace App\Filament\Resources\PaymentResource\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            // Les actions individuelles sur chaque ligne
            ->recordActions([
                EditAction::make(),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('success')
                    ->url(fn ($record): string => route('receipt.print', $record))
                    ->openUrlInNewTab(),
            ])
            // 🔥 CORRECTION : Les boutons globaux du haut se déclarent dans headerActions
            
            // Les actions lorsque l'on coche plusieurs lignes
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
