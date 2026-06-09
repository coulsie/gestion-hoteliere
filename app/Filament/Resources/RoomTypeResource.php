<?php

namespace App\Filament\Resources; // <-- Doit être exactement ceci

use App\Models\RoomType;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
// Ciblage exact des pages après renommage du dossier
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

    protected static bool $shouldSkipAuthorization = true; // <-- AJOUTEZ CETTE LIGNE

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Types de Chambres';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';
    protected static ?string $pluralModelLabel = 'Types de Chambres';
    protected static ?string $modelLabel = 'Type de Chambre';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nom du type de chambre'),

                Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->prefix('€')
                    ->required()
                    ->label('Prix de base par nuit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('base_price')->label('Prix de base')->money('EUR'),
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
        'index' => RoomTypeResource\Pages\ListRoomTypes::route('/'),
    ];
}



}
