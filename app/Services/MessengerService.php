<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerService
{
    /**
     * Envoie une alerte ou un rapport financier sur le Messenger privé du propriétaire
     */
    public static function envoyerMessenger(string $message): bool
    {
        // 1. CLÉS SECRÈTES CONFIGURÉES SUR META DEVELOPERS
        $accessToken = "VOTRE_PAGE_ACCESS_TOKEN_META";
        $pageId = "VOTRE_FACEBOOK_PAGE_ID";
        $psidProprietaire = "ID_MESSENGER_DU_PROPRIETAIRE"; // L'identifiant PSID unique du gérant

        try {
            $url = "https://facebook.com{$pageId}/messages?access_token={$accessToken}";

            // FIX LOCALHOST : withoutVerifying() désactive la barrière de sécurité SSL de WampServer
            $response = Http::withoutVerifying()->post($url, [
                'recipient' => [
                    'id' => $psidProprietaire
                ],
                'message' => [
                    'text' => strip_tags($message) // Messenger classique ne supporte pas le HTML brut
                ]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erreur envoi Facebook Messenger : " . $e->getMessage());
            return false;
        }
    }
}
