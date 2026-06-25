<?php

namespace App\Filament\Resources\DailyClosureResource\Pages;

use App\Filament\Resources\DailyClosureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyClosures extends ListRecords
{
    protected static string $resource = DailyClosureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Effectuer une Clôture de Caisse')
                ->color('primary'),
        ];
    }
}
