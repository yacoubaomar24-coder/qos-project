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

    public function getWidgets(): array
    {
        
        return [
            \App\Filament\Widgets\PeriodFilter::class,
            //\App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\MetricsWidget::class,
            \App\Filament\Widgets\MapWidget::class,
        ];
    }
}
