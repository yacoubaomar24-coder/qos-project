<?php
// app/Filament/Widgets/MetricsWidget.php

namespace App\Filament\Widgets;

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

    public string $period  = 'today';
    public array $metrics  = [];

    public function mount(): void
    {
        $this->metrics = $this->getMetrics();
    }

    #[\Livewire\Attributes\On('periodChanged')]
    public function updatePeriod(string $period): void
    {
        $this->period  = $period;
        $this->metrics = $this->getMetrics();
    }

    public function getMetrics(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $votesQuery = Vote::query();
        $sitesQuery = Site::query()->where('statut', true);

        // Filtre par période
        match ($this->period) {
            'today' => $votesQuery->whereDate('created_at', today()),
            'week'  => $votesQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $votesQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'year'  => $votesQuery->whereYear('created_at', now()->year),
            default => $votesQuery->whereDate('created_at', today()),
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
        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $siteIds   = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $votesQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $siteIds  = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $votesQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            $votesQuery->where('site_id', $user->site_id);
            $sitesQuery->where('id', $user->site_id);
        }

        $total      = (clone $votesQuery)->count();
        $satisfaits = (clone $votesQuery)->where('niveau', 'satisfait')->count();
        $taux       = $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0;
        $sitesActifs = (clone $sitesQuery)->count();

        // Meilleur et moins bon site
        $siteIds     = (clone $sitesQuery)->pluck('id');
        $meilleur    = $this->getBestSite($siteIds, 'DESC');
        $moinsbon    = $this->getBestSite($siteIds, 'ASC');

        return [
            'total'        => $total,
            'satisfaits'   => $satisfaits,
            'taux'         => $taux,
            'sitesActifs'  => $sitesActifs,
            'meilleur'     => $meilleur,
            'moinsbon'     => $moinsbon,
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
}