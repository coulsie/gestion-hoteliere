<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringOrder extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        // Génère automatiquement un numéro de commande unique avant la création (Ex: CMD-20260612-1045)
        static::creating(function ($order) {
            $order->order_number = 'CMD-' . date('Ymd-Hi') . '-' . rand(10, 99);
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CateringOrderItem::class, 'catering_order_id');
    }
}
