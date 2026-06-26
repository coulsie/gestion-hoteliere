<?php


namespace App\Filament\Pages;

use App\Models\Room;
use Filament\Pages\Page;
use Filament\Notifications\Notification; // 🔥 ASSURE LA PRÉSENCE DE CETTE LIGNE CORRECTE
use BackedEnum;

class HousekeepingDashboard extends Page
{
    // Propriétés de navigation (Doivent être statiques)
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Tableau du Ménage';

    protected static ?string $title = 'Suivi du Ménage & Maintenance';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Interne';

    protected string $view = 'filament.pages.housekeeping-dashboard';

    // Récupère toutes les chambres triées par numéro
    public function getChambresProperty()
    {
        return Room::with('roomType')->orderBy('number')->get();
    }
    // Action rapide : Passer la chambre en "Propre"
    public function marquerCommePropre(int $roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update(['housekeeping_status' => 'propre']);

            Notification::make()
                ->title("Chambre N° {$room->number} prête !")
                ->success()
                ->send();
        }
    }

    // Action rapide : Passer la chambre en "Ménage en cours"
    public function démarrerMénage(int $roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update(['housekeeping_status' => 'en_cours']);
        }
    }

    // 🔥 NOUVELLE MÉTHODE : Gère le clic unique sur la cellule pour basculer d'état
    // Gère le clic unique sur la cellule pour basculer d'état
    public function changerEtatChambreAction(int $roomId): void
    {
        $room = Room::find($roomId);
        if (!$room) return;

        // Logique de bascule (Sale -> En cours -> Propre)
        if ($room->housekeeping_status === 'sale') {
            $room->update(['housekeeping_status' => 'en_cours']);
            Notification::make()->title("Chambre N° {$room->number} : Ménage démarré")->warning()->send();
        } elseif ($room->housekeeping_status === 'en_cours') {
            $room->update(['housekeeping_status' => 'propre']);
            Notification::make()->title("Chambre N° {$room->number} est PROPRE et prête !") ->success()->send();
        } elseif ($room->housekeeping_status === 'propre') {
            $room->update(['housekeeping_status' => 'sale']);
            Notification::make()->title("Chambre N° {$room->number} marquée comme SALE")->danger()->send();
        }
    }
}
