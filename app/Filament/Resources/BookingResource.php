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
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;


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
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('customer_name')
                    ->label('Nom du client')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('room_id')
                    ->label('Chambre N°')
                    ->relationship('room', 'number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        static::calculerTarifDynamique($get, $set);
                    }),

                Forms\Components\DatePicker::make('check_in')
                    ->label('Date d\'arrivée')
                    ->default(now())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($get, $set) => static::calculerTarifDynamique($get, $set)),

                // Durée en heures pour le type passage (Masqué si classique)
                Forms\Components\Select::make('nombre_heures')
                    ->label('Durée du passage (Heures)')
                    ->options([
                        1 => '1 Heure de passage',
                        2 => '2 Heures de passage',
                        3 => '3 Heures de passage',
                        4 => '4 Heures de passage',
                    ])
                    ->default(1)
                    ->required()
                    ->live()
                    ->dehydrated(false) // Champ virtuel, ne s'envoie pas en BDD
                    ->visible(function ($get) {
                        $roomId = $get('room_id');
                        if (! $roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        $type = strtolower($room?->roomType?->name ?? '');
                        return str_contains($type, 'passage') || str_contains($type, 'heure');
                    })
                    ->afterStateUpdated(function ($state, $get, $set) {
                        if ($get('check_in')) {
                            $set('check_out', $get('check_in'));
                        }
                        static::calculerTarifDynamique($get, $set);
                    }),

                // TOUJOURS VISIBLE mais verrouillé si c'est un passage (Règle l'erreur 1364)
                Forms\Components\DatePicker::make('check_out')
                    ->label('Date de départ')
                    ->default(now()->addDay())
                    ->required()
                    ->live()
                    ->dehydrated() // Force l'envoi en BDD
                    ->readOnly(function ($get) {
                        $roomId = $get('room_id');
                        if (! $roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        $type = strtolower($room?->roomType?->name ?? '');
                        return str_contains($type, 'passage') || str_contains($type, 'heure');
                    })
                    ->afterStateUpdated(fn ($get, $set) => static::calculerTarifDynamique($get, $set)),

                // TOUJOURS VISIBLE mais verrouillé si c'est un passage (Règle l'erreur 1364)
                Forms\Components\TextInput::make('total_price')
                    ->label('Prix Total')
                    ->numeric()
                    ->required()
                    ->prefix('FCFA')
                    ->dehydrated() // Force l'envoi en BDD
                    ->readOnly(function ($get) {
                        $roomId = $get('room_id');
                        if (! $roomId) return false;
                        $room = \App\Models\Room::with('roomType')->find($roomId);
                        $type = strtolower($room?->roomType?->name ?? '');
                        return str_contains($type, 'passage') || str_contains($type, 'heure');
                    }),
            ]);
    }


/**
 * Alignement complet des calculs sur la structure de votre base de données
 */
public static function calculerTarifDynamique($get, $set): void
{
    $roomId = $get('room_id');
    $start = $get('check_in');

    if (!empty($roomId) && !empty($start)) {
        $room = Room::with('roomType')->find($roomId);
        $typeChambre = strtolower($room?->roomType?->name ?? '');
        $prixUnitaire = $room?->roomType?->base_price ?? 0;

        // Formule de Passage à l'heure
        if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
            $heures = (int) ($get('nombre_heures') ?? 1);

            // On enregistre le même jour dans check_out car le type BDD est "date" sans heure
            $set('check_out', $start);
            // Calcul du prix total basé sur le nombre d'heures
            $set('total_price', $heures * $prixUnitaire);
        }
        // Formule Hôtelière classique (Nuitée)
        else {
            $end = $get('check_out');
            if ($end) {
                $debut = Carbon::make($start);
                $fin = Carbon::make($end);
                $jours = max(1, $debut->diffInDays($fin));
                $set('total_price', $jours * $prixUnitaire);
            }
        }
    }
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
