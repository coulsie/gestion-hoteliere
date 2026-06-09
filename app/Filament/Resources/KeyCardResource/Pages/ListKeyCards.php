<?php

namespace App\Filament\Resources\KeyCardResource\Pages;

use App\Filament\Resources\KeyCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKeyCards extends ListRecords
{
    protected static string $resource = KeyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
