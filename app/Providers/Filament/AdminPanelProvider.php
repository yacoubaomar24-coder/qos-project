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
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    private function getSiteIdsParRole(\App\Models\Utilisateur $user): \Illuminate\Support\Collection
    {
        $query = \App\Models\Site::query()->where('statut', true);

        if ($user->hasRole('Super admin')) {
            $adminIds = \App\Models\Utilisateur::where('created_by', $user->id)
                ->where('role', 'Admin national')->pluck('id')->toArray();
            $query->whereIn('created_by', array_merge([$user->id], $adminIds));

        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $query->whereIn('ville_id', $villeIds);

        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $query->whereIn('ville_id', $villeIds);

        } elseif ($user->hasRole('Admin de site')) {
            $query->where('id', $user->site_id);
        }

        return $query->pluck('id');
    }

    private function getConfigParRole(?\App\Models\Utilisateur $user): ?\App\Models\Configuration
    {
        if ($user === null) {
            return null;
        }
        
        // Super admin — sa propre config
        if ($user->hasRole('Super admin')) {
            return \App\Models\Configuration::where('created_by', $user->id)->first();
        }

        // Autres admins — config du Super admin qui les a créés
        // Remonter jusqu'au Super admin via created_by
        $createur = $user;
        $maxNiveaux = 3; // éviter boucle infinie

        for ($i = 0; $i < $maxNiveaux; $i++) {
            if (!$createur->created_by) break;

            $parent = \App\Models\Utilisateur::find($createur->created_by);
            if (!$parent) break;

            if ($parent->hasRole('Super admin')) {
                return \App\Models\Configuration::where('created_by', $parent->id)->first();
            }

            $createur = $parent;
        }

        return null;
    }

    public function panel(Panel $panel): Panel
    {
        // ✅ Charger la config de l'utilisateur connecté
        \Filament\Facades\Filament::serving(function () use ($panel) {
            /** @var \App\Models\Utilisateur|null $user */
            $user   = filament()->auth()->user();
            
            //$config = \App\Models\Configuration::where('created_by', $user?->id)->first();

            //$user   = filament()->auth()->user();

            // ✅ Toujours vider le brand Filament — peu importe la config
            $panel->brandName('');
            $panel->brandLogo(fn() => new \Illuminate\Support\HtmlString(''));
            $panel->brandLogoHeight('0px');

            if (!$user instanceof \App\Models\Utilisateur) return;

            $config = $this->getConfigParRole($user);  // Trouver la config selon le rôle
            
            if (!$config) return;

            //$panel->brandName($config->organisation_nom);

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
            if ($config->couleur_secondaire) {
                $panel->colors([
                    'secondary' => \Filament\Support\Colors\Color::hex($config->couleur_secondaire),
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
                \App\Filament\Pages\Anomalies::class,
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
            // Notifications des alertes et des anomalies
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                function (): HtmlString {
                    // Compter les alertes nouvelles
                    $user = filament()->auth()->user();
                    if (!$user instanceof \App\Models\Utilisateur) return new HtmlString('');
                    if ($user->hasRole('Admin')) return new HtmlString('');

                    // Compter les alertes nouvelles
                    // $siteIds = \App\Models\Site::where('created_by', $user->id)->pluck('id');
                    $siteIds = $this->getSiteIdsParRole($user);

                    $nombreAlertes = \App\Models\Alerte::whereIn('site_id', $siteIds)
                        ->where('statut', 'nouvelle')
                        ->count();

                    // Compter les anomalies du jour
                    $nombreAnomalies = 0;
                    foreach ($siteIds as $siteId) {
                        $totalToday      = \App\Models\Vote::where('site_id', $siteId)
                            ->whereDate('created_at', today())->count();
                        $satisfaitsToday = \App\Models\Vote::where('site_id', $siteId)
                            ->whereDate('created_at', today())
                            ->where('niveau', 'satisfait')->count();
                        $tauxToday = $totalToday > 0 ? round(($satisfaitsToday / $totalToday) * 100, 1) : null;

                        $totalWeek      = \App\Models\Vote::where('site_id', $siteId)
                            ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->subDay()->endOfDay()])
                            ->count();
                        $satisfaitsWeek = \App\Models\Vote::where('site_id', $siteId)
                            ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->subDay()->endOfDay()])
                            ->where('niveau', 'satisfait')->count();
                        $tauxWeek = $totalWeek > 0 ? round(($satisfaitsWeek / $totalWeek) * 100, 1) : null;

                        if ($tauxToday !== null && $tauxWeek !== null && ($tauxWeek - $tauxToday) >= 20) {
                           $nombreAnomalies++;
                        }
                    }

                    // Badge alertes
                    $badgeAlertes = $nombreAlertes > 0
                        ? "<span style='
                            position:absolute; top:-6px; right:-6px;
                            background:#ef4444; color:white;
                            font-size:10px; font-weight:700;
                            min-width:18px; height:18px; padding:0 3px;
                            border-radius:999px;
                            display:flex; align-items:center; justify-content:center;
                            '>" . ($nombreAlertes > 99 ? '99+' : $nombreAlertes) . "</span>"
                        : '';

                    // Badge anomalies
                    $badgeAnomalies = $nombreAnomalies > 0
                            ? "<span style='
                                position:absolute; top:-6px; right:-6px;
                                background:#f59e0b; color:white;
                                font-size:10px; font-weight:700;
                                min-width:18px; height:18px; padding:0 3px;
                                border-radius:999px;
                                display:flex; align-items:center; justify-content:center;
                            '>" . ($nombreAnomalies > 99 ? '99+' : $nombreAnomalies) . "</span>"
                        : '';

                    $urlAlertes    = url('/admin/alertes');
                    $urlAnomalies  = url('/admin/anomalies'); 

                    $colorAlertes   = $nombreAlertes   > 0 ? '#ef4444' : '#6b7280';
                    $colorAnomalies = $nombreAnomalies > 0 ? '#f59e0b' : '#6b7280';

                    return new HtmlString("
                        <div style='display:flex; align-items:center; gap:16px; margin-right:16px;'>
                            <a href='{$urlAlertes}'
                                style='position:relative; display:inline-flex; align-items:center;
                                        text-decoration:none; padding:6px; border-radius:8px;
                                        color:{$colorAlertes};'
                                title='{$nombreAlertes} alerte(s) de seuil'>
                                <svg style='width:22px; height:22px;' fill='none' viewBox='0 0 24 24'
                                    stroke='currentColor' stroke-width='2'>
                                    <path stroke-linecap='round' stroke-linejoin='round'
                                    d='M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'/>
                                </svg>
                                {$badgeAlertes}
                            </a>

                            <a href='{$urlAnomalies}'
                                style='position:relative; display:inline-flex; align-items:center;
                                    text-decoration:none; padding:6px; border-radius:8px;
                                    color:{$colorAnomalies};'
                                title='{$nombreAnomalies} anomalie(s) détectée(s)'>
                                <svg style='width:22px; height:22px;' fill='none' viewBox='0 0 24 24'
                                    stroke='currentColor' stroke-width='2'>
                                    <path stroke-linecap='round' stroke-linejoin='round'
                                    d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/>
                                </svg>
                                {$badgeAnomalies}
                            </a>
                        </div>
                    ");
                }
            )
            //->brandLogo(fn () => view('filament.titre'))
            ->brandName('QoS - System')
            ->globalSearch(false)                   // Désactiver la recherche globale
            //->sidebarCollapsibleOnDesktop()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('260px')
            ->collapsedSidebarWidth('64px')
            //->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('16rem') // Par défaut c'est 20rem (320px). '16rem' correspond à 250px.
            
            // Titre dashbord et période
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                function (): \Illuminate\Support\HtmlString {
                    /** @var \App\Models\Utilisateur|null $user */
                    $user = filament()->auth()->user();

                    if (!$user instanceof \App\Models\Utilisateur) {
                        return new \Illuminate\Support\HtmlString('');
                    }

                    if ($user->hasRole('Admin')) {
                        return new \Illuminate\Support\HtmlString('');
                    }

                    $period = request()->query('period', 'today');

                    return new \Illuminate\Support\HtmlString("
                        <div class='dashboard-topbar-period'>
                            <span class='dashboard-topbar-title'>Dashboard</span>
                            <span class='dashboard-topbar-separator'></span>
                            <label class='dashboard-topbar-label'>
                                Période
                            </label>
                            <select id='global-period-select'
                                onchange='changerPeriodeGlobale(this.value)'
                                class='dashboard-topbar-select'>
                                <option value='today' " . ($period === 'today' ? 'selected' : '') . ">Aujourd'hui</option>
                                <option value='week' " . ($period === 'week' ? 'selected' : '') . ">Cette semaine</option>
                                <option value='month' " . ($period === 'month' ? 'selected' : '') . ">Ce mois</option>
                                <option value='year' " . ($period === 'year' ? 'selected' : '') . ">Cette année</option>
                            </select>
                        </div>

                        <style>
                            .dashboard-topbar-period {
                                position: fixed;
                                top: 18px;
                                left: 290px;
                                z-index: 60;
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                white-space: nowrap;
                                pointer-events: auto;
                            }

                            .dashboard-topbar-title {
                                font-size: 22px;
                                font-weight: 700;
                                color: #111827;
                            }

                            .dashboard-topbar-separator {
                                width: 1px;
                                height: 24px;
                                background: #e5e7eb;
                                display: inline-block;
                            }

                            .dashboard-topbar-label {
                                font-size: 12px;
                                font-weight: 700;
                                color: #9ca3af;
                                text-transform: uppercase;
                                letter-spacing: 0.04em;
                            }

                            .dashboard-topbar-select {
                                border: 1.5px solid #111827;
                                border-radius: 12px;
                                padding: 7px 34px 7px 14px;
                                font-size: 14px;
                                background: #ffffff;
                                color: #374151;
                                cursor: pointer;
                                min-width: 160px;
                            }

                            @media (max-width: 1024px) {
                                .dashboard-topbar-period {
                                    left: 210px;
                                    gap: 8px;
                                }

                                .dashboard-topbar-title {
                                    font-size: 18px;
                                }

                                .dashboard-topbar-select {
                                    min-width: 135px;
                                    font-size: 14px;
                                    padding: 6px 28px 6px 10px;
                                }
                            }

                            @media (max-width: 768px) {
                                .dashboard-topbar-period {
                                    position: static;
                                    margin-left: 8px;
                                    gap: 6px;
                                }

                                .dashboard-topbar-title,
                                .dashboard-topbar-separator,
                                .dashboard-topbar-label {
                                    display: none;
                                }

                                .dashboard-topbar-select {
                                    min-width: 110px;
                                    max-width: 120px;
                                    font-size: 12px;
                                    padding: 5px 24px 5px 8px;
                                    border-radius: 8px;
                                }
                            }
                        </style>

                        <script>
                            function changerPeriodeGlobale(period) {

                                const url = new URL(window.location.href);

                                url.searchParams.set('period', period);

                                window.location.href = url.toString();
                            }
                        </script>
                    ");
                }
            )

            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                function (): \Illuminate\Support\HtmlString {
                    /** @var \App\Models\Utilisateur|null $user */
                    $user   = filament()->auth()->user();
                    /*
                    $config = $user instanceof \App\Models\Utilisateur
                        ? \App\Models\Configuration::where('created_by', $user?->id)->first()
                        : null;*/
                    if (!$user instanceof \App\Models\Utilisateur) {
                        return new \Illuminate\Support\HtmlString('');
                    }

                    $config = $this->getConfigParRole($user);  // Trouver la config selon le rôle
            
                    //$user   = filament()->auth()->user();

                    $nom     = $config?->organisation_nom ?? 'QoS-System';
                    $logoUrl = $config?->organisation_logo
                        ? \Illuminate\Support\Facades\Storage::url($config->organisation_logo)
                        : null;

                    $logoHtml = $logoUrl
                        ? "<img src='{$logoUrl}' style='height:45px; width:auto; object-fit:contain;'>"
                        : "<div style='width:28px; height:28px; background:#f59e0b; border-radius:6px;
                                    display:flex; align-items:center; justify-content:center;
                                    font-weight:700; color:white; font-size:13px;'>
                            " . strtoupper(substr($nom, 0, 1)) . "
                        </div>";

                    return new \Illuminate\Support\HtmlString("
                        <div style='display:flex; align-items:center; gap:10px; 
                            margin-right:-65px; pointer-events:none;overflow:hidden;
                            width:280px;min-width:280px;max-width:280px;'>
                            
                            <a href='" . url('/admin') . "'
                                style='display:flex; align-items:center; gap:10px;
                                        text-decoration:none; pointer-events:auto; min-width:0;'>

                                    <div style='flex-shrink:0;font-size:20px;'>{$logoHtml}</div>

                                    <span style='font-size:16px; font-weight:700; color:#111827;
                                                white-space:nowrap; overflow:hidden;
                                                text-overflow:ellipsis; max-width:200px;'>
                                        {$nom}
                                    </span>

                            </a>
                            <div style='width:1px; height:20px; background: #e5e7eb; 
                                    flex-shrink:0; margin-left:auto; pointer-events:none;'></div>
                        </div>
                    ");
                    
                }
            )
            ;
    }
}
