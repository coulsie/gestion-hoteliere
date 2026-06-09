<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\KeyCard;

class Booking extends Model
{
    // Utilisation exclusive de $guarded = [] pour autoriser tous les champs à la fois,
    // ce qui évite d'avoir à lister manuellement tous vos anciens champs.
    protected $guarded = [];

    // Configuration des formats de date automatiques (Casts) pour les cartes magnétiques
    protected $casts = [
        'key_card_assigned_at' => 'datetime',
        'key_card_expires_at' => 'datetime',
    ];

    /**
     * Relation : Une réservation appartient à une chambre physique
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relation : Une réservation peut être associée à une carte d'accès magnétique
     */
   
    public function keyCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KeyCard::class);
    }

}

