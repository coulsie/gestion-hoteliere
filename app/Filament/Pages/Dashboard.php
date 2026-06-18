<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    /**
     * Configuration stricte de la grille par propriété directe.
     * Évite les erreurs de compatibilité de méthode PHP.
     */
    protected int | string | array $columns = [
        'default' => 1,
        'md' => 2, // Force l'affichage sur 2 colonnes horizontales
    ];
}
