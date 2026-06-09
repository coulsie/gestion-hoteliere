<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * Cette méthode native de Filament définit où aller APRES avoir cliqué sur "Créer"
     */
    protected function getRedirectUrl(): string
    {
        // $this->record représente le paiement qui vient tout juste d'être enregistré en BDD
        return route('payment.receipt.download', ['record' => $this->record->id]);
    }
}
