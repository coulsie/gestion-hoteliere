<?php

namespace App\Filament\Resources;

use App\Models\Booking;
use App\Models\Room;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\BookingResource\Pages\ListBookings;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Réservations de Chambres';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Hôtelière';
    protected static ?string $pluralModelLabel = 'Réservations de Chambres';
    protected static ?string $modelLabel = 'Réservation';



    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->label('Nom du client'),

                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'number')
                    ->live()
                    ->required()
                    ->label('Chambre N°')
                    ->afterStateUpdated(function ($get, $set) {
                        self::calculerPrixTotal($get, $set);
                    }),

                Forms\Components\DatePicker::make('check_in')
                    ->live()
                    ->required()
                    ->label('Date d\'arrivée')
                    ->native(false)
                    ->afterStateUpdated(function ($get, $set) {
                        self::calculerPrixTotal($get, $set);
                    }),

                Forms\Components\DatePicker::make('check_out')
                    ->live()
                    ->required()
                    ->label('Date de départ')
                    ->native(false)
                    ->afterStateUpdated(function ($get, $set) {
                        self::calculerPrixTotal($get, $set);
                    })
                    ->rules([
                        'after_or_equal:check_in',
                    ]),

                // AJOUT/CORRECTION : Ce champ doit être présent et configuré ainsi
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('€')
                    ->required() // Requis par la base de données
                    ->dehydrated() // Force l'envoi de la valeur lors de la sauvegarde
                    ->label('Prix Total'),
            ]);
    }

    public static function calculerPrixTotal($get, $set): void
    {
        $checkIn = $get('check_in');
        $checkOut = $get('check_out');
        $roomId = $get('room_id');

        if ($checkIn && $checkOut && $roomId) {
            $debut = Carbon::parse($checkIn);
            $fin = Carbon::parse($checkOut);
            $nuits = $debut->diffInDays($fin);

            if ($nuits > 0) {
                // Récupère la chambre avec son type pour avoir le prix de base
                $chambre = Room::with('roomType')->find($roomId);
                $prixBase = $chambre?->roomType?->base_price ?? 0;

                // Assigne la valeur calculée au champ total_price
                $set('total_price', $nuits * $prixBase);
            } else {
                $set('total_price', 0);
            }
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('room.number')->label('Chambre'),
                Tables\Columns\TextColumn::make('check_in')->label('Arrivée')->date(),
                Tables\Columns\TextColumn::make('check_out')->label('Départ')->date(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('EUR'),
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
            'index' => ListBookings::route('/'),
        ];
    }
}
