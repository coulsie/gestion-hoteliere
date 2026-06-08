<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Room;
use App\Models\Booking;
use BackedEnum;
use Filament\Support\Enums\Width;
use Carbon\Carbon;

class VueChambres extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected string $view = 'filament.pages.vue-chambres';

    protected static ?string $title = 'Carte des Chambres';

    protected Width|string|null $maxContentWidth = Width::Full;

    public $statusFilter = 'all';

    public function setFilter(string $status): void
    {
        $this->statusFilter = $status;
    }

    protected function getViewData(): array
    {
        $aujourdhui = Carbon::today()->toDateString();
        $chambres = Room::with('roomType')->orderBy('number')->get();

        // Analyse de chaque chambre par rapport au calendrier
        foreach ($chambres as $chambre) {
            // Vérifie si une réservation est en cours aujourd'hui pour cette chambre
            $reservationEnCours = Booking::where('room_id', $chambre->id)
                ->where('check_in', '<=', $aujourdhui)
                ->where('check_out', '>', $aujourdhui)
                ->first();

            if ($reservationEnCours) {
                // Le calendrier dit que la chambre est occupée par un client
                $chambre->statut_calculer = 'occupee';
                $chambre->client_actuel = $reservationEnCours->customer_name;
                $chambre->date_depart = Carbon::parse($reservationEnCours->check_out)->format('d/m/Y');
            } elseif ($chambre->status === 'menage') {
                // Si aucune réservation n'est en cours mais qu'elle est marquée en ménage
                $chambre->statut_calculer = 'menage';
            } else {
                // La chambre est libre sur le calendrier et propre matériellement
                $chambre->statut_calculer = 'disponible';
            }
        }

        // Filtrage dynamique selon le choix du réceptionniste sur les boutons Livewire
        if ($this->statusFilter !== 'all') {
            $chambres = $chambres->where('statut_calculer', $this->statusFilter);
        }

        return [
            'chambres' => $chambres,
        ];
    }
}
