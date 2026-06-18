<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth; // 🔥 Pour vérifier l'admin connecté

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                // 🔥 SÉCURITÉ : Masque le bouton de suppression si la fiche éditée est celle de l'admin connecté
                ->hidden(fn ($record) => $record->id === Auth::id()),
        ];
    }
}
