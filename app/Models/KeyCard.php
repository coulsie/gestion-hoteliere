<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeyCard extends Model
{
    use HasFactory;

    // Définition de la table associée en base de données
    protected $table = 'key_cards';

    // Utilisation de $guarded = [] pour s'aligner sur la configuration de votre modèle Booking
    protected $guarded = [];

    /**
     * Relation : Une carte magnétique peut être associée à plusieurs réservations au fil du temps
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope utile pour Filament : Récupérer uniquement les cartes fonctionnelles et disponibles
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
