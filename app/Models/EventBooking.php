<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBooking extends Model
{
    // FIX SÉCURITÉ : Autorise l'enregistrement de tous les champs (client_name, etc.)
    protected $guarded = [];

    /**
     * Relation : Une réservation d'événement est liée à un espace événementiel (salle)
     */
    public function eventSpace(): BelongsTo
    {
        return $this->belongsTo(EventSpace::class, 'event_space_id');
    }
}
