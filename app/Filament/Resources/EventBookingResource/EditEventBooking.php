<?php

namespace App\Filament\Resources\EventBookings\Pages;

use App\Filament\Resources\EventBookings\EventBookingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventBooking extends EditRecord
{
    protected static string $resource = EventBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
