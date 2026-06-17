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
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nom du plat / Forfait banquet'),

                Forms\Components\Select::make('category')
                    ->options([
                        'plat' => 'Plat / Menu Cuisine',
                        'boisson' => 'Boisson / Boisson Bar',
                        'forfait_buffet' => 'Forfait Buffet / Pause-Café',
                    ])
                    ->required()
                    ->label('Catégorie Restauration'),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required()
                    ->label('Prix Unitaire'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Désignation'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'plat' => 'info',
                        'boisson' => 'success',
                        'forfait_buffet' => 'warning',
                        default => 'gray',
                    })
                    ->label('Catégorie'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->money('XOF')
                    ->label('Prix Unitaire'),
            ])
            ->actions([
                // 1. Action native d'édition
                \Filament\Actions\EditAction::make(),

                // 2. BOUTON D'ENCAISSEMENT DIRECT COMPTOIR AVEC TERMINAISON SCELLÉE
                \Filament\Actions\Action::make('encaisser_restaurant')
                    ->label('Encaisser & Reçu')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('receipt_number')
                            ->label('Numéro de Reçu Restaurant')
                            ->default('REC-RESTO-' . date('Ymd-His'))
                            ->required()
                            ->readOnly(),

                        \Filament\Forms\Components\TextInput::make('client_passage')
                            ->label('Nom du client (Optionnel)')
                            ->placeholder('Ex: Client de passage au comptoir')
                            ->default('Client de passage'),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Montant de la Note (A Encaisser)')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required()
                            ->default(fn($record) => (float) ($record->unit_price ?? 0)),

                        \Filament\Forms\Components\Select::make('payment_method')
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
                    ->action(function (array $data, $record, \Filament\Actions\Action $action): void {
                        $payment = \App\Models\Payment::create([
                            'receipt_number'    => $data['receipt_number'],
                            'event_booking_id'  => null,
                            'amount'            => $data['amount'],
                            'payment_method'    => $data['payment_method'],
                            'payment_type'      => 'restauration',
                            'status'            => 'validé / encaissé',
                            'date_encaissement' => now(),
                        ]);

                        $url = route('payment.receipt.download', ['record' => $payment->id]);

                        \Filament\Notifications\Notification::make()
                            ->title('Note de restaurant encaissée !')
                            ->actions([
                                \Filament\Actions\Action::make('imprimer')
                                    ->label('🖨️ Imprimer la note')
                                    ->color('success')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->success()
                            ->send();

                        $action->success();
                    })
                    // FIX SYNTAXE : Scellage obligatoire de la fenêtre modale Filament
                    ->requiresConfirmation()
                    ->modalHeading('Encaisser un client direct au comptoir')
                    ->modalSubmitActionLabel('Émettre le Ticket Resto'),
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
