<?php

namespace App\Filament\Resources;

use App\Models\Room;
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

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static bool $shouldSkipAuthorization = true;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Chambres';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';
    protected static ?string $pluralModelLabel = 'Chambres';
    protected static ?string $modelLabel = 'Chambre';

    // Formulaire de saisie sécurisé contre les doublons
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->label('Numéro de chambre')
                    // Ajout de la sécurité d'unicité pour bloquer les doublons (ex: chambre 31)
                    ->unique(table: 'rooms', column: 'number', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Ce numéro de chambre est déjà attribué à un autre hébergement.',
                    ]),

                Forms\Components\Select::make('room_type_id')
                    ->relationship('roomType', 'name')
                    ->required()
                    ->label('Type de chambre'),

                Forms\Components\Select::make('status')
                    ->options([
                        'disponible' => 'Disponible',
                        'occupee' => 'Occupée',
                        'menage' => 'En cours de ménage',
                    ])
                    ->default('disponible')
                    ->required()
                    ->label('Statut'),
            ]);
    }

    // Tableau d'affichage avec badges de couleur
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->sortable()
                    ->searchable()
                    ->label('Chambre N°'),

                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Type'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'occupee' => 'danger',
                        'menage' => 'warning',
                        default => 'gray',
                    })
                    ->label('Statut'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'disponible' => 'Disponible',
                        'occupee' => 'Occupée',
                        'menage' => 'En ménage',
                    ])
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
            'index' => RoomResource\Pages\ListRooms::route('/'),
        ];
    }
}
