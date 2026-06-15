<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CateringOrderResource\Pages;
use App\Models\CateringOrder;
use App\Models\CateringItem;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CateringOrderResource extends Resource
{
    protected static ?string $model = CateringOrder::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Prendre Commande';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';
    protected static ?string $pluralModelLabel = 'Commandes Restaurant';
    protected static ?string $modelLabel = 'Commande';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('client_name')
                    ->label('Nom du client (Si externe / Comptoir)')
                    ->default('Client Comptoir')
                    ->required(),

                Forms\Components\Select::make('booking_id')
                    ->label('Rattacher à une Chambre (Si Résident)')
                    ->relationship(
                        name: 'booking',
                        titleAttribute: 'customer_name',
                        modifyQueryUsing: function ($query, $related, $component, $state) {
                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->placeholder('Laissez vide si client externe'),

                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->label('Articles commandés')
                    ->columnSpanFull()
                    ->grid(2)
                    ->components([
                        Forms\Components\Select::make('catering_item_id')
                            ->label('Choisir un article')
                            ->options(\App\Models\CateringItem::all()->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!empty($state)) {
                                    $item = \Illuminate\Support\Facades\DB::table('catering_items')
                                        ->where('id', $state)
                                        ->first();

                                    $set('price', $item ? (float)$item->unit_price : 0);
                                }
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantité')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live(debounce: 150),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix Unitaire')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            ->required(),
                    ])
                    ->live(),

                // FIX DEFINITIF : Affichage dynamique réactif à l'écran sans aucun champ masqué conflictuel
                Forms\Components\Placeholder::make('total_amount_display')
                    ->label('Montant Total de la Commande')
                    ->columnSpanFull()
                    ->content(function ($get) {
                        $items = $get('items') ?? [];
                        $total = 0;

                        foreach ($items as $item) {
                            $prix = (float) ($item['price'] ?? 0);
                            $quantite = (int) ($item['quantity'] ?? 1);
                            $total += $prix * $quantite;
                        }

                        return number_format($total, 0, '.', ' ') . ' FCFA';
                    }),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Commande')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('booking.room.number')
                    ->label('Chambre N°')
                    ->placeholder('🧑‍💻 Externe'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Note')
                    ->money('XOF'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'paye' => 'success',
                        'en_attente' => 'warning',
                        'annule' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('payer_resto')
                    ->label('Encaisser la note')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'en_attente')
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de règlement')
                            ->options(['cash' => 'Espèces / Cash', 'mobile_money' => 'Mobile Money'])
                            ->required()
                            ->default('cash'),
                    ])
                    ->action(function (array $data, $record) {
                        \App\Models\Payment::create([
                            'receipt_number'    => 'REC-RESTO-' . date('Ymd-His'),
                            'amount'            => $record->total_amount,
                            'payment_method'    => $data['payment_method'],
                            'payment_type'      => 'restauration',
                            'status'            => 'validé / encaissé',
                            'date_encaissement' => now(),
                        ]);

                        $record->update(['status' => 'paye']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCateringOrders::route('/'),
            'create' => Pages\CreateCateringOrder::route('/create'),
        ];
    }
}
