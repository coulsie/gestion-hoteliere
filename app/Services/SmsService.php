<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS classique sur le téléphone du propriétaire
     */
    public static function envoyerSms(string $message): bool
    {
        // Récupération sécurisée des clés depuis le fichier .env
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $numeroExpediteur = env('TWILIO_NUMBER', '+14155238886');

        // Lecture du numéro de téléphone validé sur votre console Twilio
        $numeroProprietaire = env('PROPRIETAIRE_SMS', '+2250584365858');

        if (empty($sid) || str_contains($sid, 'VOTRE_')) {
            Log::warning("SMS annulé : Les clés TWILIO_ACCOUNT_SID ne sont pas configurées ou invalides dans le .env");
            return false;
        }

        try {
            // FIX URL STRICTE : Rétablissement de la véritable URL de l'API Twilio SMS
            $url = "https://twilio.com{$sid}/Messages.json";

            // FIX LOCALHOST : withoutVerifying() force le passage à travers le pare-feu WampServer
            $response = Http::withoutVerifying()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $numeroExpediteur,
                    'To'   => $numeroProprietaire,
                    'Body' => strip_tags($message), // Supprime le HTML pour ne garder que le texte brut
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Échec envoi SMS Twilio - Code HTTP : " . $response->status() . " - Réponse : " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Erreur critique envoi SMS Twilio : " . $e->getMessage());
            return false;
        }
    }
}
