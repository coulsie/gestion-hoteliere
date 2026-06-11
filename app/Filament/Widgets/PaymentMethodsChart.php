<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PaymentMethodsChart extends ChartWidget
{
    // FIX FILAMENT v4 : On enlève le mot-clé "static" qui provoquait le plantage
    protected ?string $heading = '💳 Répartition des Modes de Règlement';

    protected function getData(): array
    {
        // Calcul du volume financier réel pour chaque mode de règlement
        $cash = \App\Models\Payment::where('payment_method', 'cash')->sum('amount');
        $mobileMoney = \App\Models\Payment::where('payment_method', 'mobile_money')->sum('amount');
        $card = \App\Models\Payment::where('payment_method', 'card')->sum('amount');
        $transfer = \App\Models\Payment::where('payment_method', 'bank_transfer')->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Volume (FCFA)',
                    'data' => [$cash, $mobileMoney, $card, $transfer],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#3b82f6', '#6366f1'],
                ],
            ],
            'labels' => ['Espèces', 'Mobile Money', 'Carte Bancaire', 'Virement'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
