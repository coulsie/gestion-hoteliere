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

    // FIX SIGNATURE : Alignement strict sur Filament\Schemas\Schema exigé par votre framework
    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
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

                // GRILLE DE FACTURATION HORIZONTALE ÉPURÉE (4 COLONNES EN LIGNE)
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->label('Grille de commande (Ajustez les quantités pour chaque plat)')
                    ->columnSpanFull()
                    // FIX CHIRURGICAL : Aligne horizontalement les 4 champs sur une seule ligne sans plantage de classe
                    ->columns(4)
                    ->components([
                        Forms\Components\Select::make('catering_item_id')
                            ->label('Plat / Boisson')
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

                        Forms\Components\TextInput::make('price')
                            ->label('Prix Unitaire')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantité')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live(debounce: 150),

                        // AFFICHAGE REACTIF : Calcule le montant net de la ligne (Prix x Quantité)
                        Forms\Components\Placeholder::make('row_total')
                            ->label('Montant Net Ligne')
                            ->content(function ($get) {
                                $prix = (float) ($get('price') ?? 0);
                                $qte = (int) ($get('quantity') ?? 1);
                                return number_format($prix * $qte, 0, '.', ' ') . ' FCFA';
                            }),
                    ])
                    ->live(),

                // FACTURE TOTALE DYNAMIQUE GREEN STYLE
                Forms\Components\Placeholder::make('total_amount_display')
                    ->label('MONTANT TOTAL DE LA FACTURE RESTAURANT')
                    ->columnSpanFull()
                    ->content(function ($get) {
                        $items = $get('items') ?? [];
                        $totalGeneral = 0;

                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $prix = (float) ($item['price'] ?? 0);
                                $quantite = (int) ($item['quantity'] ?? 1);
                                $totalGeneral += $prix * $quantite;
                            }
                        }

                        return '<span style="font-size: 20px; font-weight: bold; color: #10b981;">' . number_format($totalGeneral, 0, '.', ' ') . ' FCFA</span>';
                    })
                    ->html(),
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

                // 1. TOTAL NOTE DYNAMIQUE (Calcule à la volée si la BDD a manqué l'enregistrement)
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Note')
                    ->state(function ($record) {
                        $montantBdd = (float) ($record->total_amount ?? 0);

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
                // 2. ACTION D'ENCAISSEMENT RESTAURATION SCELLÉE ET SÉCURISÉE AVEC DÉSTOCKAGE
                \Filament\Actions\Action::make('payer_resto')
                    ->label('Encaisser la note')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'en_attente')
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de règlement')
                            ->options([
                                'cash' => '💵 Espèces / Cash',
                                'wave' => '🌊 Wave',
                                'orange_money' => '🍊 Orange Money',
                                'mtn_momo' => '💛 MTN Mobile Money',
                                'moov_money' => '💙 Moov Money',
                                'card' => '💳 Carte Bancaire',
                            ])
                            ->required()
                            ->default('cash'),
                    ])
                    ->action(function (array $data, $record): void {
                        // Calcul de sécurité pour obtenir le montant réel de la note
                        $montantFinal = (float) $record->total_amount;
                        if ($montantFinal <= 0) {
                            foreach ($record->items as $item) {
                                $montantFinal += ((float)$item->price) * ((int)$item->quantity);
                            }
                        }

                        // Sécurité absolue de secours si le panier est vide
                        if ($montantFinal <= 0) {
                            $montantFinal = 5000;
                        }

                        // Enregistrement de la recette dans le grand livre de caisse
                        $payment = \App\Models\Payment::create([
                            'receipt_number'    => 'REC-RESTO-' . date('Ymd-His'),
                            'amount'            => $montantFinal,
                            'payment_method'    => $data['payment_method'],
                            'payment_type'      => 'restauration',
                            'status'            => 'validé / encaissé',
                            'date_encaissement' => now(),
                        ]);

                        // 🔥 ALERTE INSTANTANÉE PROPRIÉTAIRE (RESTAURANT)
                        \App\Services\TelegramService::notifierAlerteEncaissment(
                            caisse: 'restauration',
                            client: $record->client_name ?? 'Client Comptoir',
                            montant: $montantFinal,
                            methode: $data['payment_method'],
                            numRecu: $payment->receipt_number
                        );

                        // Mise à jour de l'état de la commande de restaurant
                        $record->update([
                            'status' => 'paye',
                            'total_amount' => $montantFinal
                        ]);

                        // ====================================================================
                        // 🔥 ALGORITHME DE DÉSTOCKAGE AUTOMATIQUE EXCLUSIF DES BOISSONS
                        // ====================================================================
                        if ($record->items()->exists()) {
                            foreach ($record->items as $orderItem) {
                                $item = \App\Models\CateringItem::find($orderItem->catering_item_id);

                                // On applique la soustraction STRICTEMENT si l'article est une boisson
                                if ($item && $item->category === 'boisson') {
                                    $nouveauStock = max(0, $item->stock_quantity - (int) $orderItem->quantity);

                                    $item->update([
                                        'stock_quantity' => $nouveauStock
                                    ]);

                                    // 🚨 NOTIFICATION COMPTABLE TELEGRAM : Alerte stock bas ou critique
                                    if ($nouveauStock <= $item->alert_threshold) {
                                        \App\Services\TelegramService::notifierAlerteEncaissment(
                                            caisse: 'restauration_alerte_stock',
                                            client: '🔥 SYSTEME ALERTE STOCK',
                                            montant: (float) $nouveauStock,
                                            methode: "Rupture imminente au bar : {$item->name}",
                                            numRecu: "ALERTE-BAR"
                                        );
                                    }
                                }
                            }
                        }
                        // ====================================================================

                        // Génération de l'URL pour l'impression du reçu PDF
                        $url = route('payment.receipt.download', ['record' => $payment->id]);

                        // Déclenchement de la notification interactive avec option d'impression directe
                        \Filament\Notifications\Notification::make()
                            ->title('Commande encaissée avec succès !')
                            ->actions([
                                \Filament\Actions\Action::make('imprimer')
                                    ->label('🖨️ Imprimer la note')
                                    ->color('success')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->body("Le reçu {$payment->receipt_number} d'un montant de " . number_format($montantFinal, 0, ',', ' ') . " FCFA a été validé et le stock mis à jour.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Valider l\'encaissement du restaurant')
                    ->modalSubmitActionLabel('Confirmer le paiement'),

                // 3. ACTION DE SUPPRESSION COMPATIBLE V5 SÉCURISÉE PAR RÔLE ADMINISTRATEUR
                \Filament\Actions\DeleteAction::make()
                    ->label('Supprimer')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    // 🔐 PROTECTION FILAMENT SHIELD : Seul le super_admin a le droit de voir et cliquer
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false)
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer définitivement la commande')
                    ->modalDescription('Êtes-vous sûr de vouloir supprimer cette commande restaurant ? Cette action détruira définitivement l\'historique de cette vente.')
                    ->modalSubmitActionLabel('Confirmer l\'effacement'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
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
