<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PeriodFilter extends Widget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.period-filter';

    public string $period = 'today';

    public function updatedPeriod(): void
    {
        // Filament v5 — dispatch vers tous les composants Livewire
        $this->dispatch('periodChanged', period: $this->period)->to(StatsOverview::class);
    }
}
