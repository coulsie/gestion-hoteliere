<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use App\Services\SmsService;
use Carbon\Carbon;

class EnvoiRapportJournalier extends Command
{
    protected $signature = 'rapport:send-proprietaire';
    protected $description = 'Calcule et envoie le bilan financier du jour au propriétaire';

    public function handle()
    {
        $aujourdhui = Carbon::today();

        // 1. CALCUL DES RECETTES PAR CAISSE (ALIGNÉ SUR LA COLONNE PHYSIQUE paid_at)
        $recetteHotel = (float) DB::table('payments')
            ->where('payment_type', 'chambre')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $recetteSalle = (float) DB::table('payments')
            ->where('payment_type', 'salle')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $recetteResto = (float) DB::table('payments')
            ->where('payment_type', 'restauration')
            ->whereDate('paid_at', $aujourdhui)
            ->sum('amount');

        $totalGeneral = $recetteHotel + $recetteSalle + $recetteResto;

        // 2. STATISTIQUES OPÉRATIONNELLES (ALIGNÉ SUR LES COLONNES COMPATIBLES AVEC VOS TABLES)
        $chambresLouees = DB::table('bookings')
            ->whereDate('check_in', $aujourdhui)
            ->count();

        $evenementsValidess = DB::table('event_bookings')
            ->whereDate('start_time', $aujourdhui)
            ->count();

        $commandesResto = DB::table('catering_orders')
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

        // Nettoyage des balises HTML pour les canaux au format texte brut (WhatsApp & SMS)
        $messageBrut = strip_tags($texteMessage);

        // 🚀 4. ENVOI MULTI-CANAL SIMULTANÉ SANS INTERRUPTION

        // Canal 1 : Telegram (Supporte et affiche le formatage gras HTML)
        TelegramService::envoyerNotification($texteMessage);

        // Canal 2 : WhatsApp (Via votre passerelle Twilio)
        WhatsAppService::envoyerWhatsApp($messageBrut);

        // Canal 3 : SMS Classique Cellulaire (Texte brut direct)
        SmsService::envoyerSms($messageBrut);

        $this->info('Rapport financier envoyé avec succès sur tous les canaux (Telegram + WhatsApp + SMS) !');

        return Command::SUCCESS;
    }
}
