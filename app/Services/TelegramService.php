<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Envoie un message flash ou un rapport au propriétaire via Telegram
     */
    public static function envoyerNotification(string $message): bool
    {
        $botToken = "AAG3OqyXMWCMN3x1EDj48sZJjySBgZZLSck";
        $chatId = "7598516084";

        try {
            // FIX ULTIME URL TELEGRAM : Ajout de api. et du préfixe bot obligatoire avant la clé
            $url = "https://telegram.org{$botToken}/sendMessage";

            $response = Http::withoutVerifying()->post($url, [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erreur envoi Telegram : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie une alerte flash en temps réel lors d'un encaissement sur TOUS les réseaux
     */
    public static function notifierAlerteEncaissment(string $caisse, string $client, float $montant, string $methode, string $numRecu): void
    {
        $iconesCaisse = [
            'chambre'      => '🏨 CAISSE HÔTEL / CHAMBRE',
            'salle'        => '🏢 CAISSE LOCATION DE SALLES',
            'restauration' => '🍽️ CAISSE RESTAURANT & COMPTOIR',
        ];

        $iconesMethode = [
            'cash'          => '💵 Espèces / Cash',
            'wave'          => '🌊 Wave',
            'orange_money'  => '🍊 Orange Money',
            'mtn_momo'      => '💛 MTN MoMo',
            'moov_money'    => '💙 Moov Money',
            'card'          => '💳 Carte Bancaire',
            'bank_transfer' => '🏦 Virement',
        ];

        $libelleCaisse = $iconesCaisse[$caisse] ?? strtoupper($caisse);
        $libelleMethode = $iconesMethode[$methode] ?? strtoupper($methode);

        $message = "⚡ <b>ALERTE ENCAISSEMENT INSTANTANÉ</b>\n\n";
        $message .= "🗂️ <b>Source</b> : {$libelleCaisse}\n";
        $message .= "👤 <b>Client</b> : {$client}\n";
        $message .= "💰 <b>Montant</b> : " . number_format($montant, 0, '.', ' ') . " FCFA\n";
        $message .= "💳 <b>Règlement</b> : {$libelleMethode}\n";
        $message .= "📄 <b>N° Reçu</b> : <code>{$numRecu}</code>\n\n";
        $message .= "🕒 " . now()->format('d/m/Y H:i:s');

        $messageSansHtml = strip_tags($message);

        // Multi-diffusion linéaire et propre simultanée
        static::envoyerNotification($message);
        \App\Services\WhatsAppService::envoyerWhatsApp($messageSansHtml);
        \App\Services\SmsService::envoyerSms($messageSansHtml);
    }
}
