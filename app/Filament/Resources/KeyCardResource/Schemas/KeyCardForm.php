<?php


namespace App\Filament\Resources\KeyCardResource\Schemas; // FIX : Singulier


use Filament\Schemas\Schema;

class KeyCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
