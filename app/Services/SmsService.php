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
        $sid = env('TWILIO_ACCOUNT_SID', 'AC300ed63a9eb812f13b0d205856bdd686');
        $token = env('TWILIO_AUTH_TOKEN', 'af5d195314a993bccb66ba0786f24876');
        $numeroExpediteur = env('TWILIO_NUMBER', '+14155238886');

        $numeroProprietaire = env('PROPRIETAIRE_SMS', '+2250584365858');

        try {
            // FIX NETTOYAGE ABSOLU URL STRICTE DE PRODUCTION TWILIO SMS
            $url = "https://twilio.com{$sid}/Messages.json";

            $response = Http::withoutVerifying()
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $numeroExpediteur,
                    'To'   => $numeroProprietaire,
                    'Body' => strip_tags($message),
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Échec Twilio SMS API - Code: " . $response->status() . " - " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Erreur envoi SMS Twilio : " . $e->getMessage());
            return false;
        }
    }
}
