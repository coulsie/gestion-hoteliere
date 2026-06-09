<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventSpaces\Pages\CreateEventSpace;
use App\Filament\Resources\EventSpaces\Pages\EditEventSpace;
use App\Filament\Resources\EventSpaces\Pages\ListEventSpaces;
use App\Filament\Resources\EventSpaces\Tables\EventSpacesTable;
use App\Models\EventSpace;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventSpaceResource extends Resource
{
    protected static ?string $model = EventSpace::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    // Propriétés de traduction pour le menu et l'interface en français
    protected static ?string $navigationLabel = 'Espaces Événementiels';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';

    protected static ?string $pluralModelLabel = 'Espaces Événementiels';

    protected static ?string $modelLabel = 'Espace Événementiel';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Nom de l\'espace') // Libellé du champ traduit
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return EventSpacesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventSpaces::route('/'),
            'create' => CreateEventSpace::route('/create'),
            'edit' => EditEventSpace::route('/{record}/edit'),
        ];
    }
}
