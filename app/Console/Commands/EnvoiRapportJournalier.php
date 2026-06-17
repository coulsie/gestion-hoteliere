<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use Carbon\Carbon;

class EnvoiRapportJournalier extends Command
{
    protected $signature = 'rapport:send-proprietaire';
    protected $description = 'Calcule et envoie le bilan financier du jour au propriétaire';

        public function handle()
    {
        $aujourdhui = \Carbon\Carbon::today();

        // 1. CALCUL DES RECETTES PAR CAISSE (ALIGNÉ SUR LA COLONNE PHYSIQUE paid_at)
        $recetteHotel = (float) \Illuminate\Support\Facades\DB::table('payments')
            ->where('payment_type', 'chambre')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $recetteSalle = (float) \Illuminate\Support\Facades\DB::table('payments')
            ->where('payment_type', 'salle')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $recetteResto = (float) \Illuminate\Support\Facades\DB::table('payments')
            ->where('payment_type', 'restauration')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $totalGeneral = $recetteHotel + $recetteSalle + $recetteResto;

        // 2. STATISTIQUES OPÉRATIONNELLES (ALIGNÉ SUR LES COLONNES COMPATIBLES AVEC VOS TABLES)
        $chambresLouees = \Illuminate\Support\Facades\DB::table('bookings')
            ->whereDate('check_in', $aujourdhui)
            ->count();

        $evenementsValidess = \Illuminate\Support\Facades\DB::table('event_bookings')
            ->whereDate('start_time', $aujourdhui)
            ->count();

        $commandesResto = \Illuminate\Support\Facades\DB::table('catering_orders')
            ->whereDate('created_at', $aujourdhui)
            ->count();

        // 3. RÉDACTION DU MESSAGE FORMATÉ
        $texteMessage = "🔔 <b>RAPPORT D'ACTIVITÉ - COMPLEXE HÔTELIER</b>\n";
        $texteMessage .= "📅 Date : " . $aujourdhui->format('d/m/Y') . "\n\n";

        $texteMessage .= "💰 <b>CHIFFRE D'AFFAIRES DU JOUR :</b>\n";
        $texteMessage .= "• 🏨 Caisse Hôtel : " . number_format($recetteHotel, 0, '.', ' ') . " FCFA\n";
        $texteMessage .= "• 🏢 Caisse Salles : " . number_format($recetteSalle, 0, '.', ' ') . " FCFA\n";
        $texteMessage .= "• 🍽️ Caisse Resto : " . number_format($recetteResto, 0, '.', ' ') . " FCFA\n";
        $texteMessage .= "👉 <b>TOTAL ENCAISSÉ : " . number_format($totalGeneral, 0, '.', ' ') . " FCFA</b>\n\n";

        $texteMessage .= "📊 <b>VOLUMES & LOGISTIQUE :</b>\n";
        $texteMessage .= "• 🛏️ Arrivées Chambres : " . $chambresLouees . "\n";
        $texteMessage .= "• 🏢 Événements du jour : " . $evenementsValidess . "\n";
        $texteMessage .= "• 🍳 Commandes Cuisine & Bar : " . $commandesResto . "\n\n";

        $texteMessage .= "🟢 <i>Logiciel opérationnel - Système Stable.</i>";

        // 4. ENVOI VIA LE CANAL TELEGRAM
                // Envoi du bilan financier sur WhatsApp en ignorant le SSL local
        $messageBrut = strip_tags($texteMessage);
        \App\Services\WhatsAppService::envoyerWhatsApp($messageBrut);


        $this->info('Rapport financier envoyé avec succès au propriétaire !');
        return Command::SUCCESS;
    }

}
