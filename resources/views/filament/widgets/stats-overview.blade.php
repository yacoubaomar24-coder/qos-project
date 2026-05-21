<x-filament-widgets::widget class="fi-wi-stats-overview">
    {{-- Sélecteur de période --}}
    <div class="flex justify-end px-4 pt-4">
        <select
            wire:model.live="period"
            class="rounded-lg border border-gray-300 text-sm px-3 py-2 shadow-sm bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="today">Aujourd'hui</option>
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
        </select>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($this->getStats() as $stat)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-6">
                {{-- Label --}}
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $stat->getLabel() }}
                </p>
                {{-- Valeur --}}
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $stat->getValue() }}
                </p>
                {{-- Description --}}
                @if($stat->getDescription())
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $stat->getDescription() }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>