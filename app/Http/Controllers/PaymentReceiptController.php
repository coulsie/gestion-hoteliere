<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentReceiptController extends \App\Http\Controllers\Controller
{
    public function download(Payment $record)
    {
        // Chargement sécurisé de la relation vers la chambre pour la facture
        $record->load(['eventBooking.room.roomType']);

        return view('pdf.receipt', [
            'payment' => $record,
            'booking' => $record->eventBooking,
        ]);
    }
}
