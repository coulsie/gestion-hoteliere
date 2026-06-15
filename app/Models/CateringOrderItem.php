<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringOrderItem extends Model
{
    // Autorise l'enregistrement de toutes les colonnes (quantité, prix, etc.)
    protected $guarded = [];

    /**
     * Relation : Une ligne de commande appartient à une commande globale
     */
    public function cateringOrder(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class, 'catering_order_id');
    }

    /**
     * Relation : Une ligne de commande est liée à un article de la carte du restaurant
     */
    public function cateringItem(): BelongsTo
    {
        return $this->belongsTo(CateringItem::class, 'catering_item_id');
    }
}
