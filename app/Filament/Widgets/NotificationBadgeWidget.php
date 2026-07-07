<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use App\Models\Site;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;

class NotificationBadgeWidget extends Widget
{
    protected static bool $isLazy   = false;
    protected static ?int $sort     = 0;
    protected string      $view     = 'filament.widgets.notification-badge';

    // ✅ Polling toutes les 30 secondes
    protected static ?string $pollingInterval = '30s';

    public int    $nombreAlertes = 0;
    public string $urlAlertes    = '';

    public function mount(): void
    {
        $this->chargerAlertes();
        $this->urlAlertes = url('/admin/alertes');
    }

    public function chargerAlertes(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return;

        $siteIds = Site::where('created_by', $user->id)->pluck('id');

        $this->nombreAlertes = Alerte::whereIn('site_id', $siteIds)
            ->where('statut', 'nouvelle')
            ->count();
    }
}