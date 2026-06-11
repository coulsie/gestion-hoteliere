<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
        protected function getStats(): array
    {
        // 1. FIX ABSOLU : Calcul de la Recette Journalière brute (Paiements du jour)
        $recetteDuJour = \App\Models\Payment::whereIn('status', ['completed', 'validé / encaissé'])
            ->whereDate('created_at', \Illuminate\Support\Carbon::today())
            ->sum('amount');

        // 2. FIX ABSOLU : Calcul du Taux d'Occupation
        $totalChambres = max(1, \App\Models\Room::count());
        $chambresOccupees = \App\Models\Room::where('housekeeping_status', 'sale')
            ->orWhereHas('bookings', function ($query) {
                $query->where('check_in', '<=', \Illuminate\Support\Carbon::now())
                    ->where('check_out', '>=', \Illuminate\Support\Carbon::now());
            })->count();

        $tauxOccupation = round(($chambresOccupees / $totalChambres) * 100);

        // 3. FIX ABSOLU : Chambres nécessitant une action (Ménage)
        $chambresSales = \App\Models\Room::where('housekeeping_status', 'sale')->count();

        return [
            \Filament\Widgets\StatsOverviewWidget\Stat::make('💰 Recette du Jour', number_format($recetteDuJour, 0, '.', ' ') . ' FCFA')
                ->description('Cumul des encaissements aujourd\'hui')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('🏨 Taux d\'Occupation', $tauxOccupation . ' %')
                ->description("{$chambresOccupees} chambre(s) occupée(s) sur {$totalChambres}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($tauxOccupation > 50 ? 'success' : 'warning'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('🧹 Alertes Ménage', $chambresSales . ' Chambre(s)')
                ->description('Nombre d\'hébergements marqués SALE')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($chambresSales > 0 ? 'danger' : 'success'),
        ];
    }


}
