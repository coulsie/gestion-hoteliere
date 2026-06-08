<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_type',
        'start_date',
        'end_date',
        'total_revenue',
        'transactions_count',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_revenue' => 'decimal:2',
        'transactions_count' => 'integer',
    ];
}
