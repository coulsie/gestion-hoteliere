<?php

namespace App\Filament\Resources;

use App\Models\CateringItem;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class CateringItemResource extends Resource
{
    protected static ?string $model = CateringItem::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Services Restauration';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des Espaces';
    protected static ?string $pluralModelLabel = 'Services Restauration';
    protected static ?string $modelLabel = 'Service Restauration';

      public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nom du plat / Forfait banquet')
                    ->columnSpan(1),

                Forms\Components\Select::make('category')
                    ->label('Catégorie Restauration')
                    ->options([
                        'plat' => '🍽️ Plat / Menu Cuisine',
                        'boisson' => '🍹 Boisson / Boisson Bar',
                        'forfait_buffet' => '🏢 Forfait Buffet / Pause-Café',
                    ])
                    ->required()
                    ->live()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required()
                    ->label('Prix Unitaire')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('stock_quantity')
                    ->label('Quantité en Stock Actuelle')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->visible(fn ($get) => $get('category') === 'boisson')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('alert_threshold')
                    ->label('Seuil d\'alerte critique')
                    ->numeric()
                    ->default(5)
                    ->required()
                    ->hint('Alerte si le stock descend sous ce nombre')
                    ->visible(fn ($get) => $get('category') === 'boisson')
                    ->columnSpan(1),

                // ====================================================================
                // 📊 SECTION HISTORIQUE DES VENTES CORRIGÉE NAMESPACE V5
                // ====================================================================
                // 🔥 FIX : Utilisation du namespace de schéma unifié obligatoire en v5
                \Filament\Schemas\Components\Section::make('📈 Historique Récent des Ventes au Bar')
                    ->description('Liste des dernières sorties et encaissements enregistrés pour cette boisson.')
                    ->collapsible()
                    ->visible(fn ($get, $record) => $get('category') === 'boisson' && $record !== null)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Repeater::make('orderItems')
                            ->relationship('orderItems')
                            ->label('Sorties enregistrées')
                            ->columnSpanFull()
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->components([
                                Forms\Components\TextInput::make('created_at')
                                    ->label('Date & Heure')
                                    ->afterStateHydrated(fn ($component, $state) => $component->state($state ? \Illuminate\Support\Carbon::parse($state)->format('d/m/Y H:i') : '—'))
                                    ->readOnly(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantité Vendue')
                                    ->suffix('bouteille(s)')
                                    ->readOnly(),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix Appliqué')
                                    ->suffix('FCFA')
                                    ->readOnly(),

                                Forms\Components\Placeholder::make('total_ligne')
                                    ->label('Encaissé Total')
                                    ->content(function ($get) {
                                        $prix = (float) ($get('price') ?? 0);
                                        $qte = (int) ($get('quantity') ?? 1);
                                        return number_format($prix * $qte, 0, '.', ' ') . ' FCFA';
                                    }),
                            ])
                    ]),
                // ====================================================================
            ]);
    }

        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Désignation'),

                \Filament\Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'plat' => 'info',
                        'boisson' => 'success',
                        'forfait_buffet' => 'warning',
                        default => 'gray',
                    })
                    ->label('Catégorie'),

                \Filament\Tables\Columns\TextColumn::make('unit_price')
                    ->money('XOF')
                    ->label('Prix Unitaire'),


                // À insérer dans ->columns([...]) de votre fonction table():
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock Réel')
                    ->sortable()
                    // 🔥 Si c'est un plat, on affiche un tiret, si c'est une boisson, on affiche le chiffre ou "RUPTURE"
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->category !== 'boisson') {
                            return '—';
                        }
                        return $state <= 0 ? '🚨 RUPTURE DE STOCK' : $state . ' bouteille(s)';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->category !== 'boisson') return 'gray';
                        if ($state <= 0) return 'danger';
                        if ($state <= $record->alert_threshold) return 'warning';
                        return 'success';
                    }),

            ])
            // 🔥 CORRECTION : On ne garde strictement que l'Action native de modification des plats
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
            'index' => CateringItemResource\Pages\ListCateringItems::route('/'),
        ];
    }
}
