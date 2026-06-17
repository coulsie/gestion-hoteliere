<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Envoie une notification ou un rapport d'activité directement sur le WhatsApp du propriétaire
     */
    public static function envoyerWhatsApp(string $message): bool
    {
        // 1. VOS CLÉS SECRÈTES TWILIO
        $sid = "VOTRE_TWILIO_ACCOUNT_SID";
        $token = "VOTRE_TWILIO_AUTH_TOKEN";
        $numeroTwilio = "whatsapp:+14155238886"; // Le numéro sandbox fourni par Twilio
        $numeroProprietaire = "whatsapp:+225XXXXXXXXXX"; // Votre numéro WhatsApp (au format international)

        try {
            $url = "https://twilio.com{$sid}/Messages.json";

            // FIX LOCALHOST : withoutVerifying() détruit le blocage SSL de WampServer
            $response = Http::withoutVerifying()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $numeroTwilio,
                    'To' => $numeroProprietaire,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur envoi WhatsApp : " . $e->getMessage());
            return false;
        }
    }
}
