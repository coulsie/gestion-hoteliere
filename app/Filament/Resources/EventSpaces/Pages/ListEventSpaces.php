<?php

namespace App\Filament\Resources\EventSpaces\Pages;

use App\Filament\Resources\EventSpaceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventSpaces extends ListRecords
{
    protected static string $resource = EventSpaceResource::class;

    /**
     * Configuration des boutons d'en-tête de la liste
     */
    protected function getHeaderActions(): array
    {
        return [
            // On s'assure que le bouton ouvre la page entière de création
            CreateAction::make()
                ->label('Ajouter un espace'),
        ];
    }
}
