<?php

namespace App\Filament\Resources\EventBookingResource\Pages;

use App\Filament\Resources\EventBookingResource; // FIX : Retrait du "s" pour coller au dossier réel
use Filament\Resources\Pages\EditRecord;

class EditEventBooking extends EditRecord
{
    protected static string $resource = EventBookingResource::class;

    /**
     * Redirection dynamique vers la liste après modification
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
