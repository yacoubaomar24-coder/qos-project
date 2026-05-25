<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->authGuard('web')
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber, // Couleur principale (ex: boutons, liens)
                'secondary' => Color::Gray, // Couleur secondaire (ex: arrière-plan, éléments de navigation)
                'success' => Color::Green, // Couleur de succès (ex: messages de confirmation)
                'danger' => Color::Red, // Couleur de danger (ex: messages d'erreur)
                'warning' => Color::Yellow, // Couleur d'avertissement (ex: messages d'alerte)
                'info' => Color::Blue, // Couleur d'information (ex: messages informatifs)
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->widgets([
                \App\Filament\Widgets\PeriodFilter::class,
                \App\Filament\Widgets\StatsOverview::class,  
                \App\Filament\Widgets\MapWidget::class,   
            ])
            ->middleware([
                EncryptCookies::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
                // Middleware personnalisé pour vérifier le statut de l'utilisateur
                \App\Http\Middleware\CheckUserStatut::class, 
            ])
            // Afficher les messages d'erreur de statut inactif sur la page de login
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn(): HtmlString => new HtmlString(
                    session('error')
                        ? '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">'
                            . e(session('error')) // e() protège contre les injections XSS dans le message affiché.
                            . '</div>'
                        : ''
                )
            )
            ->brandName('Satisfaction Client')
            //->brandLogo(asset('images/FAST.jpg'))  // public/images/logo.png
            ->globalSearch(false)                   // Désactiver la recherche globale
            ->sidebarCollapsibleOnDesktop()
            //->sidebarFullyCollapsibleOnDesktop()
            ;
    }
}
