<x-filament-widgets::widget>
    <x-filament::section heading="Carte des sites">

        {{-- Filtres --}}
        <div class="flex flex-wrap gap-3 mb-4">

            {{-- Pays — JS pur --}}
            <select id="filter-pays"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Tous les pays</option>
                @foreach(\App\Models\Pays::all() as $pays)
                    <option value="{{ $pays->id }}">{{ $pays->nom }}</option>
                @endforeach
            </select>

            {{-- Région — JS pur --}}
            <select id="filter-region"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Toutes les régions</option>
                @foreach(\App\Models\Region::all() as $region)
                    <option value="{{ $region->id }}">{{ $region->nom }}</option>
                @endforeach
            </select>

            {{-- Ville — JS pur --}}
            <select id="filter-ville"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Toutes les villes</option>
                @foreach(\App\Models\Ville::all() as $ville)
                    <option value="{{ $ville->id }}">{{ $ville->nom }}</option>
                @endforeach
            </select>

            {{-- Site — JS pur --}}
            <select id="filter-site"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="">Tous les sites</option>
                @foreach(\App\Models\Site::all() as $site)
                    <option value="{{ $site->id }}">{{ $site->nom }}</option>
                @endforeach
            </select>

            {{-- Période — Livewire --}}
            <select wire:model="selectedPeriod"
                class="rounded-lg border border-gray-300 text-sm px-3 py-2 bg-white">
                <option value="all">Toutes les périodes</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>

            {{-- Bouton appliquer période --}}
            <button wire:click="applyPeriod"
                class="rounded-lg bg-amber-500 text-white text-sm px-4 py-2 hover:bg-amber-600">
                Appliquer
            </button>

            {{-- Bouton reset --}}
            <button id="btn-reset"
                class="rounded-lg border border-gray-300 text-sm px-4 py-2 bg-white hover:bg-gray-50">
                Réinitialiser
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

    // Construire les marqueurs depuis les données
    function buildMarkers(sites) {
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
                'Ville : '  + site.ville  + '<br>' +
                'Region : ' + site.region + '<br>' +
                'Pays : '   + site.pays   + '<br>' +
                'Satisfaction : <strong>' + site.taux + '%</strong><br>' +
                'Total avis : ' + site.total +
                '</div>'
            );

            // Stocker les IDs pour le filtrage
            m.siteData = site;
            allMarkers.push(m);
        });

        fitMap(allMarkers);
    }

    // Ajuster le zoom sur les marqueurs visibles
    function fitMap(markers) {
        if (markers.length > 0) {
            mapInstance.fitBounds(
                L.featureGroup(markers).getBounds().pad(0.2)
            );
        }
    }

    // Appliquer les filtres JS (pays, région, ville, site)
    function applyJsFilters() {
        var pays   = document.getElementById('filter-pays').value;
        var region = document.getElementById('filter-region').value;
        var ville  = document.getElementById('filter-ville').value;
        var site   = document.getElementById('filter-site').value;
        var visible = [];

        allMarkers.forEach(function(m) {
            var s    = m.siteData;
            var show = true;

            // Filtre indépendant par chaque critère
            if (pays   && String(s.pays_id)   !== pays)   show = false;
            if (region && String(s.region_id) !== region) show = false;
            if (ville  && String(s.ville_id)  !== ville)  show = false;
            if (site   && String(s.id)        !== site)   show = false;

            if (show) { m.addTo(mapInstance); visible.push(m); }
            else      { mapInstance.removeLayer(m); }
        });

        fitMap(visible);
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

        // Écouter les filtres JS
        ['filter-pays', 'filter-region', 'filter-ville', 'filter-site']
            .forEach(function(id) {
                document.getElementById(id).addEventListener('change', applyJsFilters);
            });

        // Bouton reset
        document.getElementById('btn-reset').addEventListener('click', function() {
            ['filter-pays', 'filter-region', 'filter-ville', 'filter-site']
                .forEach(function(id) {
                    document.getElementById(id).value = '';
                });
            applyJsFilters();
        });
    }

    // Écouter l'événement Livewire personnalisé
    window.addEventListener('sitesDataUpdated', function(event) {
        if (!mapInstance) return;

        var sites = event.detail.sites;
        console.log('Données mises à jour:', sites.length, 'sites');

        buildMarkers(sites);
        applyJsFilters();
    });

    setTimeout(tryInitMap, 1000);
    </script>

</x-filament-widgets::widget>