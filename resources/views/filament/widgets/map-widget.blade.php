<x-filament-widgets::widget>
    <x-filament::section heading="Carte des sites">

        {{-- Filtres --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <select wire:model.live="filterPays"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Tous les pays</option>
                @foreach(\App\Models\Pays::all() as $pays)
                    <option value="{{ $pays->id }}">{{ $pays->nom }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterRegion"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Toutes les régions</option>
                @foreach(\App\Models\Region::all() as $region)
                    <option value="{{ $region->id }}">{{ $region->nom }}</option>
                @endforeach
            </select>

            <select wire:model.live="period"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>
        </div>

        {{-- Carte 
        style="height: 100%; width: 100%; border-radius: 12px;"> --}}
        <div id="map-container"
             wire:ignore
             style="height: 500px; border-radius: 12px; z-index: 1; position: relative;">
            <div id="map-leaflet"
                 style="height: 500px; width: 100%;">
            </div>
        </div>

        {{-- Données pour JS 
               data-sites="{{ htmlspecialchars(json_encode($sitesData), ENT_QUOTES) }}"
        --}}
        <div id="map-data"
            data-sites='@json($sitesData)'
            style="display:none">
        </div>

        {{-- Légende --}}
        <div class="flex gap-4 mt-3 text-sm">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                Satisfaisant (70%+)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                Moyen (40-70%)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                Insuffisant (-40%)
            </span>
        </div>

    </x-filament::section>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <pre>{{ json_encode($sitesData, JSON_PRETTY_PRINT) }}</pre>
    @push('scripts')
<script>

document.addEventListener('livewire:navigated', function () {

    const mapEl = document.getElementById('map-leaflet');

    if (!mapEl) return;

    // éviter double initialisation
    if (mapEl._leaflet_id) {
        mapEl._leaflet_id = null;
        mapEl.innerHTML = '';
    }

    const dataEl = document.getElementById('map-data');

    const sites = JSON.parse(
        dataEl.dataset.sites || '[]'
    );

    console.log(sites);

    const map = L.map('map-leaflet')
        .setView([17.6078, 8.0817], 6);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: 'OpenStreetMap'
        }
    ).addTo(map);

    sites.forEach(site => {

        if (site.latitude == null || site.longitude == null) return;

        L.circleMarker(
            [site.latitude, site.longitude],
            {
                color: site.color || 'green',
                fillColor: site.color || 'green',
                fillOpacity: 0.8,
                radius: 10
            }
        )
        .addTo(map)
        .bindPopup(site.nom);
    });

    setTimeout(() => {
        map.invalidateSize();
    }, 300);

});

</script>
@endpush
</x-filament-widgets::widget>