<?php

namespace App\Filament\Resources\CateringItemResource\Pages;

use App\Filament\Resources\CateringItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCateringItem extends CreateRecord
{
    protected static string $resource = CateringItemResource::class;

    /**
     * Retour automatique à la liste des articles après création
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
