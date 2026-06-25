<?php

namespace App\Filament\Resources\DailyClosures\Pages;

use App\Filament\Resources\DailyClosures\DailyClosureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyClosure extends EditRecord
{
    protected static string $resource = DailyClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
