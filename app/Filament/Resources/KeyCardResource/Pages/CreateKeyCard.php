<?php

namespace App\Filament\Resources\KeyCardResource\Pages; // <-- Mettre au singulier

use App\Filament\Resources\KeyCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKeyCard extends CreateRecord
{
    protected static string $resource = KeyCardResource::class;
}
