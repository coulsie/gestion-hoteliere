<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\View\PanelsRenderHook; // 🔥 IMPORTATION DU SYSTEME DE HOOK V5
use Illuminate\Support\Facades\Blade; // 🔥 REQUIS POUR LE RENDU DU LINK CSS
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

            // 🔥 CHANGER LE LOGO : Remplace définitivement le texte "Laravel" par le nom de votre hôtel
            ->brandName('HOTEL BEL HORIZON')

            // 🔥 AJOUT : Intègre l'image de votre logo au-dessus du menu latéral
            ->brandLogo(asset('images/logo-hotel.png'))
            // 🎨 PALETTE ÉCLATANTE HAUTE SÉLECTION (Couleurs néons ultra contrastées)
            ->colors([
                'primary' => Color::Fuchsia,
                'success' => Color::Lime,
                'danger'  => Color::Red,
                'warning' => Color::Yellow,
                'info'    => Color::Cyan,
                'gray'    => Color::Slate,
            ])

            // Force la barre supérieure et certains composants à adopter le style contrasté
            ->darkMode(true)

            ->sidebarCollapsibleOnDesktop()

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class, // 🔥 FIX : Retrait de "Pages\" car la classe est déjà importée au sommet du fichier
                \App\Filament\Pages\RapportFinancier::class,
            ])

            // 🔥 CONFIGURATION VALIDE EN V5 : Injection du CSS via un Render Hook au niveau du Head HTML
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<link rel="stylesheet" href="{{ asset(\'css/custom-neon.css\') }}">'),
            )

            // 🚀 INJECTION DES DOSSIERS AVEC TITRES COLORELS DANS L'INTERFACE
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion Hôtelière')
                    ->icon('heroicon-o-building-office-2'),

                NavigationGroup::make()
                    ->label('Gestion des Espaces')
                    ->icon('heroicon-o-squares-2x2'),

                NavigationGroup::make()
                    ->label('Services Restauration')
                    ->icon('heroicon-o-cake'),

                NavigationGroup::make()
                    ->label('Gestion Financière')
                    ->icon('heroicon-o-banknotes'),

                NavigationGroup::make()
                    ->label('Configuration')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])

            ->widgets([
                \App\Filament\Widgets\PaymentMethodsChart::class,
                \App\Filament\Widgets\RevenueChart::class,
                \App\Filament\Widgets\StatsOverview::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
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
