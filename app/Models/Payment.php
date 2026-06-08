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

    // Relation avec la réservation d'espace
    public function eventBooking(): BelongsTo
    {
        return $this->belongsTo(EventBooking::class);
    }

    // Relation avec l'utilisateur/caissier qui a enregistré le paiement
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
