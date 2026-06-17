<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Utilisateur;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SiteDetails extends Page
{

    protected static ?string $navigationLabel = 'Vue par site';
    protected static ?string $title = '';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.site-details';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-presentation-chart-line';
    }

    // Site sélectionné
    public ?int $selectedSiteId = null;
    public array $siteStats     = [];
    public array $sitesOptions  = [];
    public string $period       = 'week';

    public function mount(): void
    {
        $this->sitesOptions = $this->getSitesOptions();

        // Sélectionner le premier site par défaut
        if (!empty($this->sitesOptions)) {
            $this->selectedSiteId = array_key_first($this->sitesOptions);
            $this->loadSiteStats();

            // Dispatcher pour initialiser le chart au chargement (courbes)
            $this->dispatch('siteChanged', evolution: $this->siteStats['evolution'] ?? []);
        }
    }

    // Charger les sites accessibles selon le rôle
    private function getSitesOptions(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        if (!$user instanceof Utilisateur) return [];

        $query = Site::query()->where('statut', true);

        if ($user->hasRole('Super admin')) {
            $creatorIds = array_merge(
                [$user->id],
                Utilisateur::where('created_by', $user->id)
                    ->where('role', 'Admin national')
                    ->pluck('id')->toArray()
            );
            $query->whereIn('created_by', $creatorIds);
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

    // Charger les stats du site sélectionné
    public function loadSiteStats(): void
    {
        if (!$this->selectedSiteId) return;

        $site = Site::with(['ville.region.pays'])->find($this->selectedSiteId);
        if (!$site) return;

        // -----------------------------------------------
        // 1. Stats globales du site
        // -----------------------------------------------
        $totalVotes    = Vote::where('site_id', $this->selectedSiteId)->count();
        $satisfaits    = Vote::where('site_id', $this->selectedSiteId)->where('niveau', 'satisfait')->count();
        $moyens       = Vote::where('site_id', $this->selectedSiteId)->where('niveau', 'moyen')->count();
        $insatisfaits  = Vote::where('site_id', $this->selectedSiteId)->where('niveau', 'insatisfait')->count();

        // -----------------------------------------------
        // 2. Courbe d'évolution selon la période
        // -----------------------------------------------
        $evolution = $this->getEvolution();

        // -----------------------------------------------
        // 3. Moyenne régionale
        // -----------------------------------------------
        $moyenneRegionale = $this->getMoyenneRegionale($site);

        // -----------------------------------------------
        // 4. Moyenne nationale
        // -----------------------------------------------
        $moyenneNationale = $this->getMoyenneNationale($site);

        $this->siteStats = [
            'site'             => $site->nom,
            'ville'            => $site->ville?->nom ?? 'N/A',
            'region'           => $site->ville?->region?->nom ?? 'N/A',
            'pays'             => $site->ville?->region?->pays?->nom ?? 'N/A',
            'total'            => $totalVotes,
            'satisfaits'       => $satisfaits,
            'moyens'          => $moyens,
            'insatisfaits'     => $insatisfaits,
            'taux_satisfaction' => $totalVotes > 0 ? round(($satisfaits / $totalVotes) * 100, 1) : 0,
            'taux_moyen'      => $totalVotes > 0 ? round(($moyens / $totalVotes) * 100, 1) : 0,
            'taux_insatisfait' => $totalVotes > 0 ? round(($insatisfaits / $totalVotes) * 100, 1) : 0,
            'evolution'        => $evolution,
            'moyenne_regionale' => $moyenneRegionale,
            'moyenne_nationale' => $moyenneNationale,
        ];
    }

    // Courbe d'évolution par jour/semaine/mois
    private function getEvolution(): array
    {
        $data = [];

        match ($this->period) {
            // 7 derniers jours
            'week' => (function() use (&$data) {
                for ($i = 6; $i >= 0; $i--) {
                    $date  = now()->subDays($i);
                    $total = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)->count();
                    $satisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'satisfait')->count();
                    $moyens = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'moyen')->count();
                    $insatisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'insatisfait')->count();
                    $data[] = [
                        'label' => $date->format('d/m'),
                        'total' => $total,
                        'taux_satisfait'  => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                        'taux_moyen'  => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
                        'taux_insatisfait'  => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),

            // 30 derniers jours
            'month' => (function() use (&$data) {
                for ($i = 29; $i >= 0; $i--) {
                    $date  = now()->subDays($i);
                    $total = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)->count();
                    $satisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'satisfait')->count();
                    $moyens = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'moyen')->count();
                    $insatisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereDate('created_at', $date)
                        ->where('niveau', 'insatisfait')->count();
                    $data[] = [
                        'label' => $date->format('d/m'),
                        'total' => $total,
                        'taux_satisfait' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                        'taux_moyen' => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
                        'taux_insatisfait' => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
                    ];
                }
            })(),

            // day
            default => (function () use (&$data) {

                for ($i = 0; $i <= 23; $i++) {
                    $heureDebut = now()->startOfDay()->addHours($i);
                    $heureFin   = (clone $heureDebut)->addHour();

                    $total = Vote::where('site_id', $this->selectedSiteId)
                        ->whereBetween('created_at', [$heureDebut, $heureFin])
                        ->count();

                    $satisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereBetween('created_at', [$heureDebut, $heureFin])
                        ->where('niveau', 'satisfait')
                        ->count();
                    $moyens = Vote::where('site_id', $this->selectedSiteId)
                        ->whereBetween('created_at', [$heureDebut, $heureFin])
                        ->where('niveau', 'moyen')->count();
                    $insatisfaits = Vote::where('site_id', $this->selectedSiteId)
                        ->whereBetween('created_at', [$heureDebut, $heureFin])
                        ->where('niveau', 'insatisfait')->count();

                    $data[] = [
                        'label' => $heureDebut->format('H:i'),
                        'total' => $total,
                        'taux_satisfait'  => $total > 0 ? round(($satisfaits / $total) * 100, 1): 0,
                        'taux_moyen' => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
                        'taux_insatisfait' => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
                    ];
                }

            })(),
        };

        return $data;
    }

    // Moyenne régionale
    private function getMoyenneRegionale(Site $site): float
    {
        // Elle calcule le taux de satisfaction moyen de tous les sites d'une même région 
        // pour comparer avec le site sélectionné

        // 1. Récupérer l'ID de la région du site sélectionné
        $regionId = $site->ville?->region?->id;
        
        // 2. Sécurité — si la région n'existe pas, retourner 0
        if (!$regionId) return 0;

        // 3. Trouver toutes les villes de cette région
        $villeIds = \App\Models\Ville::where('region_id', $regionId)->pluck('id');

        // 4. Trouver tous les sites dans ces villes
        $siteIds  = Site::whereIn('ville_id', $villeIds)->pluck('id');

        // 5. Compter tous les votes de ces sites, Ex: 50 votes au total dans la région
        $total = Vote::whereIn('site_id', $siteIds)->count();
        
        // 6. Compter uniquement les votes "satisfait", Ex: 35 votes satisfaits dans la région
        $satisfaits = Vote::whereIn('site_id', $siteIds)->where('niveau', 'satisfait')->count();

        // 7. Calculer le taux en %
        // Si total > 0 → (35 / 50) * 100 = 70%
        // Sinon → 0 pour éviter une division par zéro
        return $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0;
    }

    // Moyenne nationale
    private function getMoyenneNationale(Site $site): float
    {
        $paysId = $site->ville?->region?->pays?->id;

        if (!$paysId) return 0;

        $regionIds = \App\Models\Region::where('pays_id', $paysId)->pluck('id');
        $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
        $siteIds   = Site::whereIn('ville_id', $villeIds)->pluck('id');

        $total      = Vote::whereIn('site_id', $siteIds)->count();
        $satisfaits = Vote::whereIn('site_id', $siteIds)->where('niveau', 'satisfait')->count();

        return $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0;
    }

    public function changeSite(int $value = null): void
    {
        if ($value) $this->selectedSiteId = $value;
        $this->loadSiteStats();

        // ✅ Envoyer les nouvelles données au JS
        $this->dispatch('siteChanged', evolution: $this->siteStats['evolution'] ?? []);
    }

    // Changer la période
    public function changePeriod(string $period): void
    {
        $this->period = $period;
        $this->loadSiteStats();

        $this->dispatch('siteChanged', evolution: $this->siteStats['evolution'] ?? []);
    }
}