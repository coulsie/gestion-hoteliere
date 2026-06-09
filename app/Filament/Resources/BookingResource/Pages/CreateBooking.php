<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Room;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    /**
     * Intercepte et sécurise les données du formulaire avant l'envoi à MariaDB
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roomId = $data['room_id'] ?? null;
        $checkIn = $data['check_in'] ?? null;

        if ($roomId && $checkIn) {
            $room = Room::with('roomType')->find($roomId);
            $typeChambre = strtolower($room?->roomType?->name ?? '');
            $prixUnitaire = $room?->roomType?->base_price ?? 0;

            // Sécurité absolue pour le type Passage
            if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
                $heures = (int) ($data['nombre_heures'] ?? 1);

                // On injecte directement les valeurs manquantes dans le tableau SQL
                $data['check_out'] = $checkIn;
                $data['total_price'] = $heures * $prixUnitaire;
            }
        }

        return $data;
    }
}
