<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyClosure extends Model
{
    protected $fillable = [
        'user_id', 'closure_date',
        'theoretical_cash', 'theoretical_mobile', 'theoretical_card',
        'real_cash', 'real_mobile', 'real_card',
        'discrepancy', 'notes', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
