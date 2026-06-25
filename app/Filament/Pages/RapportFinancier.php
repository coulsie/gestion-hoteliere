<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RapportFinancier extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Rapport par Période';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Financière';

    protected static ?string $title = 'État des Recettes par Caisse';

    protected string $view = 'filament.pages.rapport-financier';

    public array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'date_debut' => Carbon::now()->startOfMonth()->toDateString(),
            'date_fin'   => Carbon::now()->endOfMonth()->toDateString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Sélectionner la période d\'analyse')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date_debut')
                            ->label('Date de Début')
                            ->required()
                            ->live(),

                        DatePicker::make('date_fin')
                            ->label('Date de Fin')
                            ->required()
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * 🖨️ ACTION D'IMPRESSION SYNCHRONISÉE FILAMENT V5
     * Force la lecture de l'état brut du navigateur au moment exact du clic
     */
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('imprimer_rapport')
                ->label('🖨️ Imprimer le Bilan')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->action(function (): void {
                    // 🔥 LA CLE DU PROBLEME : On force l'extraction en direct de l'état du formulaire
                    $formState = $this->form->getRawState();

                    $debut = $formState['date_debut'] ?? Carbon::now()->startOfMonth()->toDateString();
                    $fin = $formState['date_fin'] ?? Carbon::now()->endOfMonth()->toDateString();

                    $debutClean = Carbon::parse($debut)->toDateString();
                    $finClean = Carbon::parse($fin)->toDateString();

                    $url = route('admin.rapport.print', [
                        'debut' => $debutClean,
                        'fin'   => $finClean
                    ]);

                    // Émission de l'événement vers le script Blade pour ouvrir le nouvel onglet
                    $this->dispatch('open-window', url: $url);
                }),
        ];
    }

    /**
     * 📊 CALCULATEUR DES CARTES DE REVENUS À L'ÉCRAN
     */
    public function getRecettesProperty(): array
    {
        // Extraction synchronisée pour les cartes
        $formState = $this->form->getRawState();
        $dateDebut = $formState['date_debut'] ?? Carbon::now()->startOfMonth()->toDateString();
        $dateFin = $formState['date_fin'] ?? Carbon::now()->endOfMonth()->toDateString();

        $debut = Carbon::parse($dateDebut)->toDateString() . ' 00:00:00';
        $fin = Carbon::parse($dateFin)->toDateString() . ' 23:59:59';

        $restaurant = DB::table('payments')
            ->whereBetween('paid_at', [$debut, $fin])
            ->where('receipt_number', 'LIKE', '%RESTO%')
            ->sum('amount');

        $salle = DB::table('payments')
            ->whereBetween('paid_at', [$debut, $fin])
            ->where('receipt_number', 'LIKE', '%SALLE%')
            ->sum('amount');

        $paiementsHotel = DB::table('payments')
            ->whereBetween('paid_at', [$debut, $fin])
            ->where('receipt_number', 'LIKE', 'REC-%')
            ->where('receipt_number', 'NOT LIKE', '%RESTO%')
            ->where('receipt_number', 'NOT LIKE', '%SALLE%')
            ->get();

        $chambreNormale = 0;
        $passage = 0;
        $suite = 0;

        foreach ($paiementsHotel as $payment) {
            $booking = DB::table('bookings')
                ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                ->where('bookings.id', $payment->event_booking_id)
                ->select('room_types.name as type_name')
                ->first();

            $nomType = strtolower($booking?->type_name ?? '');

            if (str_contains($nomType, 'passage')) {
                $passage += (float) $payment->amount;
            } elseif (str_contains($nomType, 'suite')) {
                $suite += (float) $payment->amount;
            } else {
                $chambreNormale += (float) $payment->amount;
            }
        }

        $totalGeneral = $restaurant + $salle + $chambreNormale + $passage + $suite;

        return compact('restaurant', 'salle', 'chambreNormale', 'passage', 'suite', 'totalGeneral');
    }
}
