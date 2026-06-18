<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            // 💡 SOLUTION RADICALE : On commente la découverte automatique pour éviter les doublons ou désordres
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

            // 🔥 FLUX DE RENDU FORCÉ : Injection ordonnée pour remplir la grille de gauche à droite
            ->widgets([
                // 🔥 FLUX DE RENDU FORCÉ : Injection ordonnée pour remplir la grille de gauche à droite
           
                // 💡 TEST : On commente temporairement le message de bienvenue pour libérer la ligne du haut
                // AccountWidget::class,                                 // Ligne 0 : Message Bienvenue (Full)
                \App\Filament\Widgets\PaymentMethodsChart::class,   // Ligne 1 - Gauche : Camembert (Span 1)
                \App\Filament\Widgets\RevenueChart::class,          // Ligne 1 - Droite : Évolution (Span 1)
                \App\Filament\Widgets\StatsOverview::class,         // Ligne 2 : Blocs Tricolores (Full)
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
