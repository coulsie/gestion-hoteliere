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

/**
 * Calcule le montant total théorique de la chambre pour ce séjour
 */
    
/**
 * Calcule le montant total théorique du séjour (Prend en compte les jours ou les heures)
 */
public function getMontantTotalChambreAttribute(): float
{
    $debut = \Illuminate\Support\Carbon::parse($this->start_date ?? $this->created_at);
    $fin = \Illuminate\Support\Carbon::parse($this->end_date ?? now());

    // Récupère le nom de la catégorie (ex: "Chambre de passage")
    $typeChambre = strtolower($this->room?->roomType?->name ?? '');
    $prixBase = $this->room?->roomType?->base_price ?? 0;

    // Détection automatique : Si c'est une formule de passage ou à l'heure
    if (str_contains($typeChambre, 'passage') || str_contains($typeChambre, 'heure')) {
        // Calcule le nombre d'heures réelles (minimum 1 heure facturée)
        $heures = max(1, $debut->diffInHours($fin));
        return (float) ($heures * $prixBase);
    }

    // Sinon, tarification standard à la nuitée / jour
    $jours = max(1, $debut->diffInDays($fin));
    return (float) ($jours * $prixBase);
}


}

