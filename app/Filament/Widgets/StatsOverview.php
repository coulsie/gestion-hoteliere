<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth; // 🔥 Ajouté pour récupérer l'admin connecté
use App\Models\Payment;
use App\Models\Room;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $aujourdhui = Carbon::today();

        // 🔥 Récupération dynamique du prénom ou nom de l'administrateur connecté
        $adminNom = Auth::user()?->name ?? 'Administrateur';

        // 1. Somme des paiements
        $recetteDuJour = (float) Payment::whereIn('status', ['completed', 'validé / encaissé'])
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        // 2. Calcul du Taux d'Occupation
        $totalChambres = Room::count() ?: 1;

        $chambresOccupees = Room::where('housekeeping_status', 'sale')
            ->orWhereHas('bookings', function ($query) use ($aujourdhui) {
                $query->whereDate('check_in', '<=', $aujourdhui)
                      ->whereDate('check_out', '>=', $aujourdhui);
            })
            ->count();

        $tauxOccupation = (int) round(($chambresOccupees / $totalChambres) * 100);

        // 3. Alertes Ménage
        $chambresSales = Room::where('housekeeping_status', 'sale')->count();

        $chartRecette = $recetteDuJour > 0 ? [$recetteDuJour * 0.3, $recetteDuJour * 0.7, $recetteDuJour * 0.5, $recetteDuJour] : [];
        $chartMenage = $chambresSales > 0 ? [$chambresSales * 1.5, $chambresSales * 0.5, $chambresSales] : [];

        return [
            // BLOC 1 : FINANCES + MESSAGE DE BIENVENUE INTÉGRÉ
            Stat::make("👋 Bonjour, {$adminNom} !", number_format($recetteDuJour, 0, '.', ' ') . ' FCFA')
                ->description("Recette du jour encaissée aujourd'hui")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($chartRecette)
                ->color('success'),

            // BLOC 2 : TAUX D'OCCUPATION
            Stat::make('🏨 Taux d\'Occupation', "{$tauxOccupation} %")
                ->description("{$chambresOccupees} occupée(s) / {$totalChambres} disponibles")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([10, 30, 45, $tauxOccupation])
                ->color(match(true) {
                    $tauxOccupation >= 70 => 'success',
                    $tauxOccupation >= 30 => 'warning',
                    default => 'info'
                }),

            // BLOC 3 : ALERTE LOGISTIQUE
            Stat::make('🧼 État du Ménage', "{$chambresSales} Chambre(s) Sale(s)")
                ->description($chambresSales > 0 ? 'Action requise par le personnel' : 'Toutes les chambres sont prêtes')
                ->descriptionIcon($chambresSales > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->chart($chartMenage)
                ->color($chambresSales > 0 ? 'danger' : 'success'),
        ];
    }
}
