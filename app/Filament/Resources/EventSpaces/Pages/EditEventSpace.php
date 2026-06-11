<?php

namespace App\Filament\Resources\EventSpaces\Pages;

use App\Filament\Resources\EventSpaceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventSpace extends EditRecord
{
    protected static string $resource = EventSpaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * REDIRECTION AUTOMATIQUE : Renvoie l'utilisateur vers le tableau
     * de la liste des salles immédiatement après avoir cliqué sur Enregistrer.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
