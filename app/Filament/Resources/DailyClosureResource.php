<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyClosureResource\Pages;
use App\Models\DailyClosure;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section; // 🔥 RECTIFICATION NAMESPACE V5 EXACT
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyClosureResource extends Resource
{
    protected static ?string $model = DailyClosure::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Clôtures de Caisse';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Financière';
    protected static ?string $pluralModelLabel = 'Clôtures de Caisse';
    protected static ?string $modelLabel = 'Clôture';

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 🔥 RECTIFICATION V5 : Utilisation directe de la classe Section importée
                Section::make('Informations Générales')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('closure_date')
                            ->label('Date de Clôture')
                            ->default(now()->toDateString())
                            ->readOnly()
                            ->required(),
                        TextInput::make('user_name')
                            ->label('Agent de Caisse')
                            ->default(fn () => Auth::user()?->name ?? 'Caissier')
                            ->readOnly(),
                    ]),

                Section::make('💵 Écritures Théoriques Système (Recettes du Jour)')
                    ->description('Montants calculés d\'après les reçus émis en base de données aujourd\'hui.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('theoretical_cash')
                            ->label('Système : Espèces')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            // 🔥 CORRECTION : Utilisation de paid_at à la place de date_encaissement
                            ->default(fn () => (float) Payment::whereDate('paid_at', Carbon::today())->where('payment_method', 'cash')->sum('amount')),

                        TextInput::make('theoretical_mobile')
                            ->label('Système : Mobile Money')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            // 🔥 CORRECTION : Utilisation de paid_at à la place de date_encaissement
                            ->default(fn () => (float) Payment::whereDate('paid_at', Carbon::today())->whereIn('payment_method', ['wave', 'orange_money', 'mtn_momo', 'moov_money'])->sum('amount')),

                        TextInput::make('theoretical_card')
                            ->label('Système : Banque / Cartes')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            // 🔥 CORRECTION : Utilisation de paid_at à la place de date_encaissement
                            ->default(fn () => (float) Payment::whereDate('paid_at', Carbon::today())->whereIn('payment_method', ['card', 'bank_transfer'])->sum('amount')),
                    ]),

                Section::make('💰 Comptage Physique (Saisie Réceptionniste)')
                    ->description('Saisissez l\'argent physique réellement présent dans le tiroir-caisse et sur les téléphones de l\'hôtel.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('real_cash')
                            ->label('Physique : Espèces')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required()
                            ->live(debounce: 200)
                            ->afterStateUpdated(fn ($state, $set, $get) => self::calculerEcart($set, $get)),

                        TextInput::make('real_mobile')
                            ->label('Physique : Mobile Money')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required()
                            ->live(debounce: 200)
                            ->afterStateUpdated(fn ($state, $set, $get) => self::calculerEcart($set, $get)),

                        TextInput::make('real_card')
                            ->label('Physique : TPE Carte')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required()
                            ->live(debounce: 200)
                            ->afterStateUpdated(fn ($state, $set, $get) => self::calculerEcart($set, $get)),
                    ]),

                Section::make('⚠️ Analyse de l\'Écart de Caisse')
                    ->schema([
                        TextInput::make('discrepancy')
                            ->label('Écart de Caisse Constaté')
                            ->numeric()
                            ->prefix('FCFA')
                            ->readOnly()
                            ->helperText('Un montant négatif indique un MANQUANT en caisse. Un montant positif indique un SURPLUS.'),

                        Textarea::make('notes')
                            ->label('Commentaires / Justifications de l\'écart')
                            ->placeholder('Obligatoire si l\'écart n\'est pas égal à 0 F CFA')
                            ->required(fn ($get) => (float)$get('discrepancy') !== 0.0),
                    ]),
            ]);
    }

    protected static function calculerEcart($set, $get): void
    {
        $theorique = (float)$get('theoretical_cash') + (float)$get('theoretical_mobile') + (float)$get('theoretical_card');
        $reel = (float)$get('real_cash') + (float)$get('real_mobile') + (float)$get('real_card');

        $set('discrepancy', $reel - $theorique);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('closure_date')->label('Date')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Caissier'),
                Tables\Columns\TextColumn::make('real_cash')->label('Espèces Comptées')->money('XOF'),
                Tables\Columns\TextColumn::make('discrepancy')
                    ->label('Écart de Caisse')
                    ->money('XOF')
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'success' : ($state < 0 ? 'danger' : 'warning')),
                Tables\Columns\TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()?->hasRole('super_admin') ?? false),
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
            'index' => Pages\ListDailyClosures::route('/'),
            'create' => Pages\CreateDailyClosure::route('/create'),
        ];
    }
}
