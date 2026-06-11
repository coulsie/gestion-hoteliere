<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_booking_id',
        'receipt_number',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relation corrigée : On lie la méthode eventBooking au modèle Booking (Chambres)
     * en spécifiant explicitement votre clé étrangère 'event_booking_id'.
     */
    public function eventBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'event_booking_id');
    }

    // Relation avec l'utilisateur/caissier qui a enregistré le paiement
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcule le total déjà payé par le client pour une réservation donnée
     */
   public static function getSommePayeePourReservation(int|string|null $bookingId): float
{
    if (! $bookingId) {
        return 0.0;
    }

    return (float) static::where('event_booking_id', $bookingId)
        // FIX : On demande à SQL de faire la somme des paiements 'completed' ET 'validé / encaissé'
        ->whereIn('status', ['completed', 'validé / encaissé'])
        ->sum('amount');
}

    /**
     * Calcule le cumul déjà payé pour une location de salle
     */
    public static function getSommePayeePourSalle(int|string|null $bookingId): float
    {
        if (! $bookingId) {
            return 0.0;
        }

        return (float) static::where('event_booking_id', $bookingId)
            ->where('payment_type', 'salle')
            ->whereIn('status', ['completed', 'validé / encaissé'])
            ->sum('amount');
    }


}
