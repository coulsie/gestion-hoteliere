<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringOrderItem extends Model
{
    // FIX DÉFINITIF : On indique le nom exact de la table pivot créée par votre migration
    protected $table = 'catering_order_items';

    protected $guarded = [];

    public function cateringOrder(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class, 'catering_order_id');
    }

    public function cateringItem(): BelongsTo
    {
        return $this->belongsTo(CateringItem::class, 'catering_item_id');
    }
}
