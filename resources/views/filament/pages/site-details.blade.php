<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Sélecteur  select wire:model.live="selectedSiteId" --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; 
                padding:12px; display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; 
                box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:11px; font-weight:600; text-transform:uppercase; 
                            letter-spacing:0.05em; color:#9ca3af;">Site</label>
            <select wire:change="changeSite($event.target.value)"
                style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; font-size:14px; 
                        background:#f9fafb; color:#374151; min-width:200px;">
                @foreach($sitesOptions as $id => $nom)
                    <option value="{{ $id }}">{{ $nom }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:11px; font-weight:600; text-transform:uppercase; 
                            letter-spacing:0.05em; color:#9ca3af;">Période</label>
            <select wire:change="changePeriod($event.target.value)"
                style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; font-size:14px; background:#f9fafb; color:#374151;">
                <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
            </select>
        </div>

        @if(!empty($siteStats))
        <div style="margin-left:auto; text-align:right;">
            <p @style="font-size:14px; color:#6b7280;">{{ $siteStats['ville'] }} — {{ $siteStats['region'] }} — {{ $siteStats['pays'] }}</p>
            <p @style="font-size:12px; color:#9ca3af;">{{ $siteStats['total'] }} avis au total</p>
        </div>
        @endif

    </div>

    @if(empty($siteStats))
        <div style="background:#f9fafb; border-radius:12px; padding:32px; text-align:center; color:#9ca3af;">
            Aucun site disponible.
        </div>
    @else

    @php
        $tauxSatisfaction  = $siteStats['taux_satisfaction'] ?? 0;
        $tauxMoyen        = $siteStats['taux_moyen'] ?? 0;
        $tauxInsatisfait   = $siteStats['taux_insatisfait'] ?? 0;
        $styleSatisfait    = "width:{$tauxSatisfaction}%";
        $styleMoyen       = "width:{$tauxMoyen}%";
        $styleInsatisfait  = "width:{$tauxInsatisfait}%";
    @endphp

    {{-- Indicateurs --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">

        {{-- Satisfait --}}
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:16px; padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; font-weight:600; color:#15803d;">Satisfait</span>
                <span @style="background:#dcfce7; color:#15803d; font-size:11px; font-weight:700; 
                            padding:2px 8px; border-radius:999px;">{{ $siteStats['satisfaits'] }} votes</span>
            </div>
            <p @style="font-size:36px; font-weight:700; color:#16a34a; margin:12px 0 8px;">{{ $tauxSatisfaction }}%</p>
            <div @style="height:8px; border-radius:999px; background:#dcfce7;">
                <div @style="height:8px; border-radius:999px; background:#22c55e; {{ $styleSatisfait }};"></div>
            </div>
        </div>

        {{-- Moyen --}}
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:16px; padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; font-weight:600; color:#b45309;">Moyennement satisfait</span>
                <span @style="background:#fef3c7; color:#b45309; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px;">{{ $siteStats['moyens'] }} votes</span>
            </div>
            <p @style="font-size:36px; font-weight:700; color:#d97706; margin:12px 0 8px;">{{ $tauxMoyen }}%</p>
            <div @style="height:8px; border-radius:999px; background:#fef3c7;">
                <div @style="height:8px; border-radius:999px; background:#f59e0b; {{ $styleMoyen }};"></div>
            </div>
        </div>

        {{-- Insatisfait --}}
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:16px; padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; font-weight:600; color:#b91c1c;">Insatisfait</span>
                <span @style="background:#fee2e2; color:#b91c1c; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px;">{{ $siteStats['insatisfaits'] }} votes</span>
            </div>
            <p @style="font-size:36px; font-weight:700; color:#ef4444; margin:12px 0 8px;">{{ $tauxInsatisfait }}%</p>
            <div @style="height:8px; border-radius:999px; background:#fee2e2;">
                <div @style="height:8px; border-radius:999px; background:#ef4444; {{ $styleInsatisfait }};"></div>
            </div>
        </div>

    </div>

    {{-- Comparaison --}}
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:16px;">
        @php
            $diffRegion   = round($tauxSatisfaction - $siteStats['moyenne_regionale'], 1);
            $diffNational = round($tauxSatisfaction - $siteStats['moyenne_nationale'], 1);
            $colorRegion  = $diffRegion >= 0 ? '#16a34a' : '#ef4444';
            $colorNational = $diffNational >= 0 ? '#16a34a' : '#ef4444';
        @endphp

        {{-- Régionale --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; 
                        padding:15px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            {{-- Ligne 1 : Titre + Badge région --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:13px; font-weight:600; color:#6b7280; text-transform:uppercase; 
                            letter-spacing:0.05em;"> Moyenne régionale
                </span>
                <span style="background:#f3f4f6; color:#374151; font-size:11px; 
                        font-weight:600; padding:2px 10px; border-radius:999px;">{{ $siteStats['region'] }}
                </span>
            </div>

            @php
                $colorClass = $diffRegion >= 0
                    ? 'text-green-600'
                    : 'text-red-600';
            @endphp

            {{-- Ligne 2 : Taux + Comparaison --}}
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span class="text-3xl font-bold {{ $colorClass }}">
                    {{ $siteStats['moyenne_regionale'] }}%
                </span>
                <span class="text-3xl font-bold {{ $colorClass }}">
                    {{ $diffRegion >= 0 ? '+' : '' }}{{ $diffRegion }}%
                    {{ $diffRegion >= 0 ? 'au-dessus' : 'en dessous' }}
                </span>
            </div>

            {{-- Barre de progression --}}
            <div style="height:6px; border-radius:999px; background:#f3f4f6; margin-top:12px;">
                <div style="height:6px; border-radius:999px; background: {{ $colorRegion }}; width: {{ $siteStats['moyenne_regionale'] }}%;"></div>
            </div>
        </div>

        {{-- Nationale --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; padding:15px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

            {{-- Ligne 1 : Titre + Badge pays --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:13px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">
                    Moyenne nationale
                </span>
                <span style="background:#f3f4f6; color:#374151; font-size:11px; font-weight:600; padding:2px 10px; border-radius:999px;">
                    {{ $siteStats['pays'] }}
                </span>
            </div>

            @php
                $colorClass = $diffNational >= 0
                    ? 'text-green-600'
                    : 'text-red-600';
            @endphp
            {{-- Ligne 2 : Taux + Comparaison --}}
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span class="text-3xl font-bold {{ $colorClass }}">
                    {{ $siteStats['moyenne_nationale'] }}%
                </span>
                <span class="text-3xl font-bold {{ $colorClass }}">
                    {{ $diffNational >= 0 ? '+' : '' }}{{ $diffNational }}%
                    {{ $diffNational >= 0 ? 'au-dessus' : 'en dessous' }}
                </span>
            </div>

            {{-- Barre de progression --}}
            <div style="height:6px; border-radius:999px; background:#f3f4f6; margin-top:12px;">
                <div style="height:6px; border-radius:999px; background: {{ $colorNational }}; width: {{ $siteStats['moyenne_nationale'] }}%;"></div>
            </div>

        </div>

    </div>

    {{-- Courbe --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; padding:15px; 
                    box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <p style="font-size:15px; font-weight:600; color:#374151; margin-bottom:16px;">
                    Évolution du taux de satisfaction</p>
        <canvas id="evolution-chart" style="max-height:300px;"></canvas>
        
        {{-- données hors wire:ignore pour être mises à jour --}}
        <script id="evolution-data" type="application/json">{!! json_encode($siteStats['evolution']) !!}</script>
    </div>
    @endif

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// ✅ Écouter l'événement personnalisé
window.addEventListener("siteChanged", function(event) {
    console.log("siteChanged recu, points:", event.detail.evolution.length);
    
    let canvas = document.getElementById("evolution-chart");
    if (!canvas) return;

    let evolution = event.detail.evolution;

    if (window.evolutionChart instanceof Chart) {
        window.evolutionChart.destroy();
        window.evolutionChart = null;
    }

    window.evolutionChart = new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
            labels: evolution.map(function(d) { return d.label; }),
            datasets: [
                {
                    label: "Taux de satisfaction (%)",
                    data: evolution.map(function(d) { return d.taux; }),
                    borderColor: "#22c55e",
                    backgroundColor: "rgba(34,197,94,0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    yAxisID: "y"
                },
                {
                    label: "Nombre de votes",
                    data: evolution.map(function(d) { return d.total; }),
                    borderColor: "#f59e0b",
                    backgroundColor: "rgba(245,158,11,0.1)",
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    yAxisID: "y1"
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: "index", intersect: false },
            plugins: { legend: { position: "top" } },
            scales: {
                y:  { type: "linear", position: "left",  min: 0, max: 100, title: { display: true, text: "Taux (%)" } },
                y1: { type: "linear", position: "right", min: 0, grid: { drawOnChartArea: false }, title: { display: true, text: "Votes" } }
            }
        }
    });
});
</script>

</x-filament-panels::page>