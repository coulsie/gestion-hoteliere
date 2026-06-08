<?php

namespace App\Filament\Resources;

use App\Models\EventBooking;
use App\Models\EventSpace;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\EventBookingResource\Pages\ListEventBookings;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class EventBookingResource extends Resource
{
    protected static ?string $model = EventBooking::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('client_name')
                    ->required()
                    ->label('Nom du Client / Organisation'),

                Forms\Components\Select::make('event_space_id')
                    ->relationship('eventSpace', 'name')
                    ->live()
                    ->required()
                    ->label('Espace / Salle à allouer')
                    ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set)),

                Forms\Components\DateTimePicker::make('start_time')
                    ->live()
                    ->required()
                    ->label('Date & Heure de début')
                    ->native(false)
                    ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set)),

                Forms\Components\DateTimePicker::make('end_time')
                    ->live()
                    ->required()
                    ->label('Date & Heure de fin')
                    ->native(false)
                    ->afterStateUpdated(fn ($get, $set) => self::calculerPrixEvenement($get, $set))
                    // FIX INDISPENSABLE : Validation standard Laravel acceptée à 100% par Livewire et Filament v5
                    ->rules([
                        'after:start_time',
                    ]),

                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required()
                    ->dehydrated()
                    ->label('Montant de la Location'),
            ]);
    }

    public static function calculerPrixEvenement($get, $set): void
    {
        $start = $get('start_time');
        $end = $get('end_time');
        $spaceId = $get('event_space_id');

        if ($start && $end && $spaceId) {
            $debut = Carbon::parse($start);
            $fin = Carbon::parse($end);
            $heures = $debut->diffInHours($fin);

            if ($heures > 0) {
                $espace = EventSpace::find($spaceId);
                $tarifHoraire = $espace?->hourly_rate ?? 0;
                $set('total_amount', $heures * $tarifHoraire);
            } else {
                $set('total_amount', 0);
            }
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')->label('Organisateur')->searchable(),
                Tables\Columns\TextColumn::make('eventSpace.name')->label('Espace alloué'),
                Tables\Columns\TextColumn::make('start_time')->label('Début')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('end_time')->label('Fin')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('total_amount')->label('Recette')->money('XOF'),
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
            'index' => ListEventBookings::route('/'),
        ];
    }
}
