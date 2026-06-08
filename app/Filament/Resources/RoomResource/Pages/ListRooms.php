<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions\CreateAction; // <-- Obligatoire en v5
use Filament\Resources\Pages\ListRecords;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    // Cette fonction force l'apparition du bouton "Créer" pour les chambres
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading('Ajouter une Chambre'),
        ];
    }
}
