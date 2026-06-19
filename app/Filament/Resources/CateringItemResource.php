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

class CateringItemResource extends Resource
{
    protected static ?string $model = CateringItem::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Services Restauration';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';
    protected static ?string $pluralModelLabel = 'Services Restauration';
    protected static ?string $modelLabel = 'Service Restauration';

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
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Désignation'),

                \Filament\Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'plat' => 'info',
                        'boisson' => 'success',
                        'forfait_buffet' => 'warning',
                        default => 'gray',
                    })
                    ->label('Catégorie'),

                \Filament\Tables\Columns\TextColumn::make('unit_price')
                    ->money('XOF')
                    ->label('Prix Unitaire'),
            ])
            // 🔥 CORRECTION : On ne garde strictement que l'Action native de modification des plats
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => CateringItemResource\Pages\ListCateringItems::route('/'),
        ];
    }
}
