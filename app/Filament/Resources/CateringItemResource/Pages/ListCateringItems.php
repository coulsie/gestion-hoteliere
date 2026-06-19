<?php

namespace App\Filament\Resources\CateringItemResource\Pages;

use App\Filament\Resources\CateringItemResource;
use Filament\Actions\CreateAction; // 🔥 IMPORTATION DU BOUTON DE CREATION V5
use Filament\Resources\Pages\ListRecords;

class ListCateringItems extends ListRecords
{
    protected static string $resource = CateringItemResource::class;

    /**
     * 🔥 ACTION DE CONFIGURATION DE L'EN-TÊTE
     * Rétablit le bouton officiel pour ajouter de nouveaux plats à la carte
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Créer une Désignation')
                ->color('primary'), // Bleu éclatant style Bootstrap
        ];
    }
}
