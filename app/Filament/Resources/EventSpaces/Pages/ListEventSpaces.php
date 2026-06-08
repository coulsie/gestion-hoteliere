<?php

namespace App\Filament\Resources\EventSpaces\Pages;

use App\Filament\Resources\EventSpaceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventSpaces extends ListRecords
{

    protected static string $resource = EventSpaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

