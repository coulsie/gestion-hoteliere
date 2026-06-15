<?php

namespace App\Filament\Resources\CateringOrderResource\Pages;

use App\Filament\Resources\CateringOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCateringOrder extends CreateRecord
{
    protected static string $resource = CateringOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
