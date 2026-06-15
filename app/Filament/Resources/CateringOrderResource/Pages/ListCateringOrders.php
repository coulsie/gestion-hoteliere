<?php

namespace App\Filament\Resources\CateringOrderResource\Pages;

use App\Filament\Resources\CateringOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCateringOrders extends ListRecords
{
    protected static string $resource = CateringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle Commande'),
        ];
    }
}
