<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Sélecteur Période --}}
                <select wire:model.live="period"
                    style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; font-size:14px; 
                            background:#f9fafb; color:#374151; min-width:200px;">
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="year">Cette année</option>
                </select>
    </x-filament::section>
</x-filament-widgets::widget>
