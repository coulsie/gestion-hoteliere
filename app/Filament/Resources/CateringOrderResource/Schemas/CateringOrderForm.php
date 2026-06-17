<?php



namespace App\Filament\Resources\CateringOrderResource\Schemas; // FIX : Singulier


use Filament\Schemas\Schema;

use App\Filament\Resources\CateringOrderResource;

class CateringOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
