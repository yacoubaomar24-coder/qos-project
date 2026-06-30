<x-filament-panels::page>

    {{-- Widgets Filament --}}
    {{-- WIDGETS --}}
    <x-filament-widgets::widgets
        :widgets="$this->getVisibleWidgets()"
        :columns="$this->getColumns()"
    />

</x-filament-panels::page>