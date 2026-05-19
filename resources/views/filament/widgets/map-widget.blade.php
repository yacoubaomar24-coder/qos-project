<x-filament-widgets::widget>
    <x-filament::section heading="Carte des sites">

        <div class="flex flex-wrap gap-3 mb-4">
            <select wire:model.live="filterPays" class="rounded-lg border-gray-300 text-sm">
                <option value="">Tous les pays</option>
                @foreach(\App\Models\Pays::all() as $pays)
                    <option value="{{ $pays->id }}">{{ $pays->nom }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterRegion" class="rounded-lg border-gray-300 text-sm">
                <option value="">Toutes les regions</option>
                @foreach(\App\Models\Region::all() as $region)
                    <option value="{{ $region->id }}">{{ $region->nom }}</option>
                @endforeach
            </select>

            <select wire:model.live="period" class="rounded-lg border-gray-300 text-sm">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>
        </div>

        <div id="map" style="height: 500px; border-radius: 12px; z-index: 1;"></div>

        <div class="flex gap-4 mt-3 text-sm">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                Satisfaisant
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                Moyen
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                Insuffisant
            </span>
        </div>

    </x-filament::section>
    {{-- Dans la section HTML --}}
    <div id="map"
        data-sites="{{ htmlspecialchars(json_encode($sitesData), ENT_QUOTES) }}"
        style="height: 500px; border-radius: 12px; z-index: 1;">
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map').setView([17.6078, 8.0817], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'OpenStreetMap'
            }).addTo(map);

            //const sites = @json($sitesData);

            sites.forEach(function(site) {
                const color = site.color;
                const lat = site.lat ? site.lat : 17.6078;
                const lng = site.lng ? site.lng : 8.0817;

                const marker = L.circleMarker([lat, lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.8,
                    radius: 10
                }).addTo(map);

                marker.bindPopup(
                    '<div style="min-width:180px">' +
                    '<strong>' + site.nom + '</strong><br>' +
                    'Ville : ' + site.ville + '<br>' +
                    'Region : ' + site.region + '<br>' +
                    'Pays : ' + site.pays + '<br>' +
                    'Satisfaction : ' + site.taux + '%<br>' +
                    'Total avis : ' + site.total +
                    '</div>'
                );
            });
        });
    </script>
    @endpush
</x-filament-widgets::widget>