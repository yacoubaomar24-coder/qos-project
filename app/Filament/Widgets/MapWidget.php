<?php

namespace App\Filament\Widgets;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;

class MapWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    //protected static string $view = 'filament.widgets.map-widget';
    protected string $view = 'filament.widgets.map-widget';

    
    // ✅ Propriétés publiques — accessibles dans Blade
    public array $sitesData = [];
    public string $period    = 'today';
    public string $filterPays   = '';
    public string $filterRegion = '';

    public function mount(): void
    {
        $this->sitesData = $this->getSitesData();
    }

    
    public function getSitesData(): array {
        /** @var Utilisateur|null $user */
        $user  = filament()->auth()->user();
        //$user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

        $sites = Site::with(['ville.region.pays'])
            ->where('statut', true)
            ->when($user->hasRole('Super admin'), fn($q) =>
                $q->where('created_by', $user->id)
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

            return $sites->map(function (Site $site) {
            $totalVotes  = Vote::where('site_id', $site->id)->count();
            $satisfaits  = Vote::where('site_id', $site->id)->where('niveau', 'satisfait')->count();
            $taux        = $totalVotes > 0 ? round(($satisfaits / $totalVotes) * 100, 1) : 0;

            return [
                'id'      => $site->id,
                'nom'     => $site->nom,
                'ville'   => $site->ville?->nom,
                'region'  => $site->ville?->region?->nom,
                'pays'    => $site->ville?->region?->pays?->nom,
                'statut'  => $site->statut,
                'taux'    => $taux,
                'total'   => $totalVotes,
                'latitude' => $site->latitude,
                'longitude' => $site->longitude,
                'color'   => $taux >= 70 ? 'green' : ($taux >= 40 ? 'orange' : 'red'),
            ];
        })->toArray();
    }

    // Mise à jour quand les filtres changent
    public function updatedPeriod(): void
    {
        $this->sitesData = $this->getSitesData();
    }

    public function updatedFilterPays(): void
    {
        $this->sitesData = $this->getSitesData();
    }

    public function updatedFilterRegion(): void
    {
        $this->sitesData = $this->getSitesData();
    }
    
}
