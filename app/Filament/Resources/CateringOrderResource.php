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
                        // FIX COMPOSANT LIVE : Force Livewire à écouter ce choix pour le Placeholder parent
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
                        // FIX RECOUVREMENT : Informe instantanément le formulaire du changement de chiffre
                        ->live(debounce: 150),

                    Forms\Components\TextInput::make('price')
                        ->label('Prix Unitaire')
                        ->numeric()
                        ->prefix('FCFA')
                        ->readOnly()
                        ->required()
                        // FIX PERSISTENCE : Permet au Placeholder de lire la valeur calculée
                        ->live(),
                ])
                // Rapprochement Livewire complet du bloc répéteur
                ->live()
                ->saveRelationshipsUsing(function ($record, $state) {
                    $record->items()->delete();
                    if (is_array($state)) {
                        foreach ($state as $item) {
                            if (!empty($item['catering_item_id'])) {
                                $record->items()->create([
                                    'catering_item_id' => $item['catering_item_id'],
                                    'quantity' => (int) ($item['quantity'] ?? 1),
                                    'price' => (float) ($item['price'] ?? 0),
                                ]);
                            }
                        }
                    }
                }),

            // Affichage dynamique et réactif corrigé
            Forms\Components\Placeholder::make('total_amount_display')
                ->label('Montant Total de la Commande')
                ->columnSpanFull()
                ->content(function ($get) {
                    // Extraction des lignes en direct avec typage sécurisé
                    $items = $get('items');
                    $total = 0;

                    if (is_array($items)) {
                        foreach ($items as $item) {
                            $prix = (float) ($item['price'] ?? 0);
                            $quantite = (int) ($item['quantity'] ?? 1);
                            $total += $prix * $quantite;
                        }
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
                

                                // FIX VUE LISTE : Calcule le montant en direct si la BDD est restée à 0
            Tables\Columns\TextColumn::make('total_amount')
                ->label('Total Note')
                ->state(function ($record) {
                    $montantBdd = (float) ($record->total_amount ?? 0);

                    // Si la BDD indique 0 mais qu'il y a des articles, on fait la somme en direct
                    if ($montantBdd <= 0 && $record->items()->exists()) {
                        $totalCalcule = 0;
                        foreach ($record->items as $item) {
                            $totalCalcule += ((float)$item->price) * ((int)$item->quantity);
                        }
                        return $totalCalcule;
                    }

                    return $montantBdd;
                })
                ->money('XOF')
                ->sortable(),

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
                    ->visible(fn ($record) => $record->status === 'en_attente')
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de règlement')
                            ->options(['cash' => 'Espèces / Cash', 'mobile_money' => 'Mobile Money'])
                            ->required()
                            ->default('cash'),
                    ])
                    ->action(function (array $data, $record) {
                        // On recalcule le montant réel des lignes pour le reçu au cas où total_amount soit à 0
                        $montantReel = (float) $record->total_amount;
                        if ($montantReel <= 0) {
                            foreach ($record->items as $item) {
                                $montantReel += ((float)$item->price) * ((int)$item->quantity);
                            }
                        }

                        $payment = \App\Models\Payment::create([
                            'receipt_number'    => 'REC-RESTO-' . date('Ymd-His'),
                            'event_booking_id'  => null,
                            'amount'            => $montantReel > 0 ? $montantReel : 5000, // Valeur de secours
                            'payment_method'    => $data['payment_method'],
                            'payment_type'      => 'restauration',
                            'status'            => 'validé / encaissé',
                            'date_encaissement' => now(),
                        ]);

                        $record->update([
                            'status' => 'paye',
                            'total_amount' => $montantReel > 0 ? $montantReel : 5000
                        ]);

                        $url = route('payment.receipt.download', ['record' => $payment->id]);

                        \Filament\Notifications\Notification::make()
                            ->title('Commande encaissée avec succès !')
                            ->actions([
                                \Filament\Actions\Action::make('imprimer')
                                    ->label('🖨️ Imprimer la note')
                                    ->color('success')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->send();
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
