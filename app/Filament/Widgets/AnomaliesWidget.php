<?php
// app/Filament/Widgets/AnomaliesWidget.php

namespace App\Filament\Widgets;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;

class AnomaliesWidget extends Widget
{
    protected static ?int $sort = 2; // après StatsOverview et MapWidget
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.anomalies-widget';
    protected static bool $isLazy = false;

    public array $anomalies = [];

    public function mount(): void
    {
        $this->anomalies = $this->getAnomalies();
    }

    // Masquer aux Admins — comme les autres widgets de stats
    public static function canView(): bool
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return false;
        return !$user->hasRole('Admin');
    }

    // -----------------------------------------------
    // Sites accessibles selon le rôle
    // -----------------------------------------------
    private function getSiteIds(Utilisateur $user): array
    {
        $query = Site::query()->where('statut', true);

        if ($user->hasRole('Super admin')) {
            $adminIds = Utilisateur::where('created_by', $user->id)
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

        return $query->pluck('id')->toArray();
    }

    // -----------------------------------------------
    // Détection d'anomalies — chute soudaine
    // -----------------------------------------------
    private function getAnomalies(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $siteIds   = $this->getSiteIds($user);
        $anomalies = [];

        foreach ($siteIds as $siteId) {
            $site = Site::find($siteId);
            if (!$site) continue;

            // Taux aujourd'hui
            $totalToday      = Vote::where('site_id', $siteId)->whereDate('created_at', today())->count();
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

            if ($tauxToday !== null && $tauxWeek !== null) {
                $chute = $tauxWeek - $tauxToday;
                if ($chute >= 20) {
                    $anomalies[] = [
                        'site'       => $site->nom,
                        'taux_today' => $tauxToday,
                        'taux_week'  => $tauxWeek,
                        'chute'      => round($chute, 1),
                        'niveau'     => $chute >= 40 ? 'critique' : 'warning',
                    ];
                }
            }
        }

        usort($anomalies, fn($a, $b) => $b['chute'] <=> $a['chute']);
        return $anomalies;
    }
}