<?php

namespace App\Filament\Resources;

use App\Models\CateringItem;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class CateringItemResource extends Resource
{
    protected static ?string $model = CateringItem::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cake';

    // Signature native v5 avec Schema et components
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nom du plat / Forfait banquet'),

                Forms\Components\Select::make('category')
                    ->options([
                        'plat' => 'Plat / Menu Cuisine',
                        'boisson' => 'Boisson / Boisson Bar',
                        'forfait_buffet' => 'Forfait Buffet / Pause-Café',
                    ])
                    ->required()
                    ->label('Catégorie Restauration'),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required()
                    ->label('Prix Unitaire'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Désignation'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'plat' => 'info',
                        'boisson' => 'success',
                        'forfait_buffet' => 'warning',
                        default => 'gray',
                    })
                    ->label('Catégorie'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->money('XOF')
                    ->label('Prix Unitaire'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => CateringItemResource\Pages\ListCateringItems::route('/'),
        ];
    }


}
