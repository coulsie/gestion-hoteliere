<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // MODIFICATION : Désactive la protection pour autoriser l'enregistrement de 'number', 'status', etc.
    protected $guarded = [];

    // Relation : Une chambre appartient à un type de chambre
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    // Relation : Une chambre peut avoir plusieurs réservations
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
