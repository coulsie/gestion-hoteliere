<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions\CreateAction; // Importation v5 obligatoire
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    // Cette fonction force l'affichage du bouton de réservation en haut à droite
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading('Allouer une Chambre (Nouvelle Réservation)'),
        ];
    }
}
