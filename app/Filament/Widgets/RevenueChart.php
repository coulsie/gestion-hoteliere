<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    // Titre compatible Filament v4 (sans static)
    protected ?string $heading = '📊 Évolution du Chiffre d\'Affaires (7 derniers jours)';

    // FIX FILAMENT v4 : On enlève le mot-clé "static" sur la couleur pour éviter le crash
    protected string $color = 'success';

    protected function getData(): array
    {
        $donnees = [];
        $labels = [];

        // Génération des statistiques jour par jour sur les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Format clair pour l'axe X (ex: "jeu. 11 juin")
            $labels[] = $date->translatedFormat('D d M');

            // Somme brute des encaissements pour cette journée précise
            $donnees[] = \App\Models\Payment::whereIn('status', ['completed', 'validé / encaissé'])
                ->whereDate('created_at', $date)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Recettes (FCFA)',
                    'data' => $donnees,
                    'fill' => 'start',
                    'tension' => 0.3, // Courbe lissée au style moderne
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
