<?php

namespace App\Filament\Resources\DailyClosureResource\Pages;

use App\Filament\Resources\DailyClosureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyClosure extends CreateRecord
{
    protected static string $resource = DailyClosureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // Enregistre de force l'ID du réceptionniste connecté
        $data['status'] = 'clôturé';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
