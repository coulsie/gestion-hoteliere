<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envoie une notification ou un rapport d'activité directement sur le WhatsApp du propriétaire
     */
    public static function envoyerWhatsApp(string $message): bool
    {
        $sid = env('TWILIO_ACCOUNT_SID', 'AC300ed63a9eb812f13b0d205856bdd686');
        $token = env('TWILIO_AUTH_TOKEN', 'af5d195314a993bccb66ba0786f24876');
        $numeroTwilio = env('TWILIO_NUMBER', 'whatsapp:+14155238886');

        $numeroProprietaire = "whatsapp:" . env('PROPRIETAIRE_WHATSAPP', '+2250584365858');

        try {
            // FIX NETTOYAGE ABSOLU URL STRICTE DE PRODUCTION TWILIO WHATSAPP
            $url = "https://twilio.com{$sid}/Messages.json";

            $response = Http::withoutVerifying()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $numeroTwilio,
                    'To'   => $numeroProprietaire,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Échec Twilio WhatsApp API - Code: " . $response->status() . " - " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Erreur envoi WhatsApp Twilio : " . $e->getMessage());
            return false;
        }
    }
}
