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


public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
{
    return $schema
        ->columns(2)
        ->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Nom de l\'espace / Salle')
                ->required()
                ->placeholder('Ex: Salle Katanna 250 places')
                ->columnSpanFull(),

            \Filament\Forms\Components\Select::make('type')
                ->label('Type d\'espace')
                ->options([
                    'salle_reunion' => 'Salle de Réunion',
                    'salle_fete' => 'Salle de Fête / Banquet',
                    'conference' => 'Salle de Conférence',
                    'esplanade' => 'Esplanade / Plein air',
                ])
                ->required()
                ->default('conference'),

            \Filament\Forms\Components\TextInput::make('capacity')
                ->label('Capacité d\'accueil (Personnes)')
                ->numeric()
                ->integer()
                ->required()
                ->placeholder('Ex: 250')
                ->prefix('👤'),

            // FIX DEFINTIF : Alignement sur le nom réel de votre colonne 'hourly_rate'
            \Filament\Forms\Components\TextInput::make('hourly_rate')
                ->label('Tarif Horaire de Location')
                ->numeric()
                ->required()
                ->default(0)
                ->prefix('FCFA')
                ->placeholder('Ex: 50000'),
        ]);
}


  public static function table(Table $table): Table
{
    return $table
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('name')
                ->label('Nom de l\'espace')
                ->searchable()
                ->sortable(),

            \Filament\Tables\Columns\TextColumn::make('type')
                ->label('Type d\'espace')
                ->badge()
                ->color('info')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'salle_reunion' => 'Salle de Réunion',
                    'salle_fete' => 'Salle de Fête / Banquet',
                    'conference' => 'Salle de Conférence',
                    'esplanade' => 'Esplanade / Plein air',
                    default => $state,
                }),

            \Filament\Tables\Columns\TextColumn::make('capacity')
                ->label('Capacité (Personnes)')
                ->numeric()
                ->sortable(),

            // FIX DEFINITIF : Intégration directe de la vraie colonne hourly_rate
            \Filament\Tables\Columns\TextColumn::make('hourly_rate')
                ->label('Tarif Horaire')
                ->money('XOF')
                ->sortable(),
        ])
        ->filters([
            \Filament\Tables\Filters\SelectFilter::make('type')
                ->label('Filtrer par type')
                ->options([
                    'salle_reunion' => 'Salle de Réunion',
                    'salle_fete' => 'Salle de Fête / Banquet',
                    'conference' => 'Salle de Conférence',
                    'esplanade' => 'Esplanade / Plein air',
                ]),
        ])
               ->actions([
            // FIX v4 : Chemin absolu vers l'action de modification globale
            \Filament\Actions\EditAction::make(),
        ])
        ->bulkActions([
            // FIX v4 : Chemins absolus vers les actions de groupe unifiées
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
            'index' => ListEventSpaces::route('/'),
            'create' => CreateEventSpace::route('/create'),
            'edit' => EditEventSpace::route('/{record}/edit'),
        ];
    }
}
