<?php

namespace App\Filament\Resources\EventSpaces\Pages;

use App\Filament\Resources\EventSpaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventSpace extends CreateRecord
{
    protected static string $resource = EventSpaceResource::class;

    /**
     * REDIRECTION AUTOMATIQUE : Renvoie l'utilisateur vers le tableau
     * de la liste des salles immédiatement après l'enregistrement.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
