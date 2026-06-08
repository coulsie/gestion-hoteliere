<?php

namespace App\Filament\Resources\EventBookings\Pages;

use App\Filament\Resources\EventBookings\EventBookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventBooking extends CreateRecord
{
    protected static string $resource = EventBookingResource::class;
}
