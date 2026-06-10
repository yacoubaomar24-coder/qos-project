<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use App\Models\Site;
use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

//class StatsOverview extends StatsOverviewWidget
class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    // Période sélectionnée
    public string $period = 'today';

    // ✅ Écouter l'événement via attribut On
    #[On('periodChanged')]

    // Pour changer la période
    // Livewire appelle cette méthode automatiquement
    // quand $period change via wire:model.live
    public function updatePeriod(string $period): void
    {
        $this->period = $period;
    }

    private function getAuthUser(): ?Utilisateur
    {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        return $user instanceof Utilisateur ? $user : null;
    }

    private static function getVisibleCreatorIds(\App\Models\Utilisateur $user): array
    {
        // IDs des Admin nationaux créés par ce Super admin
        $adminNationalIds = \App\Models\Utilisateur::where('created_by', $user->id)
            ->where('role', 'Admin national')
            ->pluck('id')
            ->toArray();

        // Super admin lui-même + ses Admin nationaux
        return array_merge([$user->id], $adminNationalIds);
    }

    protected function getStats(): array
    {
        $user = $this->getAuthUser();

        // Sécurité — si pas connecté retourner des stats vides
        if (!$user) {
            return [];
        }

        //** @var Utilisateur $user */
        //$user = filament()->auth()->user();

        $votesQuery = $this->getFilteredVotesQuery($user);

        // Nombre total d'avis sur la période
        $totalAvis = (clone $votesQuery)->count();

        // Taux de satisfaction sur la période
        $satisfaits = (clone $votesQuery)->where('niveau', 'satisfait')->count();
        $tauxSatisfaction = $totalAvis > 0
            ? round(($satisfaits / $totalAvis) * 100, 1)
            : 0;

        // Nombre de sites actifs selon le rôle
        $sitesActifs = $this->getFilteredSitesQuery($user)
            ->where('statut', true)
            ->count();

        // Site le plus performant, avec meilleur taux de satisfaction
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

    // Filtre des votes selon le rôle et la période
    private function getFilteredVotesQuery(Utilisateur $user) {
        
        $query = Vote::query();

        // Filtre par période
        match ($this->period) {
            'today'   => $query->whereDate('created_at', today()),
            'week'    => $query->whereBetween('created_at', [
                                now()->startOfWeek(), 
                                now()->endOfWeek()
                            ]),
            'month' => $query->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year),
            'year'  => $query->whereYear('created_at', now()->year),
            default   => $query->whereDate('created_at', today()),
        };

        // Filtre par rôle
        // 'today' → uniquement les votes d'aujourd'hui
        // 'week'  → votes de la semaine en cours (lundi → dimanche)
        if ($user->hasRole('Admin')) {
            // Admin voit tous les votes sans restriction
            return $query;
        }

        if ($user->hasRole('Super admin')) {
            // Super admin voit ses sites + sites de ses Admin nationaux
            $creatorIds = $this->getVisibleCreatorIds($user);
            $siteIds    = Site::whereIn('created_by', $creatorIds)->pluck('id');
            $query->whereIn('site_id', $siteIds);

        } elseif ($user->hasRole('Admin national')) {
            // Admin national voit les votes des sites de son pays
            // Chemin : pays → régions → villes → sites → votes
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)
                ->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)
                ->pluck('id');
            $siteIds   = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $query->whereIn('site_id', $siteIds); 

        } elseif ($user->hasRole('Admin régional')) {
            // Admin régional voit les votes des sites de sa région
            // Chemin : région → villes → sites → votes
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $siteIds  = Site::whereIn('ville_id', $villeIds)->pluck('id');
            $query->whereIn('site_id', $siteIds);

        } elseif ($user->hasRole('Admin de site')) {
            // Admin de site voit uniquement les votes de son site
            $query->where('site_id', $user->site_id);
        }

        return $query;
    }

    // Filtre des sites selon le rôle
    private function getFilteredSitesQuery(Utilisateur $user)
    {
        $query = Site::query();

        if ($user->hasRole('Admin')) {
            // Admin voit tous les sites
            return $query;
        }
        
        if ($user->hasRole('Super admin')) {
            // Super admin voit ses sites + sites de ses Admin nationaux
            $creatorIds = $this->getVisibleCreatorIds($user);
            $query->whereIn('created_by', $creatorIds);

        } elseif ($user->hasRole('Admin national')) {
            // Admin national voit les sites de son pays
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)
                ->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)
                ->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin régional')) {
            // Admin régional voit les sites de sa région
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)
                ->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            // Admin de site voit uniquement son site
            $query->where('id', $user->site_id);
        }

        return $query;
    }

    // Le meilleur site
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

    // Site le moins performant
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
