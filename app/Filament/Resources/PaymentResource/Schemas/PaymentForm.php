<?php

namespace App\Filament\Resources\PaymentResource\Schemas;
 // <-- Mettre PaymentResource (singulier)

use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
