<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
        protected function getStats(): array
    {
        // 1. Calcul de la Recette Journalière brute (Paiements du jour)
        $recetteDuJour = Payment::whereIn('status', ['completed', 'validé / encaissé'])
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // 2. Calcul du Taux d'Occupation
        $totalChambres = max(1, Room::count());
        $chambresOccupees = Room::where('housekeeping_status', 'sale')
            ->orWhereHas('bookings', function ($query) {
                $query->where('check_in', '<=', Carbon::now())
                    ->where('check_out', '>=', Carbon::now());
            })->count();

        $tauxOccupation = round(($chambresOccupees / $totalChambres) * 100);

        // 3. Chambres nécessitant une action (Ménage)
        $chambresSales = Room::where('housekeeping_status', 'sale')->count();

        return [
            // FIX : Ajout des 4 arguments obligatoires (0, '.', ' ') pour number_format
            Stat::make('💰 Recette du Jour', number_format($recetteDuJour, 0, '.', ' ') . ' FCFA')
                ->description('Cumul des encaissements aujourd\'hui')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('🏨 Taux d\'Occupation', $tauxOccupation . ' %')
                ->description("{$chambresOccupees} chambre(s) occupée(s) sur {$totalChambres}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($tauxOccupation > 50 ? 'success' : 'warning'),

            Stat::make('🧹 Alertes Ménage', $chambresSales . ' Chambre(s)')
                ->description('Nombre d\'hébergements marqués SALE')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($chambresSales > 0 ? 'danger' : 'success'),
        ];
    }

}
