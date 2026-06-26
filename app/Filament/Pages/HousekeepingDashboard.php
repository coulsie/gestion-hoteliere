<?php

namespace App\Filament\Pages;

use App\Models\Room;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use BackedEnum;

class HousekeepingDashboard extends Page
{
    // Propriétés de navigation (Doivent être statiques)
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Tableau du Ménage';

    protected static ?string $title = 'Suivi du Ménage & Maintenance';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion Interne';

    // 🔥 CORRECTION : Retrait du mot-clé "static" sur la vue
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
}
