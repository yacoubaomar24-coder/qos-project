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
    
    // ✅ non-static — correct pour Filament v5
    protected string $view = 'filament.widgets.map-widget';

    // ✅ Désactiver le lazy loading — cause du snapshot missing
    protected static bool $isLazy = false;

    public array $sitesData = [];
    public string $period = 'all'; // ← période sélectionnée

    public function mount(): void
    {
        $this->sitesData = $this->getSitesData();
    }

    // ← appelé quand on clique sur Appliquer
    public function applyPeriod(string $period): void
    {
        $this->period = $period;
        $this->sitesData = $this->getSitesData();
    }

    public function getSitesData(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        if (!$user instanceof Utilisateur) return [];

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

        return $sites->map(function (Site $site) {
            $totalVotes = Vote::where('site_id', $site->id)->count();
            $satisfaits = Vote::where('site_id', $site->id)
                ->where('niveau', 'satisfait')->count();
            $taux = $totalVotes > 0
                ? round(($satisfaits / $totalVotes) * 100, 1)
                : 0;

            return [
                'id'        => $site->id,
                'nom'       => $site->nom,
                'ville'     => $site->ville?->nom ?? 'N/A',
                'region'    => $site->ville?->region?->nom ?? 'N/A',
                'pays'      => $site->ville?->region?->pays?->nom ?? 'N/A',
                'taux'      => $taux,
                'total'     => $totalVotes,
                'latitude'  => $site->latitude,
                'longitude' => $site->longitude,
                'color'     => $taux >= 70 ? 'green' : ($taux >= 40 ? 'orange' : 'red'),
            ];
        })->toArray();
    }
}