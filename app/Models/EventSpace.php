<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSpace extends Model
{
    // FIX SÉCURITÉ LARAVEL : Autorise l'enregistrement en masse de tous les champs (y compris 'name')
    protected $guarded = [];

    // ... Conservez le reste de vos relations ou configurations existantes en dessous
}
