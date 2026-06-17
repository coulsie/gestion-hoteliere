<?php

namespace App\Filament\Resources\CateringOrderResource\Pages;

// FIX COMPILATION : Importation exacte du chemin de la ressource principale
use App\Filament\Resources\CateringOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCateringOrder extends EditRecord
{
    protected static string $resource = CateringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Redirection automatique vers la liste des commandes après modification
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
