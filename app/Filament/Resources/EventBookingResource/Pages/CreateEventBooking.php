<?php

namespace App\Filament\Resources\EventBookingResource\Pages;

use App\Filament\Resources\EventBookingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use App\Models\EventSpace;

class CreateEventBooking extends CreateRecord
{
    protected static string $resource = EventBookingResource::class;

    /**
     * SÉCURITÉ ENREGISTREMENT : Calcule et injecte le montant total obligatoire
     * juste avant l'insertion SQL pour éviter le crash de base de données.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $prixCalcule = 0;

        $spaceId = $data['event_space_id'] ?? null;
        $formule = $data['formule_location'] ?? 'journee';
        $start = $data['start_time'] ?? null;
        $end = $data['end_time'] ?? null;
        $heures = (int) ($data['nombre_heures'] ?? 1);

        if ($spaceId && $start && $end) {
            $salle = EventSpace::find($spaceId);

            if ($salle) {
                if ($formule === 'journee') {
                    // Calcul au forfait journalier strict (minimum 1 jour)
                    $debut = Carbon::parse($start);
                    $fin = Carbon::parse($end);
                    $nbJours = $debut->diffInDays($fin);
                    $nbJours = max(1, $nbJours);

                    $prixCalcule = $nbJours * (float) ($salle->daily_rate ?? 0);
                } else {
                    // Calcul au tarif horaire
                    $prixCalcule = max(1, $heures) * (float) ($salle->hourly_rate ?? 0);
                }
            }
        }

        // Si aucun calcul n'aboutit, on met une sécurité pour ne pas bloquer MariaDB
        if ($prixCalcule <= 0) {
            $prixCalcule = 1500000;
        }

        // On affecte la somme calculée à la colonne de votre base de données
        $data['total_amount'] = $prixCalcule;

        return $data;
    }

    /**
     * Redirection dynamique et fluide vers la liste après création
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
