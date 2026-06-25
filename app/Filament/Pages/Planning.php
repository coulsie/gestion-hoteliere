<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Icons\Heroicon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Planning extends Page
{
    use HasPageShield; // Conserve le contrôle d'accès Shield par rôle

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Planning des Occupations';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Hôtelière';
    protected static ?string $title = 'Planning & Grille d\'Occupation';

    protected string $view = 'filament.pages.planning';

    public array $joursDuMois = [];
    public array $grilleOccupation = [];
    public string $moisActuelTexte = '';
    public string $currentMonthStr = ''; // Stocke le mois au format Y-m

    public function mount(): void
    {
        // Initialisation sur le mois courant si vide
        if (empty($this->currentMonthStr)) {
            $this->currentMonthStr = Carbon::now()->format('Y-m');
        }

        $this->chargerPlanning();
    }

    /**
     * 🔥 NAVIGATION DYNAMIQUE DES MOIS
     */
    public function moisPrecedent(): void
    {
        $this->currentMonthStr = Carbon::parse($this->currentMonthStr . '-01')->subMonth()->format('Y-m');
        $this->chargerPlanning();
    }

    public function moisSuivant(): void
    {
        $this->currentMonthStr = Carbon::parse($this->currentMonthStr . '-01')->addMonth()->format('Y-m');
        $this->chargerPlanning();
    }

    /**
     * 📊 ALGORITHME GÉNÉRATEUR DE LA GRILLE BOOTSTRAP
     */
    public function chargerPlanning(): void
    {
        $this->joursDuMois = [];
        $this->grilleOccupation = [];

        $debutMois = Carbon::parse($this->currentMonthStr . '-01')->startOfMonth();
        $finMois = Carbon::parse($this->currentMonthStr . '-01')->endOfMonth();

        // Label en français (ex: "Juillet 2026")
        $this->moisActuelTexte = ucfirst($debutMois->translatedFormat('F Y'));

        $nbJours = $debutMois->daysInMonth;
        for ($i = 1; $i <= $nbJours; $i++) {
            $this->joursDuMois[] = $debutMois->copy()->day($i);
        }

        $chambres = Room::orderBy('number', 'asc')->get();

        $reservations = Booking::where('check_out', '>=', $debutMois)
            ->where('check_in', '<=', $finMois)
            ->get();

        foreach ($chambres as $chambre) {
            $planningChambre = [];

            foreach ($this->joursDuMois as $jour) {
                $reservationTrouvee = $reservations->first(function ($booking) use ($chambre, $jour) {
                    $checkIn = Carbon::parse($booking->check_in)->startOfDay();
                    $checkOut = Carbon::parse($booking->check_out)->endOfDay();
                    return $booking->room_id == $chambre->id && $jour->between($checkIn, $checkOut);
                });

                if ($reservationTrouvee) {
                    $checkIn = Carbon::parse($reservationTrouvee->check_in);
                    $checkOut = Carbon::parse($reservationTrouvee->check_out);

                    // Détermination du type de séjour
                    $isPassage = $checkIn->diffInHours($checkOut) <= 12 || $checkIn->isSameDay($checkOut);

                    // 🔥 GENERATEUR D'URL NATIF FILAMENT V5 IMMUNISÉ CONTRE LES ERREURS DE ROUTAGES
                    $urlSecurisee = '#';


                    // 🔥 RECTIFICATION FILAMENT V5 POUR LES RESSOURCES EN MODE MODALE
                    try {
                        // Construit l'URL pointant vers la liste mais en forçant le déclenchement immédiat de la modale edit
                        $urlSecurisee = \App\Filament\Resources\BookingResource::getUrl('index') . "?tableAction=edit&tableActionRecord={$reservationTrouvee->id}";
                    } catch (\Exception $e) {
                        $urlSecurisee = url("/admin/reservations-de-chambres?tableAction=edit&tableActionRecord={$reservationTrouvee->id}");
                    }


                    $planningChambre[$jour->format('Y-m-d')] = [
                        'id' => $reservationTrouvee->id,
                        'client' => $reservationTrouvee->customer_name ?? 'Client Événement',
                        'type' => $isPassage ? 'passage' : 'sejour',
                        'url' => $urlSecurisee, // 🔥 Transmet l'URL valide générée par le cœur de Filament
                        // Sécurité : Seul le super_admin verra les liens cliquables d'édition
                        'cliquable' => Auth::user()?->hasRole('super_admin') ?? false
                    ];
                } else {
                    $planningChambre[$jour->format('Y-m-d')] = null;
                }
            }

            $this->grilleOccupation[] = [
                'chambre_number' => $chambre->number,
                'chambre_type'   => $chambre->roomType->name ?? 'Standard',
                'planning'       => $planningChambre,
            ];
        }
    }
}
