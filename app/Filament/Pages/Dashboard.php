<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    // Forcer 1 colonne — tous les widgets en pleine largeur
    public function getColumns(): int|array
    {
        return 1;
    }

    // ça empêche admin de voir cette page
    /*
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\Utilisateur|null $user */
        /*$user = filament()->auth()->user();
        if (!$user instanceof \App\Models\Utilisateur) return false;

        // ✅ Admin ne voit pas cette page
        return !$user->hasRole('Admin');
    }*/

    public function getWidgets(): array
    {
        /** @var \App\Models\Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof \App\Models\Utilisateur) return [];

        if ($user->hasRole('Admin')) return [
            //
        ];
        
        return [
            \App\Filament\Widgets\PeriodFilter::class,
            \App\Filament\Widgets\MetricsWidget::class,
            \App\Filament\Widgets\MapWidget::class,
        ];
    }
}
