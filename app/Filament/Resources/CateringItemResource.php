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

                // 🔥 1. Assurez-vous que le champ "category" existant dispose de ->live() pour réveiller le formulaire
                Forms\Components\Select::make('category')
                    ->label('Catégorie')
                    ->options([
                        'plat' => '🍽️ Plat Cuisiné',
                        'boisson' => '🍹 Boisson / Bar',
                        'forfait_buffet' => '🏢 Forfait Buffet',
                    ])
                    ->required()
                    ->live(), // 🔥 OBLIGATOIRE pour rendre le formulaire réactif

                // 🔥 2. Ajoutez ensuite les deux champs de stock avec la restriction "boisson"
                Forms\Components\TextInput::make('stock_quantity')
                    ->label('Quantité en Stock Actuelle')
                    ->numeric()
                    ->default(0)
                    ->required()
                    // 🛡️ SÉCURITÉ : Visible UNIQUEMENT si la catégorie vaut 'boisson'
                    ->visible(fn ($get) => $get('category') === 'boisson'),

                Forms\Components\TextInput::make('alert_threshold')
                    ->label('Seuil d\'alerte critique')
                    ->numeric()
                    ->default(5)
                    ->required()
                    ->hint('Alerte si le stock descend sous ce nombre')
                    // 🛡️ SÉCURITÉ : Visible UNIQUEMENT si la catégorie vaut 'boisson'
                    ->visible(fn ($get) => $get('category') === 'boisson'),

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


                // À insérer dans ->columns([...]) de votre fonction table():
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock Réel')
                    ->sortable()
                    // 🔥 Si c'est un plat, on affiche un tiret, si c'est une boisson, on affiche le chiffre ou "RUPTURE"
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->category !== 'boisson') {
                            return '—';
                        }
                        return $state <= 0 ? '🚨 RUPTURE DE STOCK' : $state . ' bouteille(s)';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->category !== 'boisson') return 'gray';
                        if ($state <= 0) return 'danger';
                        if ($state <= $record->alert_threshold) return 'warning';
                        return 'success';
                    }),

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
