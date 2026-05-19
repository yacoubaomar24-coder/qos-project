<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use App\Models\Site;
use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    // Filtre période — partagé avec les autres widgets
    public string $period = 'today';

    protected function getStats(): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();

        $votesQuery = $this->getFilteredVotesQuery($user);

        // Total avis
        $totalAvis = (clone $votesQuery)->count();

        // Taux de satisfaction
        $satisfaits = (clone $votesQuery)->where('niveau', 'satisfait')->count();
        $tauxSatisfaction = $totalAvis > 0
            ? round(($satisfaits / $totalAvis) * 100, 1)
            : 0;

        // Sites actifs
        $sitesActifs = $this->getFilteredSitesQuery($user)
            ->where('statut', true)
            ->count();

        // Site le plus performant
        $meilleurSite = $this->getMeilleurSite($user);

        // Site le moins performant
        $moinsPerformant = $this->getMoinsBonSite($user);

        return [
            Stat::make('Total Avis', number_format($totalAvis))
                ->description('Avis collectés sur la période')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('primary'),

            Stat::make('Taux de Satisfaction', $tauxSatisfaction . '%')
                ->description($satisfaits . ' avis satisfaits')
                ->icon('heroicon-o-face-smile')
                ->color($tauxSatisfaction >= 70 ? 'success' : ($tauxSatisfaction >= 40 ? 'warning' : 'danger')),

            Stat::make('Sites Actifs', $sitesActifs)
                ->description('Sites opérationnels')
                ->icon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('Meilleur Site', $meilleurSite['nom'] ?? 'N/A')
                ->description('Taux : ' . ($meilleurSite['taux'] ?? 0) . '%')
                ->icon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('Site à améliorer', $moinsPerformant['nom'] ?? 'N/A')
                ->description('Taux : ' . ($moinsPerformant['taux'] ?? 0) . '%')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
        ];
    }

    private function getFilteredVotesQuery(Utilisateur $user) {
        
        $query = Vote::query();

        // Filtre par période
        match ($this->period) {
            'today'   => $query->whereDate('created_at', today()),
            'week'    => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month'   => $query->whereMonth('created_at', now()->month),
            default   => $query->whereDate('created_at', today()),
        };

        // Filtre par rôle
        if ($user->hasRole('Super admin')) {
            $siteIds = Site::where('created_by', $user->id)->pluck('id');
            $query->whereIn('site_id', $siteIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $siteIds  = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $query->whereIn('site_id', $siteIds);
        } elseif ($user->hasRole('Admin de site')) {
            $query->where('site_id', $user->site_id);
        }

        return $query;
    }

    private function getFilteredSitesQuery(Utilisateur $user)
    {
        $query = Site::query();

        if ($user->hasRole('Super admin')) {
            $query->where('created_by', $user->id);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            $query->where('id', $user->site_id);
        }

        return $query;
    }

    private function getMeilleurSite(Utilisateur $user): array {
        
        $siteIds = $this->getFilteredSitesQuery($user)->pluck('id');

        $site = Vote::whereIn('site_id', $siteIds)
            ->select('site_id', DB::raw('
                COUNT(*) as total,
                SUM(CASE WHEN niveau = "satisfait" THEN 1 ELSE 0 END) as satisfaits
            '))
            ->groupBy('site_id')
            ->orderByRaw('(satisfaits * 100.0 / total) DESC')
            ->first();

        if (!$site) return ['nom' => 'N/A', 'taux' => 0];

        $nom  = Site::find($site->site_id)?->nom ?? 'N/A';
        $taux = $site->total > 0 ? round(($site->satisfaits / $site->total) * 100, 1) : 0;

        return ['nom' => $nom, 'taux' => $taux];
    }

    private function getMoinsBonSite(Utilisateur $user): array {

        $siteIds = $this->getFilteredSitesQuery($user)->pluck('id');

        $site = Vote::whereIn('site_id', $siteIds)
            ->select('site_id', DB::raw('
                COUNT(*) as total,
                SUM(CASE WHEN niveau = "satisfait" THEN 1 ELSE 0 END) as satisfaits
            '))
            ->groupBy('site_id')
            ->orderByRaw('(satisfaits * 100.0 / total) ASC')
            ->first();

        if (!$site) return ['nom' => 'N/A', 'taux' => 0];

        $nom  = Site::find($site->site_id)?->nom ?? 'N/A';
        $taux = $site->total > 0 ? round(($site->satisfaits / $site->total) * 100, 1) : 0;

        return ['nom' => $nom, 'taux' => $taux];
    }
}
