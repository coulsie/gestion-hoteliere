<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringItem extends Model
{
protected $fillable = [
    'name',
    'category',
    'unit_price',
    'stock_quantity',  // 🔥 AJOUT
    'alert_threshold'  // 🔥 AJOUT
];


protected $guarded = [];

    /**
     * Relation : Un extra de restauration appartient à une réservation de chambre
     */
    public function eventBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'event_booking_id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // 💡 Assurez-vous que le modèle "CateringOrderItem" correspond bien au nom dans votre dossier app/Models/
        return $this->hasMany(\App\Models\CateringOrderItem::class, 'catering_item_id');
    }

}
