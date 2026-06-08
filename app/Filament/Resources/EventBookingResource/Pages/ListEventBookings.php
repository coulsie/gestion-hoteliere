<?php

namespace App\Filament\Resources\EventBookingResource\Pages;

use App\Filament\Resources\EventBookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventBookings extends ListRecords
{
    protected static string $resource = EventBookingResource::class;

    // Force le bouton de création en pop-up
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading('Nouvelle Réservation Événementielle'),
        ];
    }
}
