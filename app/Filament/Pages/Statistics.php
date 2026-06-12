<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Utilisateur;
use Filament\Pages\Page;

class Statistics extends Page
{
    protected static ?string $navigationLabel = 'Statistiques & Analyses';
    protected static ?string $title = 'Statistiques & Analyses';
    protected static ?int $navigationSort  = 3;  // 3 après dashbord 1, Vue par site : 2

    // Laravel cherche resources/views/filament/pages/statistics.blade.php
    protected string $view = 'filament.pages.statistics';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-pie';
    }

    // -----------------------------------------------
    // Propriétés publiques — état de la page
    // -----------------------------------------------
    public string $period = 'week';      // période sélectionnée
    public ?int $selectedSiteId = null;      // site sélectionné pour heatmap
    public array $sitesOptions = [];        // liste des sites
    public array $chartData = [];        // données pour tous les graphiques

    public function mount(): void
    {
        // Charger les sites accessibles selon le rôle
        $this->sitesOptions = $this->getSitesOptions();
        $this->selectedSiteId = array_key_first($this->sitesOptions) ?? null;

        // Charger toutes les données
        $this->loadChartData();
    }

    // -----------------------------------------------
    // Charger les sites selon le rôle connecté
    // -----------------------------------------------
    private function getSitesOptions(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $query = Site::query()->where('statut', true);

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

        return $query->pluck('nom', 'id')->toArray();
    }

    // -----------------------------------------------
    // Charger toutes les données des graphiques
    // -----------------------------------------------
    public function loadChartData(): void
    {
        $siteIds = array_keys($this->sitesOptions);

        $this->chartData = [
            'evolution' => $this->getEvolution($siteIds),
            'parNiveau' => $this->getParNiveau($siteIds),
            'classement' => $this->getClassement($siteIds),
            'anomalies' => $this->getAnomalies($siteIds),
            'heatmap' => $this->getHeatmap(),
        ];

        // Dispatcher toutes les données vers JS
        $this->dispatch('chartDataLoaded', data: $this->chartData);
    }

    // -----------------------------------------------
    // 1. Évolution temporelle — courbe par période
    // -----------------------------------------------
    private function getEvolution(array $siteIds): array
    {
        $data = [];

        match ($this->period) {
            // 24 dernières heures — par heure
            'day' => (function() use (&$data, $siteIds) {
                for ($i = 23; $i >= 0; $i--) {
                    $heure = now()->subHours($i);
                    $total = Vote::whereIn('site_id', $siteIds)
                        ->whereBetween('created_at', [
                            $heure->copy()->startOfHour(),
                            $heure->copy()->endOfHour(),
                        ])->count();
                    $satisfaits = Vote::whereIn('site_id', $siteIds)
                        ->whereBetween('created_at', [
                        $heure->copy()->startOfHour(),
                        $heure->copy()->endOfHour(),
                    ])
                    ->where('niveau', 'satisfait')->count();
                    $data[] = [
                        'label' => $heure->format('H:i'),
                        'total' => $total,
                        'taux' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),

            // 7 derniers jours — par jour
            'week' => (function() use (&$data, $siteIds) {
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $total = Vote::whereIn('site_id', $siteIds)
                        ->whereDate('created_at', $date)->count();
                    $satisfaits = Vote::whereIn('site_id', $siteIds)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'satisfait')->count();
                    $data[] = [
                        'label' => $date->format('d/m'),
                        'total' => $total,
                        'taux' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),

            // 30 derniers jours — par jour
            'month' => (function() use (&$data, $siteIds) {
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $total = Vote::whereIn('site_id', $siteIds)
                        ->whereDate('created_at', $date)->count();
                    $satisfaits = Vote::whereIn('site_id', $siteIds)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'satisfait')->count();
                    $data[] = [
                        'label' => $date->format('d/m'),
                        'total' => $total,
                        'taux'  => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),

            // 12 derniers mois — par mois
            default => (function() use (&$data, $siteIds) {
                for ($i = 11; $i >= 0; $i--) {
                    $mois = now()->subMonths($i);
                    $total = Vote::whereIn('site_id', $siteIds)
                        ->whereYear('created_at', $mois->year)
                        ->whereMonth('created_at', $mois->month)->count();
                    $satisfaits = Vote::whereIn('site_id', $siteIds)
                        ->whereYear('created_at', $mois->year)
                        ->whereMonth('created_at', $mois->month)
                        ->where('niveau', 'satisfait')->count();
                    $data[] = [
                        'label' => $mois->format('M Y'),
                        'total' => $total,
                        'taux'  => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),
        };

        return $data;
    }

    // -----------------------------------------------
    // 2. Répartition par niveau — histogramme global
    // -----------------------------------------------
    private function getParNiveau(array $siteIds): array
    {
        $total = Vote::whereIn('site_id', $siteIds)->count();
        $satisfaits = Vote::whereIn('site_id', $siteIds)->where('niveau', 'satisfait')->count();
        $moyens = Vote::whereIn('site_id', $siteIds)->where('niveau', 'moyen')->count();
        $insatisfaits = Vote::whereIn('site_id', $siteIds)->where('niveau', 'insatisfait')->count();

        return [
            'total' => $total,
            'satisfaits' => $satisfaits,
            'moyens' => $moyens,
            'insatisfaits' => $insatisfaits,
            'taux_satisfait' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
            'taux_moyen' => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
            'taux_insatisfait' => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
        ];
    }

    // -----------------------------------------------
    // 3. Classement des sites — du meilleur au moins bon
    // -----------------------------------------------
    private function getClassement(array $siteIds): array
    {
        $classement = [];

        foreach ($siteIds as $siteId) {
            $total = Vote::where('site_id', $siteId)->count();
            $satisfaits = Vote::where('site_id', $siteId)->where('niveau', 'satisfait')->count();
            $taux = $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0;

            $classement[] = [
                'nom' => Site::find($siteId)?->nom ?? 'N/A',
                'total' => $total,
                'taux' => $taux,
                'color' => $taux >= 70 ? '#22c55e' : ($taux >= 40 ? '#f59e0b' : '#ef4444'),
            ];
        }

        // Trier du meilleur au moins bon
        usort($classement, fn($a, $b) => $b['taux'] <=> $a['taux']);

        return $classement;
    }

    // -----------------------------------------------
    // 4. Détection d'anomalies — chute soudaine
    // Alerte si le taux d'aujourd'hui est inférieur
    // de plus de 20% par rapport à la moyenne des 7 derniers jours
    // -----------------------------------------------
    private function getAnomalies(array $siteIds): array
    {
        $anomalies = [];

        foreach ($siteIds as $siteId) {
            $site = Site::find($siteId);
            if (!$site) continue;

            // Taux aujourd'hui
            $totalToday = Vote::where('site_id', $siteId)->whereDate('created_at', today())->count();
            $satisfaitsToday = Vote::where('site_id', $siteId)->whereDate('created_at', today())
                ->where('niveau', 'satisfait')->count();
            $tauxToday = $totalToday > 0 ? round(($satisfaitsToday / $totalToday) * 100, 1) : null;

            // Taux moyen des 7 derniers jours (hors aujourd'hui)
            $totalWeek = Vote::where('site_id', $siteId)
                ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->subDay()->endOfDay()])
                ->count();
            $satisfaitsWeek = Vote::where('site_id', $siteId)
                ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->subDay()->endOfDay()])
                ->where('niveau', 'satisfait')->count();
            $tauxWeek = $totalWeek > 0 ? round(($satisfaitsWeek / $totalWeek) * 100, 1) : null;

            // Détecter l'anomalie — chute de plus de 20 points
            if ($tauxToday !== null && $tauxWeek !== null) {
                $chute = $tauxWeek - $tauxToday;
                if ($chute >= 20) {
                    $anomalies[] = [
                        'site' => $site->nom,
                        'taux_today' => $tauxToday,
                        'taux_week' => $tauxWeek,
                        'chute' => round($chute, 1),
                        'niveau' => $chute >= 40 ? 'critique' : 'warning',
                    ];
                }
            }
        }

        // Trier par chute décroissante
        usort($anomalies, fn($a, $b) => $b['chute'] <=> $a['chute']);

        return $anomalies;
    }

    // -----------------------------------------------
    // 5. Heatmap horaire — insatisfaction par heure/jour
    // Matrice 7 jours × 24 heures
    // -----------------------------------------------
    private function getHeatmap(): array
    {
        if (!$this->selectedSiteId) return [];

        $heatmap = [];
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

        // Pour chaque jour de la semaine (0=lundi, 6=dimanche)
        for ($jour = 0; $jour <= 6; $jour++) {
            $ligneJour = ['jour' => $jours[$jour], 'heures' => []];

            // Pour chaque heure de la journée
            for ($heure = 0; $heure <= 23; $heure++) {
                $total = Vote::where('site_id', $this->selectedSiteId)
                    //->whereRaw('DAYOFWEEK(created_at) = ?', [$jour + 2]) // MySQL: 1=dim, 2=lun
                    ->whereRaw("strftime('%w', created_at) = ?", [$jour + 2]) // SQLite: 0=dim, 1=lun
                    ->whereHour('created_at', $heure)
                    ->count();

                $insatisfaits = Vote::where('site_id', $this->selectedSiteId)
                    //->whereRaw('DAYOFWEEK(created_at) = ?', [$jour + 2])
                    ->whereRaw("strftime('%w', created_at) = ?", [$jour + 2])
                    ->whereHour('created_at', $heure)
                    ->where('niveau', 'insatisfait')
                    ->count();

                $taux = $total > 0 ? round(($insatisfaits / $total) * 100) : 0;

                $ligneJour['heures'][] = [
                    'heure' => $heure,
                    'total' => $total,
                    'taux'  => $taux, // taux d'insatisfaction
                    // Couleur selon le taux d'insatisfaction
                    'color' => $taux === 0
                        ? '#f9fafb'                          // gris clair — aucun vote
                        : ($taux >= 60 ? '#ef4444'           // rouge — fort taux insatisfaction
                        : ($taux >= 30 ? '#f59e0b'           // orange — taux moyen
                        : '#22c55e')),                       // vert — faible insatisfaction
                ];
            }

            $heatmap[] = $ligneJour;
        }

        return $heatmap;
    }

    // -----------------------------------------------
    // Actions publiques — appelées depuis le Blade
    // -----------------------------------------------
    public function changePeriod(string $period): void
    {
        $this->period = $period;
        $this->loadChartData();
    }

    public function changeHeatmapSite(int $siteId): void
    {
        $this->selectedSiteId = $siteId;
        $this->loadChartData();
    }
}