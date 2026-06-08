<?php

namespace App\Filament\Resources\CateringItems\Pages;

use App\Filament\Resources\CateringItems\CateringItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCateringItem extends EditRecord
{
    protected static string $resource = CateringItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
