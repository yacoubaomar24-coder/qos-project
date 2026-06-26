<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    protected string $view = 'filament.pages.dashboard';

    // Forcer 1 colonne — tous les widgets en pleine largeur
    public function getColumns(): int|array
    {
        return 1;
    }

    // Titre
    public function getHeading(): string
    {
        return '';
    }

    // Sous titre
    public function getSubheading(): ?string
    {
        return null;
    }

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
