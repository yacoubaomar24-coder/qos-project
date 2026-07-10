<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Livewire\Attributes\On;

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

    public string $period = 'today';

    public function mount(): void
    {
        $this->period = session('dashboard_period', 'today');
    }

    #[On('dashboardPeriodChanged')]
    public function updatePeriod(string $period): void
    {
        $this->period = $period;

        $this->dispatch('$refresh');
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
            //\App\Filament\Widgets\PeriodFilter::class,
            \App\Filament\Widgets\MetricsWidget::class,
            //\App\Filament\Widgets\AnomaliesWidget::class, 
            \App\Filament\Widgets\MapWidget::class,
        ];
    }
}
