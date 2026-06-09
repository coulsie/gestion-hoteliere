<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use App\Models\Booking;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Grouping\Group; // Import obligatoire pour la ventilation visuelle


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Comptabilité & Reçus';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Financière';

    protected static ?string $pluralModelLabel = 'Paiements & Recettes';

    protected static ?string $modelLabel = 'Paiement';




    public static function form(Schema $schema): Schema
{
    return $schema
        ->columns(2)
        ->components([
            TextInput::make('receipt_number')
                ->label('Numéro de Reçu')
                ->default('REC-' . date('Ymd-His'))
                ->required()
                ->readOnly()
                ->dehydrated()
                ->columnSpanFull(),

            Select::make('event_booking_id')
                ->label('Réservation concernée')
                ->relationship('eventBooking', 'id')
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->getOptionLabelFromRecordUsing(fn ($record) => "Réservation N° " . $record->id . " - Chambre " . ($record->room?->number ?? 'N/A'))
                ->afterStateUpdated(function ($state, $set) {
                    if ($state) {
                        $booking = Booking::with('room.roomType')->find($state);
                        if ($booking) {
                            // 1. Coût total de la chambre
                            $debut = Carbon::parse($booking->start_date);
                            $fin = Carbon::parse($booking->end_date);
                            $jours = max(1, $debut->diffInDays($fin));
                            $prixNuit = $booking->room?->roomType?->base_price ?? 0;
                            $totalChambre = $jours * $prixNuit;

                            // 2. Soustraction des anciens paiements
                            $dejaPaye = Payment::getSommePayeePourReservation($state);
                            $reliquatRestant = max(0, $totalChambre - $dejaPaye);

                            // On pré-remplit le champ avec le vrai reste à payer arrondi
                            $set('amount', round($reliquatRestant, 2));
                        }
                    }
                }),

            TextInput::make('amount')
                ->label('Montant Encaissé')
                ->numeric()
                ->prefix('FCFA')
                ->required(),

            Placeholder::make('cout_theorique')
                ->label('Historique Financier de la Chambre')
                ->columnSpanFull()
                ->content(function ($get) {
                    $bookingId = $get('event_booking_id');
                    if (! $bookingId) return new HtmlString('<span style="color: #6c757d; font-style: italic;">Sélectionnez une réservation pour voir le coût.</span>');

                    $booking = Booking::with('room.roomType')->find($bookingId);
                    if (! $booking) return new HtmlString('<span style="color: #dc3545; font-weight: bold;">Réservation introuvable.</span>');

                    $debut = Carbon::parse($booking->start_date);
                    $fin = Carbon::parse($booking->end_date);

                    $typeChambre = strtolower($booking->room?->roomType?->name ?? '');
                    $prixUnitaire = $booking->room?->roomType?->base_price ?? 0;

                    // Logique d'affichage conditionnel
                    if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
                        $unites = max(1, $debut->diffInHours($fin));
                        $libelleDuree = $unites . ' heure(s)';
                        $libelleTarif = 'Tarif Horaire';
                    } else {
                        $unites = max(1, $debut->diffInDays($fin));
                        $libelleDuree = $unites . ' nuit(s)';
                        $libelleTarif = 'Tarif par Nuitée';
                    }

                    $totalChambre = $unites * $prixUnitaire;
                    $dejaPaye = Payment::getSommePayeePourReservation($bookingId);

                    return new HtmlString('
                        <div style="font-size: 14px; background: #f8f9fa; padding: 15px; border-radius: 12px; border: 1px solid #dee2e6; display: inline-block; width: 100%; box-sizing: border-box;">
                            <div style="color: #333; font-weight: bold; margin-bottom: 5px;">📊 Fiche Financière :</div>
                            <div style="color: #dc3545; font-weight: bold;">• Coût total obligatoire : ' . number_format($totalChambre, 0, ',', ' ') . ' FCFA (' . $libelleDuree . ')</div>
                            <div style="color: #6c757d; font-size: 12px; margin-left: 10px;">[' . $libelleTarif . ' : ' . number_format($prixUnitaire, 0, ',', ' ') . ' FCFA]</div>
                            <div style="color: #198754; font-weight: bold; margin-top: 5px;">• Déjà encaissé au total : ' . number_format($dejaPaye, 0, ',', ' ') . ' FCFA</div>
                        </div>
                    ');
                }),


                Placeholder::make('reste_a_payer')
                    ->label('Reliquat de la facture')
                    ->columnSpanFull()
                    ->content(function ($get) {
                        $bookingId = $get('event_booking_id');
                        if (! $bookingId) return null;

                        $booking = Booking::with('room.roomType')->find($bookingId);
                        if (! $booking) return null;

                        $debut = Carbon::parse($booking->start_date);
                        $fin = Carbon::parse($booking->end_date);
                        $jours = max(1, $debut->diffInDays($fin));
                        $prixNuit = $booking->room?->roomType?->base_price ?? 0;
                        $totalChambre = $jours * $prixNuit;

                        // Somme historique + ce qui est tapé à l'écran en direct
                        $dejaPayeEnBdd = Payment::getSommePayeePourReservation($bookingId);
                        $montantEnCoursSaisie = (float) $get('amount');

                        $totalVerseGlobal = $dejaPayeEnBdd + $montantEnCoursSaisie;

                        // CORRECTION : Variable écrite sans espace et en CamelCase
                        $reliquatFinal = $totalChambre - $totalVerseGlobal;

                        if ($reliquatFinal > 0) {
                            return new HtmlString('
                                <div style="color: #fd7e14; font-size: 15px; font-weight: 900; background: rgba(253, 126, 20, 0.08); padding: 10px 15px; border-radius: 8px; display: inline-block; border: 1px solid rgba(253, 126, 20, 0.15); margin-top: 5px;">
                                    💵 Solde restant dû après ce versement : ' . number_format($reliquatFinal, 0, ',', ' ') . ' FCFA
                                </div>
                            ');
                        } elseif ($reliquatFinal < 0) {
                            return new HtmlString('
                                <div style="color: #0d6efd; font-size: 15px; font-weight: 900; background: rgba(13, 110, 253, 0.08); padding: 10px 15px; border-radius: 8px; display: inline-block; border: 1px solid rgba(13, 110, 253, 0.15); margin-top: 5px;">
                                    💳 Trop-perçu global : ' . number_format(abs($reliquatFinal), 0, ',', ' ') . ' FCFA
                                </div>
                            ');
                        } else {
                            return new HtmlString('
                                <div style="color: #198754; font-size: 15px; font-weight: 900; background: rgba(25, 135, 84, 0.08); padding: 10px 15px; border-radius: 8px; display: inline-block; border: 1px solid rgba(25, 135, 84, 0.15); margin-top: 5px;">
                                    ✅ Avec ce versement, la facture sera entièrement soldée !
                                </div>
                            ');
                        }
                    }),


            Select::make('payment_method')
                ->label('Mode de règlement')
                ->options([
                    'cash' => 'Espèces / Cash',
                    'card' => 'Carte Bancaire',
                    'mobile_money' => 'Mobile Money',
                    'bank_transfer' => 'Virement Bancaire',
                ])
                ->required(),

            Select::make('status')
                ->label('Statut')
                ->options([
                    'completed' => 'Validé / Encaissé',
                    'pending' => 'En attente',
                ])
                ->default('completed')
                ->required(),

            DateTimePicker::make('paid_at')
                ->label('Date d\'encaissement')
                ->default(now())
                ->required(),

            Textarea::make('notes')
                ->label('Remarques / Notes')
                ->columnSpanFull(),
        ]);
}


    public static function table(Table $table): Table
{
    return $table
        // 1. VENTILATION AUTOMATIQUE : Regroupe les lignes par Type de Chambre
        ->groups([
            Group::make('eventBooking.room.roomType.name')
                ->label('Type de Chambre')
                ->collapsible(), // Permet de plier/déplier chaque catégorie
        ])
        ->defaultGroup('eventBooking.room.roomType.name') // Active le regroupement par défaut

        ->columns([
            TextColumn::make('receipt_number')
                ->label('N° Reçu')
                ->searchable()
                ->sortable(),

            TextColumn::make('eventBooking.room.number')
                ->label('Chambre')
                ->sortable(),

            TextColumn::make('eventBooking.room.roomType.name')
                ->label('Catégorie / Type')
                ->badge()
                ->color('gray'),

            TextColumn::make('amount')
                ->label('Montant Encaissé')
                ->money('XOF')
                ->sortable()
                // Calcule automatiquement le sous-total par type de chambre ET le total général
                ->summarize(
                    Sum::make()
                        ->label('Recette Totale Période')
                        ->money('XOF')
                ),

            TextColumn::make('payment_method')
                ->label('Méthode')
                ->badge()
                ->color('info'),

            TextColumn::make('paid_at')
                ->label('Date d\'encaissement')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ])
                    ->filters([
                // FILTRE COMPTABLE UNIFIÉ : Journalier, Hebdomadaire et Période personnalisée
                Filter::make('periode_comptable')
                    ->label('Sélection de la Période')
                    ->form([
                        Select::make('raccourci')
                            ->label('Filtre rapide')
                            ->options([
                                'today' => 'Aujourd\'hui (Recette Journalière)',
                                'week' => 'Cette semaine (7 derniers jours)',
                                'custom' => 'Période personnalisée...',
                            ])
                            ->default('today')
                            ->live(), // Permet d'afficher/masquer les dates dynamiquement

                        DatePicker::make('paid_from')
                            ->label('Du (Date de début)')
                            ->visible(fn ($get) => $get('raccourci') === 'custom'),

                        DatePicker::make('paid_until')
                            ->label('Au (Date de fin)')
                            ->visible(fn ($get) => $get('raccourci') === 'custom'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $raccourci = $data['raccourci'] ?? 'today';

                        return match ($raccourci) {
                            // Vue journalière brute
                            'today' => $query->whereDate('paid_at', Carbon::today()),

                            // Vue sur une semaine complète (du lundi au dimanche en cours)
                            'week' => $query->whereBetween('paid_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek()
                            ]),

                            // Vue sur une période libre choisie par l'utilisateur
                            'custom' => $query
                                ->when($data['paid_from'], fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
                                ->when($data['paid_until'], fn ($q, $date) => $q->whereDate('paid_at', '<=', $date)),

                            default => $query,
                        };
                    }),
            ])

        ->actions([
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\Action::make('print_receipt')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action(fn ($record) => redirect()->route('payment.receipt.download', $record)),
        ]);
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
