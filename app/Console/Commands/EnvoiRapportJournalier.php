<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnvoiRapportJournalier extends Command
{
    protected $signature = 'rapport:send-proprietaire';
    protected $description = 'Calcule et envoie le bilan financier du jour au propriétaire';

    public function handle()
    {
        $aujourdhui = Carbon::today();

        // 1. CALCULS DES RECETTES COMPTABLES
        $recetteHotel = (float) DB::table('payments')->where('payment_type', 'chambre')->whereDate('paid_at', $aujourdhui)->sum('amount');
        $recetteSalle = (float) DB::table('payments')->where('payment_type', 'salle')->whereDate('paid_at', $aujourdhui)->sum('amount');
        $recetteResto = (float) DB::table('payments')->where('payment_type', 'restauration')->whereDate('paid_at', $aujourdhui)->sum('amount');
        $totalGeneral = $recetteHotel + $recetteSalle + $recetteResto;

        // STATISTIQUES LOGISTIQUES
        $chambresLouees = DB::table('bookings')->whereDate('check_in', $aujourdhui)->count();
        $evenementsValidess = DB::table('event_bookings')->whereDate('start_time', $aujourdhui)->count();
        $commandesResto = DB::table('catering_orders')->whereDate('created_at', $aujourdhui)->count();

        // 2. RÉDACTION DES DEUX VERSIONS DE MESSAGES
        $messageHtml = "🔔 <b>RAPPORT D'ACTIVITÉ - COMPLEXE HÔTELIER</b>\n";
        $messageHtml .= "📅 Date : " . $aujourdhui->format('d/m/Y') . "\n\n";
        $messageHtml .= "💰 <b>CHIFFRE D'AFFAIRES :</b>\n";
        $messageHtml .= "• 🏨 Hôtel : " . number_format($recetteHotel, 0, '.', ' ') . " FCFA\n";
        $messageHtml .= "• 🏢 Salles : " . number_format($recetteSalle, 0, '.', ' ') . " FCFA\n";
        $messageHtml .= "• 🍽️ Resto : " . number_format($recetteResto, 0, '.', ' ') . " FCFA\n";
        $messageHtml .= "👉 <b>TOTAL : " . number_format($totalGeneral, 0, '.', ' ') . " FCFA</b>\n\n";
        $messageHtml .= "📊 <b>LOGISTIQUE :</b>\n";
        $messageHtml .= "• 🛏️ Chambres : " . $chambresLouees . "\n";
        $messageHtml .= "• 🏢 Séminaires : " . $evenementsValidess . "\n";
        $messageHtml .= "• 🍳 Cuisine : " . $commandesResto . "\n\n";
        $messageHtml .= "🟢 <i>Système Stable Localhost.</i>";

        $messageBrut = strip_tags($messageHtml);

        // 3. RECUPERATION DES CLÉS DE SÉCURITÉ DU FICHIER .ENV
        $telDestinataire = env('PROPRIETAIRE_SMS', '+2250584365858');
        $twilioSid = env('TWILIO_ACCOUNT_SID', 'AC300ed63a9eb812f13b0d205856bdd686');
        $twilioToken = env('TWILIO_AUTH_TOKEN', 'af5d195314a993bccb66ba0786f24876');
        $twilioNum = env('TWILIO_NUMBER', '+14155238886');

        $this->info("Démarrage de la distribution multi-canal...");

               // ==========================================
        // CANAL 1 : ENVOI TELEGRAM SCELLÉ
        // ==========================================
        try {
            // FIX NETTOYAGE ABSOLU DE L'URL TELEGRAM
            $urlTelegram = "https://telegram.org";

            Http::withoutVerifying()->post($urlTelegram, [
                'chat_id' => '7598516084',
                'text' => $messageHtml,
                'parse_mode' => 'HTML'
            ]);
            $this->info("🟢 Requête acheminée vers Telegram.");
        } catch (\Exception $e) {
            Log::error("Erreur Telegram en direct : " . $e->getMessage());
        }

        // ==========================================
        // CANAL 2 : ENVOI WHATSAPP SCELLÉ
        // ==========================================
        try {
            // FIX NETTOYAGE : Extraction propre pour éviter d'embarquer "twilio.com" si présent dans le .env
            $netSid = str_replace(['https://', 'twilio.com', '/'], '', $twilioSid);
            $urlTwilioWhatsapp = "https://twilio.com" . trim($netSid) . "/Messages.json";

            Http::withoutVerifying()
                ->withBasicAuth(trim($netSid), trim($twilioToken))
                ->asForm()
                ->post($urlTwilioWhatsapp, [
                    'From' => "whatsapp:" . $twilioNum,
                    'To'   => "whatsapp:" . $telDestinataire,
                    'Body' => $messageBrut,
                ]);
            $this->info("🟢 Requête acheminée vers WhatsApp Twilio.");
        } catch (\Exception $e) {
            Log::error("Erreur WhatsApp en direct : " . $e->getMessage());
        }

        // ==========================================
        // CANAL 3 : ENVOI SMS CELLULAIRE SCELLÉ
        // ==========================================
        try {
            $netSid = str_replace(['https://', 'twilio.com', '/'], '', $twilioSid);
            $urlTwilioSms = "https://twilio.com" . trim($netSid) . "/Messages.json";

            Http::withoutVerifying()
                ->withBasicAuth(trim($netSid), trim($twilioToken))
                ->asForm()
                ->post($urlTwilioSms, [
                    'From' => $twilioNum,
                    'To'   => $telDestinataire,
                    'Body' => $messageBrut,
                ]);
            $this->info("🟢 Requête acheminée vers le réseau SMS.");
        } catch (\Exception $e) {
            Log::error("Erreur SMS en direct : " . $e->getMessage());
        }

    }
}
