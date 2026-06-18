<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    // 🔥 MÊME TRI POUR COMPLÉTER LA LIGNE DES GRAPHIQUES
    protected static ?int $sort = 1;

    protected ?string $heading = '📊 Évolution du Chiffre d\'Affaires (7 derniers jours)';

    protected int | string | array $columnSpan = 1;

    protected string $color = 'success';

    protected function getData(): array
    {
        $donnees = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('D d M');

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
                    'tension' => 0.3,
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
