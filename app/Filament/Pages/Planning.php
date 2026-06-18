<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Carbon;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon; // 🔥 IMPORTATION DE L'ICÔNE EN IMAGE
use BezhanSalleh\FilamentShield\Traits\HasPageShield; // 🔥 IMPORTATION DE LA SÉCURITÉ SHIELD

class Planning extends Page
{
    use HasPageShield; // 🔥 ACTIVE LE CONTRÔLE DES ACCÈS PAR RÔLE SUR CETTE PAGE

    // 🔥 CORRECTIF VISUEL : Utilisation de l'objet Heroicon natif pour afficher l'image colorée
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Planning des Occupations';
    protected static string|UnitEnum|null $navigationGroup = 'Gestion Hôtelière';
    protected static ?string $title = 'Planning & Grille d\'Occupation';

    protected string $view = 'filament.pages.planning';

    public array $joursDuMois = [];
    public array $grilleOccupation = [];
    public string $moisActuelTexte = '';

    public function mount(): void
    {
        // 1. Détermination du mois en cours
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();
        $this->moisActuelTexte = Carbon::now()->translatedFormat('F Y');

        // 2. Génération des jours du mois pour l'en-tête du tableau
        $nbJours = $debutMois->daysInMonth;
        for ($i = 1; $i <= $nbJours; $i++) {
            $this->joursDuMois[] = $debutMois->copy()->day($i);
        }

        // 3. Récupération de toutes les chambres
        $chambres = Room::all();

        // 4. Récupération des réservations qui chevauchent ce mois
        $reservations = Booking::where('check_out', '>=', $debutMois)
            ->where('check_in', '<=', $finMois)
            ->get();

        // 5. Construction de la grille d'occupation (Chambre par Chambre, Jour par Jour)
        foreach ($chambres as $chambre) {
            $planningChambre = [];

            foreach ($this->joursDuMois as $jour) {
                // Cherche s'il y a une réservation pour cette chambre à ce jour précis
                $reservationTrouvee = $reservations->first(function ($booking) use ($chambre, $jour) {
                    $checkIn = Carbon::parse($booking->check_in);
                    $checkOut = Carbon::parse($booking->check_out);
                    return $booking->room_id == $chambre->id && $jour->between($checkIn, $checkOut);
                });

                $planningChambre[$jour->format('Y-m-d')] = $reservationTrouvee ? [
                    'id' => $reservationTrouvee->id,
                    'client' => $reservationTrouvee->customer_name,
                    'type' => Carbon::parse($reservationTrouvee->check_in)->format('Y-m-d') == Carbon::parse($reservationTrouvee->check_out)->format('Y-m-d') ? 'passage' : 'sejour'
                ] : null;
            }

            $this->grilleOccupation[] = [
                'chambre' => $chambre->number,
                'planning' => $planningChambre,
            ];
        }
    }
}
