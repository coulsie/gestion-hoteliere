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
}
