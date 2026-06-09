<?php

namespace App\Filament\Resources;

use App\Models\RoomType;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\RoomTypeResource\Pages\ListRoomTypes;
use App\Filament\Resources\RoomTypeResource\Pages\CreateRoomType;
use App\Filament\Resources\RoomTypeResource\Pages\EditRoomType;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static bool $shouldSkipAuthorization = true;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Types de Chambres';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';
    protected static ?string $pluralModelLabel = 'Types de Chambres';
    protected static ?string $modelLabel = 'Type de Chambre';

          public static function form(Schema $schema): Schema
    {
        return $schema
            // On définit que le formulaire entier se comporte comme une grille de 2 colonnes
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nom du type de chambre')
                    ->columnSpanFull(), // Prend toute la largeur de la ligne (2 colonnes)

                // Ces deux champs vont se placer automatiquement côte à côte sur la ligne suivante
                Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->required()
                    ->label('Prix de base par nuit'),

                Forms\Components\Select::make('currency')
                    ->label('Type de Franc / Devise')
                    ->options([
                        'XOF' => 'Franc CFA (BCEAO - Afrique de l\'Ouest)',
                        'XAF' => 'Franc CFA (BEAC - Afrique Centrale)',
                        'CHF' => 'Franc Suisse (Suisse)',
                        'EUR' => 'Euro (Europe)',
                    ])
                    ->default('XOF')
                    ->required()
                    ->searchable(),
            ]);
    }

        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),

                // Utilisation de la méthode native ->money() acceptant une fonction anonyme
                \Filament\Tables\Columns\TextColumn::make('base_price')
                    ->label('Prix de base')
                    ->sortable()
                    ->money(fn ($record) => $record->currency ?? 'XOF'),
            ])
            ->filters([])
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
            'index' => ListRoomTypes::route('/'),
        ];
    }
}
