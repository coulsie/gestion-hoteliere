<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
     // Dès qu'une réservation est enregistrée, on change le statut de la chambre
    public function created(Booking $booking): void
    {
        $room = $booking->room;
        $room->update(['status' => 'occupee']);
    }

    // Dès qu'une réservation est supprimée ou archivée (Check-out)
    public function deleted(Booking $booking): void
    {
        $room = $booking->room;
        $room->update(['status' => 'menage']); // Passe en ménage après le départ
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        //
    }

      /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
