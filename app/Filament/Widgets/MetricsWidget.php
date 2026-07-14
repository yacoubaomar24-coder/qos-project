<?php
// app/Filament/Widgets/MetricsWidget.php

namespace App\Filament\Widgets;

use App\Models\Dispositif;
use App\Models\Vote;
use App\Models\Site;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class MetricsWidget extends Widget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.metrics-widget';
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '5s';

    public string $period  = 'today';
    public array $metrics  = [];
    public array $sitesOptions = [];        // liste des sites

    public array $chartData = [];        // données pour tous les graphiques
    public ?int $selectedSiteId = null;  

    public function mount(): void
    {
        $this->metrics = $this->getMetrics();

        $this->sitesOptions = $this->getSitesOptions();

        $this->selectedSiteId = array_key_first($this->sitesOptions) ?? null;

        $this->period = session('dashboard_period', 'today');

        $this->loadChartData();
    }

    #[\Livewire\Attributes\On('periodChanged')]
    #[\Livewire\Attributes\On('periodeGlobaleChangee')]
    public function updatePeriod(string $period): void
    {
        $this->period  = $period;
        $this->metrics = $this->getMetrics();
        
        //session(['dashboard_period' => $period]);
        $period = request()->query('period', 'today');
        //$this->dispatch('chartDataLoaded');
    }

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

    public function loadChartData(): void
    {
        $siteIds = array_keys($this->sitesOptions);

        $this->chartData = [
            //'evolution' => $this->getEvolution($siteIds),
            'parNiveau' => $this->getParNiveau($siteIds),
            //'classement' => $this->getClassement($siteIds),
            //'heatmap' => $this->getHeatmap(),
        ];

        // Dispatcher toutes les données vers JS
        $this->dispatch('chartDataLoaded', data: $this->chartData);
    }

    public function getMetrics(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $votesQuery = Vote::query();
        $sitesQuery = Site::query()->where('statut', true);
        $totalsitesQuery = Site::query();
        $dispositifsQuery = Dispositif::query()->where('statut', true);
        $totaldispositifsQuery = Dispositif::query();

        // Filtre par période
        /*
        match ($this->period) {
            'today' => $votesQuery->whereDate('created_at', today()),
            'week'  => $votesQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $votesQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year'  => $votesQuery->whereYear('created_at', now()->year),
            default => $votesQuery->whereDate('created_at', today()),
        };*/

        //$period = session('dashboard_period', request()->query('period', 'today'));
        $period = request()->query('period', 'today');
        //$period = $this->period ?? session('dashboard_period', 'today');

        match ($period) {
            'today' => $votesQuery->whereDate('created_at', today()),
            'week'  => $votesQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $votesQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year'  => $votesQuery->whereYear('created_at', now()->year),
            default => $votesQuery->whereMonth('created_at', now()->today)->whereYear('created_at', now()->year),
        };

        // Filtre par rôle
        if ($user->hasRole('Admin')) {
            // tout
        } elseif ($user->hasRole('Super admin')) {
            $creatorIds = array_merge([$user->id],
                Utilisateur::where('created_by', $user->id)->where('role', 'Admin national')->pluck('id')->toArray()
            );
            $siteIds = Site::whereIn('created_by', $creatorIds)->pluck('id');
            $votesQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('created_by', $creatorIds);
            $totalsitesQuery->whereIn('created_by', $creatorIds);
            $dispositifsQuery->whereIn('created_by', $creatorIds);
            $totaldispositifsQuery->whereIn('created_by', $creatorIds);
        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $siteIds   = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $votesQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('ville_id', $villeIds);
            $totalsitesQuery->whereIn('ville_id', $villeIds);
            $dispositifsQuery->whereIn('site_id', $siteIds);
            $totaldispositifsQuery->whereIn('site_id', $siteIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $siteIds  = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $votesQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('ville_id', $villeIds);
            $totalsitesQuery->whereIn('ville_id', $villeIds);
            $dispositifsQuery->whereIn('site_id', $siteIds);
            $totaldispositifsQuery->whereIn('site_id', $siteIds);
        } elseif ($user->hasRole('Admin de site')) {
            $votesQuery->where('site_id', $user->site_id);
            $sitesQuery->where('id', $user->site_id);
            $totalsitesQuery->where('id', $user->site_id);
            $dispositifsQuery->where('id', $user->dispositif_id);
            $totaldispositifsQuery->where('id', $user->dispositif_id);
        }

        $total      = (clone $votesQuery)->count();
        $satisfaits = (clone $votesQuery)->where('niveau', 'satisfait')->count();
        $insatisfaits = (clone $votesQuery)->where('niveau', 'insatisfait')->count();
        $taux       = $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0;
        $taux_insatisfait       = $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0;
        $sitesActifs = (clone $sitesQuery)->count();
        $sitesTotals = (clone $totalsitesQuery)->count();
        $dispositifsActifs = (clone $dispositifsQuery)->count();
        $dispositifsTotals = (clone $totaldispositifsQuery)->count();

        // Meilleur et moins bon site
        $siteIds     = (clone $sitesQuery)->pluck('id');
        $meilleur    = $this->getBestSite($siteIds, 'DESC');
        $moinsbon    = $this->getBestSite($siteIds, 'ASC');

        // 4 meilleurs sites
        //$metrics['sites'] = $this->getSitesPerformance($siteIds, 'DESC');
        $sites = $this->getSitesPerformance($siteIds, 'DESC');

        return [
            'total'        => $total,
            'satisfaits'   => $satisfaits,
            'insatisfaits'   => $insatisfaits,
            'taux'         => $taux,
            'taux_insatisfait' => $taux_insatisfait,
            'sitesActifs'  => $sitesActifs,
            'sitesTotals'  => $sitesTotals,
            'meilleur'     => $meilleur,
            'moinsbon'     => $moinsbon,
            'sites'     => $sites,
            'dispositifsActifs'  => $dispositifsActifs,
            'dispositifsTotals'  => $dispositifsTotals,
        ];
    }

    private function getBestSite($siteIds, string $order): array
    {
        $site = Vote::whereIn('site_id', $siteIds)
            ->select('site_id', DB::raw('COUNT(*) as total, SUM(CASE WHEN niveau = "satisfait" THEN 1 ELSE 0 END) as satisfaits'))
            ->groupBy('site_id')
            ->orderByRaw("(satisfaits * 100.0 / total) {$order}")
            ->first();

        if (!$site) return ['nom' => 'N/A', 'taux' => 0];

        return [
            'nom'  => Site::find($site->site_id)?->nom ?? 'N/A',
            'taux' => $site->total > 0 ? round(($site->satisfaits / $site->total) * 100, 1) : 0,
        ];
    }

    
    private function getParNiveau(array $siteIds): array
    {
        
        $query = Vote::whereIn('site_id', $siteIds);
        //$query = Vote::query();

        //$period = $this->period ?? session('dashboard_period', 'today');
        //$period = session('dashboard_period', $this->period ?? 'today');
        //$period = session('dashboard_period', request()->query('period', 'today'));

        //$period = $this->period ?? 'today';

        $period = request()->query('period', 'today');

        match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year'  => $query->whereYear('created_at', now()->year),
            default => $query->whereDate('created_at', now()->today()),
            //default => $query->whereMonth('created_at', now()->today)->whereYear('created_at', now()->year),
        };

        $total        = (clone $query)->count();
        $satisfaits   = (clone $query)->where('niveau', 'satisfait')->count();
        $moyens      = (clone $query)->where('niveau', 'moyen')->count();
        $insatisfaits = (clone $query)->where('niveau', 'insatisfait')->count();

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

    private function getSitesPerformance($siteIds, string $order = 'DESC'): array
    {
        $votes = Vote::whereIn('site_id', $siteIds)
            ->select(
                'site_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN niveau = "satisfait" THEN 1 ELSE 0 END) as satisfaits')
            )
            ->groupBy('site_id')
            ->get()
            ->keyBy('site_id');

        return Site::whereIn('id', $siteIds)
            ->get()
            ->map(function ($site) use ($votes) {
                $vote = $votes->get($site->id);

                $total = $vote?->total ?? 0;
                $satisfaits = $vote?->satisfaits ?? 0;

                return [
                    'id' => $site->id,
                    'nom' => $site->nom ?? 'N/A',
                    'total' => $total,
                    'satisfaits' => $satisfaits,
                    'taux' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy([
                ['taux', strtolower($order) === 'asc' ? 'asc' : 'desc'],
            ])
            ->values()
            ->toArray();
    }
}