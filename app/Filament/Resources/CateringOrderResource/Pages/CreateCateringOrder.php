<?php

namespace App\Filament\Resources\CateringOrderResource\Pages;

use App\Filament\Resources\CateringOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCateringOrder extends CreateRecord
{
    protected static string $resource = CateringOrderResource::class;

    /**
     * FIX TOTAL À LA CRÉATION : S'exécute juste après l'enregistrement
     * de la commande et de ses articles pour mettre à jour la colonne total_amount.
     */
    protected function afterCreate(): void
    {
        $order = $this->record;
        $total = 0;

        // On calcule la somme directement depuis les lignes d'articles liées en BDD
        foreach ($order->items as $item) {
            $total += ((float)$item->price) * ((int)$item->quantity);
        }

        // On enregistre définitivement la vraie valeur numérique dans la table catering_orders
        $order->update([
            'total_amount' => $total,
        ]);
    }

    /**
     * Redirection automatique vers la liste après création
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
