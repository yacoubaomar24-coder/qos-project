<x-filament-widgets::widget>
    <x-filament::section heading="Carte des sites">

        {{-- Filtres --}}
        <div class="flex flex-wrap gap-3 mb-4">

            {{-- Filtre pays — JS pur --}}
            <select id="filter-pays"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Tous les pays</option>
                @foreach(\App\Models\Pays::all() as $pays)
                    <option value="{{ $pays->nom }}">{{ $pays->nom }}</option>
                @endforeach
            </select>

            {{-- Filtre région — JS pur --}}
            <select id="filter-region"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Toutes les régions</option>
                @foreach(\App\Models\Region::all() as $region)
                    <option value="{{ $region->nom }}">{{ $region->nom }}</option>
                @endforeach
            </select>

            {{-- Filtre période — via Livewire --}}
            <select id="filter-period"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="all">Toutes les périodes</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>

            {{-- ✅ wire:click appelle directement la méthode Livewire --}}
            <button
                wire:click="applyPeriod(document.getElementById('filter-period').value)"
                class="rounded-lg bg-amber-500 text-white text-sm px-4 py-2 hover:bg-amber-600">
                Appliquer
            </button>

        </div>

        {{-- Carte --}}
        <div wire:ignore id="map-wrapper" style="height:500px; border-radius:12px;">
            <div id="leaflet-map" style="height:100%; width:100%; border-radius:12px;"></div>
        </div>

        {{-- Données JSON --}}
        <script id="map-data" type="application/json">{!! json_encode($sitesData) !!}</script>

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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var mapInstance = null;
    var allMarkers  = [];

    function buildMarkers(sites) {
        // Supprimer les anciens marqueurs
        allMarkers.forEach(function(m) { mapInstance.removeLayer(m); });
        allMarkers = [];

        sites.forEach(function(site) {
            var lat = parseFloat(site.latitude);
            var lng = parseFloat(site.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            var m = L.circleMarker([lat, lng], {
                color: site.color, fillColor: site.color,
                fillOpacity: 0.8, radius: 10
            }).addTo(mapInstance);

            m.bindPopup(
                '<div style="min-width:160px">' +
                '<strong>' + site.nom + '</strong><br>' +
                'Ville : ' + site.ville + '<br>' +
                'Region : ' + site.region + '<br>' +
                'Pays : ' + site.pays + '<br>' +
                'Satisfaction : <strong>' + site.taux + '%</strong><br>' +
                'Total avis : ' + site.total +
                '</div>'
            );

            m.siteData = site;
            allMarkers.push(m);
        });

        if (allMarkers.length > 0) {
            mapInstance.fitBounds(
                L.featureGroup(allMarkers).getBounds().pad(0.2)
            );
        }
    }

    function applyFilters() {
        var pays   = document.getElementById('filter-pays').value;
        var region = document.getElementById('filter-region').value;
        var visible = [];

        allMarkers.forEach(function(m) {
            var site = m.siteData;
            var show = true;

            if (pays   && site.pays   !== pays)   show = false;
            if (region && site.region !== region) show = false;

            if (show) { m.addTo(mapInstance);      visible.push(m); }
            else      { mapInstance.removeLayer(m); }
        });

        if (visible.length > 0) {
            mapInstance.fitBounds(
                L.featureGroup(visible).getBounds().pad(0.2)
            );
        }
    }

    function tryInitMap() {
        if (typeof L === 'undefined') { setTimeout(tryInitMap, 500); return; }

        var el = document.getElementById('leaflet-map');
        if (!el) { setTimeout(tryInitMap, 500); return; }
        if (el._leaflet_id) return;

        mapInstance = L.map('leaflet-map').setView([17.6078, 8.0817], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance);

        var sites = JSON.parse(document.getElementById('map-data').textContent || '[]');
        buildMarkers(sites);

        // Filtres pays et région — JS pur
        document.getElementById('filter-pays').addEventListener('change', applyFilters);
        document.getElementById('filter-region').addEventListener('change', applyFilters);

        // Bouton Appliquer — envoie la période à Livewire
        document.getElementById('btn-apply-period').addEventListener('click', function() {
            var period = document.getElementById('filter-period').value;

            // ✅ Cibler le composant MapWidget directement via wire:id
            var component = document.querySelector('#map-wrapper').closest('[wire\\:id]');

            // Appel Livewire pour recharger les données avec la nouvelle période
            if (component) {
                var wireId = component.getAttribute('wire:id');
                Livewire.find(wireId).call('applyPeriod', period);
            } else {
                // Fallback — chercher tous les composants
                console.log('Composant introuvable, tentative fallback');
                var allComponents = document.querySelectorAll('[wire\\:id]');
                allComponents.forEach(function(c) {
                    try {
                        Livewire.find(c.getAttribute('wire:id')).call('applyPeriod', period);
                    } catch(e) {}
                });
            }
        });
    }

    // Écouter la mise à jour Livewire pour reconstruire les marqueurs
    document.addEventListener('livewire:updated', function() {
        var dataEl = document.getElementById('map-data');
        if (!dataEl || !mapInstance) return;

        var sites = JSON.parse(dataEl.textContent || '[]');
        buildMarkers(sites);

        // Réappliquer les filtres JS
        applyFilters();
    });

    setTimeout(tryInitMap, 1000);
    </script>

</x-filament-widgets::widget>