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
                ->unique(table: 'rooms', column: 'number', ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'Ce numéro de chambre est déjà attribué à un autre hébergement.',
                ]),

            Forms\Components\Select::make('room_type_id')
                ->relationship('roomType', 'name')
                ->required()
                ->label('Type de chambre'),

            // SÉCURITÉ UNIFIÉE : Utilisation du champ officiel lié à vos automatisations de ménage
            Forms\Components\Select::make('housekeeping_status')
                ->label('État du ménage / Maintenance')
                ->options([
                    'propre' => '🧼 Propre & Prête',
                    'sale' => '🍂 Sale (À nettoyer)',
                    'en_cours' => '🧹 Ménage en cours',
                    'maintenance' => '🛠️ En Maintenance',
                ])
                ->required()
                ->default('propre'),
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

            // SÉCURITÉ UNIFIÉE : Nouvelle colonne d'état avec couleur en badge
            Tables\Columns\TextColumn::make('housekeeping_status')
                ->label('État de la Chambre')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'propre' => 'success',
                    'sale' => 'danger',
                    'en_cours' => 'warning',
                    'maintenance' => 'gray',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'propre' => '🧼 PROPRE',
                    'sale' => '🍂 SALE',
                    'en_cours' => '🧹 EN COURS',
                    'maintenance' => '🛠️ MAINTENANCE',
                    default => $state,
                }),
        ])
        ->filters([
            // NOUVEAU FILTRE : Permet de voir instantanément uniquement les chambres sales à nettoyer
            Tables\Filters\SelectFilter::make('housekeeping_status')
                ->label('Filtrer par État')
                ->options([
                    'propre' => '🧼 Propre & Prête',
                    'sale' => '🍂 Sale (À nettoyer)',
                    'en_cours' => '🧹 Ménage en cours',
                    'maintenance' => '🛠️ En Maintenance',
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
