<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBooking extends Model
{
    // La méthode doit s'appeler exactement "eventSpace"
    public function eventSpace(): BelongsTo
    {
        return $this->belongsTo(EventSpace::class);
    }
}
