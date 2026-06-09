<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    // Désactive la protection de masse pour autoriser Filament à écrire le 'name' et le 'base_price'
    protected $guarded = [];

    protected $fillable = [
        'name',
        'base_price',
        'base_price',
        'currency',
    ];

    // Relation avec les chambres
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
	