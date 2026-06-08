<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    // MODIFICATION : Autorise l'enregistrement de toutes les données du formulaire de réservation
    protected $guarded = [];

    // Relation : Une réservation appartient à une chambre physique
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
