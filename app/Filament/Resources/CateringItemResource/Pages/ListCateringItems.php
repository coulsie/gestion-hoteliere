<?php

namespace App\Filament\Resources\CateringItemResource\Pages;

use App\Filament\Resources\CateringItemResource;
use Filament\Actions\CreateAction; // Importation v5 obligatoire
use Filament\Resources\Pages\ListRecords;

class ListCateringItems extends ListRecords
{
    protected static string $resource = CateringItemResource::class;

    // Ajoute le bouton de création en haut à droite qui ouvre le formulaire en pop-up
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading('Ajouter un Article à la Carte Restauration / Banquet'),
        ];
    }
}
