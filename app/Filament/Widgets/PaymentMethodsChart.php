<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PaymentMethodsChart extends ChartWidget
{
    // 🔥 FORCE LE TRI AU DÉBUT DE LA LIGNE 1
    protected static ?int $sort = 1;

    protected ?string $heading = '💳 Répartition des Modes de Règlement';

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
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
