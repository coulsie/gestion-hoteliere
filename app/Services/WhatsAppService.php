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
        // 1. VOS CLÉS SECRÈTES TWILIO (À remplacer par vos accès Twilio réels)
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $numeroTwilio = "whatsapp:+14155238886"; // Le numéro sandbox fourni par Twilio

        // FIX SÉCURITÉ : Lecture du .env avec votre numéro réel +2250584365858 en valeur de secours par défaut
        $numeroProprietaire = "whatsapp:" . env('PROPRIETAIRE_WHATSAPP', '+2250584365858');

        try {
            // FIX URL DIRECTE : Utilisation de l'adresse API officielle et complète de Twilio pour WhatsApp
            $url = "https://twilio.com{$sid}/Messages.json";

            // FIX LOCALHOST : withoutVerifying() détruit le blocage SSL cURL 60 de WampServer
            $response = Http::withoutVerifying()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $numeroTwilio,
                    'To'   => $numeroProprietaire,
                    'Body' => $message,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erreur envoi WhatsApp Twilio : " . $e->getMessage());
            return false;
        }
    }
}
