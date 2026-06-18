
<div style="gap:6px; margin-top:0px;">
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                margin-top:0px;padding:10px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        {{-- une façon de contourner complètement Tailwind ;
                donc Filament ne peut pas écraser les styles --}}
        <div style="margin-bottom: 10;">
            <h1 style="font-size: 20px;font-weight: 700;color: #111827;margin-bottom:16px;">
                Carte interactive des sites
            </h1>
        </div>

        {{-- Filtres --}}
        <div style="display:grid; gap:16px; padding:0px;margin:0px;width:100%">

            <div>
                <p style="font-size:15px; font-weight:600; color:#374151;margin-bottom:6px">
                {{-- Pays --}}
                <select id="filter-pays"
                    style="border:2px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                        font-size:13px; background:#f9fafb; color:#374151;">

                    @php
                        /** @var \App\Models\Utilisateur $user */
                        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

                        $pays = \App\Models\Pays::query();

                        // Admin voit tous les pays sans restriction
                        if ($user->hasRole('Admin')) {
                            //
                        }

                        // Super admin → uniquement ses pays
                        if ($user->hasRole('Super admin')) {
                            $pays->where('created_by', $user->id)->pluck('nom', 'id');
                        }

                        // Admin national → pays créé par son super admin
                        if ($user->hasRole('Admin national')) {
                            $pays->where('created_by', $user->created_by)->pluck('nom', 'id');
                        }

                        $pays = $pays->get();
                    @endphp

                    <option value="">🌍 Les pays</option>

                    @foreach($pays as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->nom }}
                        </option>
                    @endforeach

                </select>

                {{-- Région --}}
                <select id="filter-region"
                    style="border:2px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                        font-size:13px; background:#f9fafb; color:#374151;">

                    @php
                        /** @var \App\Models\Utilisateur $user */
                        $user = auth()->user();

                        $regions = \App\Models\Region::query();

                        // Admin → toutes les régions
                        if ($user->hasRole('Admin')) {

                            // aucun filtre
                        }

                        // Super admin
                        elseif ($user->hasRole('Super admin')) {

                            // IDs des Admins nationaux créés par ce Super admin
                            $adminNationalIds = \App\Models\Utilisateur::query()
                                ->where('created_by', $user->id)
                                ->role('Admin national')
                                ->pluck('id');

                            // Lui-même + ses admins nationaux
                            $creatorIds = $adminNationalIds->push($user->id);

                            // Régions visibles
                            $regions->whereIn('created_by', $creatorIds);
                        }

                        // Admin national
                        elseif ($user->hasRole('Admin national')) {

                            // Toutes les régions du même pays
                            $regions->where('pays_id', $user->pays_id);
                        }

                        // Admin régional voit uniquement sa propre région
                        elseif ($user->hasRole('Admin régional')) {
                            $regions->where('id', $user->region_id);
                        }

                        elseif ($user->hasRole('Admin de site')) {
                            $regions->where('id', $user->site_id);
                        }

                        $regions = $regions->get();
                    @endphp

                    <option value="">📍Les régions</option>

                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">
                            {{ $region->nom }}
                        </option>
                    @endforeach
                </select>

                {{-- Ville --}}
                <select id="filter-ville"
                    style="border:2px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                        font-size:13px; background:#f9fafb; color:#374151;">
                    @php
                        /** @var \App\Models\Utilisateur $user */
                        $user = auth()->user();

                        $villes = \App\Models\Ville::query();

                        // Admin → toutes les villes
                        if ($user->hasRole('Admin')) {
                            // aucun filtre
                        }

                        // Super admin
                        elseif ($user->hasRole('Super admin')) {

                            // IDs des Admins nationaux créés par ce Super admin
                            $adminNationalIds = \App\Models\Utilisateur::query()
                                ->where('created_by', $user->id)
                                ->role('Admin national')
                                ->pluck('id');

                            // Lui-même + ses admins nationaux
                            $creatorIds = $adminNationalIds->push($user->id);

                            // villes visibles
                            $villes->whereIn('created_by', $creatorIds);
                        }

                        // Admin national
                        elseif ($user->hasRole('Admin national')) {
                            // Régions du pays
                            $regionIds = \App\Models\Region::query()
                                ->where('pays_id', $user->pays_id)
                                ->pluck('id');

                            // Villes de ces régions
                            $villes->whereIn('region_id', $regionIds);
                        }

                        // Admin régional voit les villes de sa propre région
                        elseif ($user->hasRole('Admin régional')) {
                            $villes->where('region_id', $user->region_id);
                        }

                        elseif ($user->hasRole('Admin de site')) {
                            $site = \App\Models\Site::find($user->site_id);
                            if ($site) {
                                $villes->where('id', $site->ville_id);
                            }
                        }

                        $villes = $villes->get();
                    @endphp

                    <option value="">🏙 Les villes</option>

                    @foreach($villes as $ville)
                        <option value="{{ $ville->id }}">
                            {{ $ville->nom }}
                        </option>
                    @endforeach
                </select>

                {{-- Site --}}
                <select id="filter-site"
                    style="border:2px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                        font-size:13px; background:#f9fafb; color:#374151;">

                    @php
                        /** @var \App\Models\Utilisateur $user */
                        $user = auth()->user();

                        $sites = \App\Models\Site::query();

                        // Admin → toutes les villes
                        if ($user->hasRole('Admin')) {
                            // aucun filtre
                        }

                        // Super admin
                        elseif ($user->hasRole('Super admin')) {

                            // IDs des Admins nationaux créés par ce Super admin
                            $adminNationalIds = \App\Models\Utilisateur::query()
                                ->where('created_by', $user->id)
                                ->role('Admin national')
                                ->pluck('id');

                            // Lui-même + ses admins nationaux
                            $creatorIds = $adminNationalIds->push($user->id);

                            // sites visibles
                            $sites->whereIn('created_by', $creatorIds);
                        }

                        // Admin national
                        elseif ($user->hasRole('Admin national')) {
                            // Régions du pays
                            $regionIds = \App\Models\Region::query()
                                ->where('pays_id', $user->pays_id)
                                ->pluck('id');

                            // Villes de ces régions
                            $villeIds = \App\Models\Ville::query()
                                ->whereIn('region_id', $regionIds)
                                ->pluck('id');

                            // Sites de ces villes
                            $sites->whereIn('ville_id', $villeIds);
                        }

                        // Admin régional voit les sites de sa propre région
                        elseif ($user->hasRole('Admin régional')) {

                            $villeIds = \App\Models\Ville::query()
                                ->where('region_id', $user->region_id)
                                ->pluck('id');

                            $sites->whereIn('ville_id', $villeIds);
                        }

                        elseif ($user->hasRole('Admin de site')) {
                            $sites->where('id', $user->site_id);
                        }

                        $sites = $sites->get();
                    @endphp

                    <option value="">🏢 Les sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}">
                            {{ $site->nom }}
                        </option>
                    @endforeach
                </select>

                {{-- Période --}}
                <select wire:model="selectedPeriod"
                    style="border:2px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                        font-size:13px; background:#f9fafb; color:#374151;">

                    <option value="all">📅 Les périodes</option>
                    <option value="today">Aujourd’hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>

                </select>

                {{-- Bouton appliquer --}}
                <button wire:click="applyPeriod"
                    style="
                        min-width: 100px;
                        height: 35px;
                        padding: 6 12px;
                        border: 2px solid hsl(138, 52%, 28%);
                        border-radius: 12px;
                        background: hsl(139, 83%, 22%);
                        font-size: 13px;
                        color: white;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                        outline: none;
                        cursor: pointer;
                    ">
                    Appliquer
                </button>

                {{-- Bouton reset --}}
                <button id="btn-reset" wire:click="resetAll"
                    style="
                        min-width: 100px;
                        height: 35px;
                        padding: 6 12px;
                        border: 2px solid #f59e0b;
                        border-radius: 12px;
                        background: #f59e0b;
                        font-size: 13px;
                        color: white;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                        outline: none;
                        cursor: pointer;
                    ">
                    Réinitialiser
                </button>
                </p>
            </div>

        </div>

        {{-- Carte --}}
        <div wire:ignore id="map-wrapper" style="height:350px; border-radius:12px;">
            <div id="leaflet-map" style="height:100%; width:100%; border-radius:12px;"></div>
        </div>

        {{-- Données JSON --}}
        <script id="map-data" type="application/json">{!! json_encode($sitesData) !!}</script>

        <div class="mt-3 text-sm"
             style="display: flex; gap: 16px; flex-wrap: nowrap; white-space: nowrap;margin-top:4px">
            <span style="display:flex;align-items:center;gap:6px;">
                <span style="width:12px;height:12px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                Satisfait (+70%)
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span style="width:12px;height:12px;border-radius:50%;background:#fb923c;display:inline-block;"></span>
                Moyennement satisfait (40-69%)
            </span>
            <span style="display:flex;align-items:center;gap:6px;">
                <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                Insatisfait (-40%)
            </span>
        </div>

        {{-- Leaflet CSS + JS --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        {{-- Charger map-widget.js après Leaflet --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let s = document.createElement("script");
                s.src = "{{ asset('js/map-widget.js') }}";
                s.onload = function() {
                    console.log("map-widget.js chargé");
                    setTimeout(window.initLeafletMap, 500);
                };
                document.head.appendChild(s);
            });
        </script>


        {{-- Script externe — évite l'interférence Livewire --}}
        <script src="{{ asset('js/map-widget.js') }}"></script>
    </div>
</div>