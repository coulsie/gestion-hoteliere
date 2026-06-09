<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeyCardResource\Pages;
use App\Models\KeyCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class KeyCardResource extends Resource
{
    protected static ?string $model = KeyCard::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Cartes Magnétiques';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Hôtelière';
    protected static ?string $pluralModelLabel = 'Cartes Magnétiques';
    protected static ?string $modelLabel = 'Carte Magnétique';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uid')
                    ->label('Code Unique de la Carte (UID)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Cliquez dans ce champ et approchez la carte du lecteur USB pour remplir le code automatiquement.')
                    ->autofocus()
                    ->maxLength(255),

                TextInput::make('label')
                    ->label('Nom / Étiquette')
                    ->placeholder('Ex: Carte Chambre 105 ou Master 01')
                    ->maxLength(255),

                Select::make('status')
                    ->label('Statut de la carte')
                    ->options([
                        'active' => 'Active / Prête',
                        'lost' => 'Perdue',
                        'damaged' => 'Défectueuse',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uid')
                    ->label('Code UID (RFID)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Étiquette')
                    ->searchable()
                    ->placeholder('Sans étiquette'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'lost' => 'danger',
                        'damaged' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Enregistrée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListKeyCards::route('/'),
            'create' => Pages\CreateKeyCard::route('/create'),
            'edit' => Pages\EditKeyCard::route('/{record}/edit'),
        ];
    }
}
