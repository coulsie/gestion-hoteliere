<?php

namespace App\Filament\Resources\CateringItemResource\Pages;

use App\Filament\Resources\CateringItemResource;
use Filament\Resources\Pages\EditRecord;

class EditCateringItem extends EditRecord
{
    protected static string $resource = CateringItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
