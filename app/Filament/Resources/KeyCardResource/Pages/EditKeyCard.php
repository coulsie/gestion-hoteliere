<?php

namespace App\Filament\Resources\KeyCardResource\Pages;

use App\Filament\Resources\KeyCardResource;
use Filament\Actions\DeleteAction; // <-- Importation de la classe globale corrigée
use Filament\Resources\Pages\EditRecord;

class EditKeyCard extends EditRecord
{
    protected static string $resource = KeyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Utilisation directe de l'action de suppression sans préfixe obsolète
            DeleteAction::make(),
        ];
    }
}
