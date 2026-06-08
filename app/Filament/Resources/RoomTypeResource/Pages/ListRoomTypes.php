<?php

namespace App\Filament\Resources\RoomTypeResource\Pages;

use App\Filament\Resources\RoomTypeResource;
use Filament\Actions\CreateAction; // <-- Obligatoire en v5
use Filament\Resources\Pages\ListRecords;

class ListRoomTypes extends ListRecords
{
    protected static string $resource = RoomTypeResource::class;

    // Cette fonction force l'apparition du bouton "Créer" en haut à droite
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading('Créer un Type de Chambre'),
        ];
    }
}
