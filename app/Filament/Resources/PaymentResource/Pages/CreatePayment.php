<?php

namespace App\Filament\Resources\PaymentResource\Pages; // <-- Mettre PaymentResource (singulier)


use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\ListRecords;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
