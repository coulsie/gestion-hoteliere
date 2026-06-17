<?php

namespace App\Filament\Resources\CateringOrderResource\Pages;

use App\Filament\Resources\CateringOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCateringOrder extends CreateRecord
{
    protected static string $resource = CateringOrderResource::class;

    /**
     * SÉCURITÉ COMPTABILITÉ : S'exécute juste après la création de la commande.
     * Parcourt le tableau horizontal, crée la table pivot et sauvegarde le Montant Total.
     */
    protected function afterCreate(): void
    {
        $order = $this->record;
        $totalGeneral = 0;

        // On force le rafraîchissement des relations à partir des lignes saisies dans la grille
        foreach ($order->items as $item) {
            $totalGeneral += ((float)$item->price) * ((int)$item->quantity);
        }

        // On enregistre la somme arithmétique exacte dans la colonne total_amount
        $order->update([
            'total_amount' => $totalGeneral,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
