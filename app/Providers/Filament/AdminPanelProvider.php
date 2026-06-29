<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        // ✅ Charger la config de l'utilisateur connecté
        \Filament\Facades\Filament::serving(function () use ($panel) {
            /** @var \App\Models\Utilisateur|null $user */
            $user   = filament()->auth()->user();
            $config = \App\Models\Configuration::where('created_by', $user?->id)->first();

            if (!$config) return;

            $panel->brandName($config->organisation_nom);

            if ($config->organisation_logo &&
                \Illuminate\Support\Facades\Storage::disk('public')->exists($config->organisation_logo)) {

                $logoUrl = \Illuminate\Support\Facades\Storage::url($config->organisation_logo);
                $nom     = $config->organisation_nom;

                $panel->brandLogo(fn() => view('filament.brand-logo', [
                    'url' => $logoUrl,
                    'nom' => $nom,
                ]));
            }

            if ($config->couleur_primaire) {
                $panel->colors([
                    'primary' => \Filament\Support\Colors\Color::hex($config->couleur_primaire),
                ]);
            }
             
        });

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
                //\App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\SiteDetails::class,
                \App\Filament\Pages\Statistics::class,
                \App\Filament\Pages\Alertes::class,
                \App\Filament\Pages\Rapports::class,
                \App\Filament\Pages\Parametres::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //\App\Filament\Widgets\PeriodFilter::class,
                //\App\Filament\Widgets\StatsOverview::class,  
                //\App\Filament\Widgets\MapWidget::class,   
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
                ),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                function (): HtmlString {
                    // Compter les alertes nouvelles
                    $user = filament()->auth()->user();
                    if (!$user instanceof \App\Models\Utilisateur) return new HtmlString('');

                    // Sites accessibles
                    $siteIds = \App\Models\Site::where('created_by', $user->id)->pluck('id');

                    // Alertes nouvelles non vues
                    $count = \App\Models\Alerte::whereIn('site_id', $siteIds)
                        ->where('statut', 'nouvelle')
                        ->count();
                        if ($count === 0) {
                            $badge = '';
                        } else {
                            $badge = "<span style='
                                position:absolute; top:-6px; right:-6px;
                                background:#ef4444; color:white;
                                font-size:10px; font-weight:700;
                                width:18px; height:18px;
                                border-radius:50%;
                                display:flex; align-items:center; justify-content:center;
                                line-height:1;
                            '>{$count}</span>";
                        }

                        $url = url('/admin/alertes');
                        return new HtmlString("
                            <a href='{$url}'
                            style='position:relative; display:inline-flex; align-items:center;
                                    text-decoration:none; margin-right:12px;
                                    padding:6px; border-radius:8px;
                                    color:" . ($count > 0 ? '#ef4444' : '#6b7280') . ";'
                            title='{$count} alerte(s) nouvelle(s)'>
                                <svg style='width:22px; height:22px;' fill='none' viewBox='0 0 24 24'
                                    stroke='currentColor' stroke-width='2'>
                                    <path stroke-linecap='round' stroke-linejoin='round'
                                        d='M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'/>
                                </svg>
                                {$badge}
                            </a>
                        ");
                }
            )
            //->brandLogo(fn () => view('filament.titre'))
            ->brandName('Collecte de Satisfaction Client')
            ->globalSearch(false)                   // Désactiver la recherche globale
            ->sidebarCollapsibleOnDesktop()
            //->sidebarFullyCollapsibleOnDesktop()
            ;
    }
}
