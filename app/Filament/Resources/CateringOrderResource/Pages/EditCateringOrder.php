<?php

namespace App\Filament\Resources\CateringOrders\Pages;

use App\Filament\Resources\CateringOrders\CateringOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCateringOrder extends EditRecord
{
    protected static string $resource = CateringOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
