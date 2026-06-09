<?php

namespace App\Filament\Widgets;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Ville;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;

class MapWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.map-widget';
    protected static bool $isLazy = false;

    public array $sitesData = [];
    public string $period = 'all';
    public string $selectedPeriod = 'all';

    public function mount(): void
    {
        $this->sitesData = $this->getSitesData();
    }

    public function resetAll(): void
    {
        // Réinitialiser la période
        $this->selectedPeriod = 'all';
        $this->period         = 'all';
        $this->sitesData      = $this->getSitesData();

        // Envoyer les nouvelles données au JS
        $this->dispatch('sitesDataUpdated', sites: $this->sitesData);

        // Réinitialiser les filtres JS via JS
        $this->js("
            ['filter-pays','filter-region','filter-ville','filter-site'].forEach(function(id) {
                let el = document.getElementById(id);
                if (el) el.value = '';
            });
            if (typeof window.applyJsFilters === 'function') window.applyJsFilters();
        ");
    }

    // ✅ Sans paramètre — lit $selectedPeriod directement
    public function applyPeriod(): void
    {
        $this->period    = $this->selectedPeriod;
        $this->sitesData = $this->getSitesData();
        
        // Ceci est pour le debug, à retirer après vérification
        logger('applyPeriod appelé, period: ' . $this->period . ', sites: ' . count($this->sitesData));

        // Émettre l'événement avec les nouvelles données
        $this->dispatch('sitesDataUpdated', sites: $this->sitesData);
    }

    // Cette méthode permet de récupérer les données des sites en fonction du rôle de l'utilisateur 
    // et de la période sélectionnée
    public function getSitesData(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        // Si user n'est pas authentifié ou n'est pas une instance de Utilisateur, retourner un tableau vide
        if (!$user instanceof Utilisateur) return [];

        // Récupérer les sites en fonction du rôle de l'utilisateur
        $sites = Site::with(['ville.region.pays'])
            ->where('statut', true)
            ->when($user->hasRole('Super admin'), fn($q) =>
                $q->whereIn('created_by', array_merge(
                    [$user->id],
                    Utilisateur::where('created_by', $user->id)
                        ->where('role', 'Admin national')
                        ->pluck('id')
                        ->toArray()
                ))
            )
            ->when($user->hasRole('Admin national'), fn($q) =>
                $q->whereHas('ville.region', fn($q2) =>
                    $q2->where('pays_id', $user->pays_id)
                )
            )
            ->when($user->hasRole('Admin régional'), fn($q) =>
                $q->whereHas('ville', fn($q2) =>
                    $q2->where('region_id', $user->region_id)
                )
            )
            ->when($user->hasRole('Admin de site'), fn($q) =>
                $q->where('id', $user->site_id)
            )
            ->get();

        // Pour chaque site, calculer le taux de satisfaction en fonction des votes sur la période sélectionnée
        return $sites->map(function (Site $site) {
            // Filtre par période
            $votesQuery = Vote::where('site_id', $site->id);

            match ($this->period) {
                'today' => $votesQuery->whereDate('created_at', today()),
                'week'  => $votesQuery->whereBetween('created_at', [
                               now()->startOfWeek(),
                               now()->endOfWeek(),
                           ]),
                'month' => $votesQuery->whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year),
                default => null,
            };

            $totalVotes = (clone $votesQuery)->count();
            $satisfaits = (clone $votesQuery)->where('niveau', 'satisfait')->count();
            $taux = $totalVotes > 0
                ? round(($satisfaits / $totalVotes) * 100, 1)
                : 0;

            // returner les données du site avec le taux de satisfaction et les coordonnées pour la carte
            return [
                'id'        => $site->id,
                'nom'       => $site->nom,
                'ville'     => $site->ville?->nom ?? 'N/A',
                'ville_id'  => $site->ville_id,
                'region'    => $site->ville?->region?->nom ?? 'N/A',
                'region_id' => $site->ville?->region?->id,
                'pays'      => $site->ville?->region?->pays?->nom ?? 'N/A',
                'pays_id'   => $site->ville?->region?->pays?->id,
                'taux'      => $taux,
                'total'     => $totalVotes,
                'latitude'  => $site->latitude,
                'longitude' => $site->longitude,
                //'hasVotes'     => $totalVotes > 0,  // Masquer le site si aucun vote sur la période  
                'color'     => $taux >= 70 ? 'green' : ($taux >= 40 ? 'orange' : 'red'),
            ];
        })->toArray();
    }
}