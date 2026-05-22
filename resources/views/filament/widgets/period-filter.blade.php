<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <div class="flex justify-end px-4 py-2">
            <select
                wire:model.live="period"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="year">Cette année</option>
            </select>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
