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
    protected function casts(): array
    {
        return [
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    /**
     * Relation : Une réservation appartient à une chambre physique
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
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

    /**
     * Relation : Une réservation peut être associée à une carte d'accès magnétique
     */
    public function keyCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KeyCard::class, 'key_card_id');
    }


protected static function booted(): void
{
    static::deleting(function (Booking $booking) {
        // 1. Libération automatique de la carte magnétique
        if ($booking->key_card_id) {
            $booking->key_card_id = null;
            $booking->save();
        }

        // 2. AUTOMATISATION : La chambre passe à l'état 'sale' dès que le client libère la chambre
        if ($booking->room_id) {
            \App\Models\Room::where('id', $booking->room_id)->update([
                'housekeeping_status' => 'sale'
            ]);
        }
    });
}

    /**
     * Relation : Une réservation peut avoir plusieurs commandes de restauration
     */


    /**
     * Relation : Une réservation peut avoir plusieurs commandes de restauration
     */
    public function cateringItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CateringItem::class, 'event_booking_id');
    }

    /**
     * Calcule le montant total cumulé (Chambre + Extras)
     */
    public function getGrandTotalAttribute(): float
    {
        // 1. On récupère le prix de base de la chambre stocké dans votre colonne native total_price
        $coutChambre = (float) ($this->total_price ?? 0);

        // 2. Récupération des extras. Nous utilisons une sécurité (prix OU montant OU 0)
        // pour s'adapter à la colonne exacte de votre table catering_items
        $coutRestauration = (float) $this->cateringItems->sum(function ($item) {
            return $item->prix ?? $item->amount ?? $item->price ?? 0;
        });

        // 3. Retourne la somme globale
        return $coutChambre + $coutRestauration;
    }


}

