<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         SECTION 1 : Répartition par niveau (Histogramme)
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Répartition globale par niveau de satisfaction
        </h3>

        @if(!empty($chartData['parNiveau']))
        @php $pn = $chartData['parNiveau']; @endphp

        {{-- Résumé chiffré --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px;">
            <div style="text-align:center; padding:12px; background:#f9fafb; border-radius:10px;">
                <p style="font-size:11px; color:#9ca3af; margin:0;">Total</p>
                <p style="font-size:22px; font-weight:700; color:#111827; margin:4px 0 0;">{{ $pn['total'] }}</p>
            </div>
            <div style="text-align:center; padding:12px; background:#f0fdf4; border-radius:10px;">
                <p style="font-size:11px; color:#15803d; margin:0;">Satisfaits</p>
                <p style="font-size:22px; font-weight:700; color:#16a34a; margin:4px 0 0;">{{ $pn['satisfaits'] }}</p>
                <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">{{ $pn['taux_satisfait'] }}%</p>
            </div>
            <div style="text-align:center; padding:12px; background:#fffbeb; border-radius:10px;">
                <p style="font-size:11px; color:#b45309; margin:0;">Moyens</p>
                <p style="font-size:22px; font-weight:700; color:#d97706; margin:4px 0 0;">{{ $pn['moyens'] }}</p>
                <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">{{ $pn['taux_moyen'] }}%</p>
            </div>
            <div style="text-align:center; padding:12px; background:#fef2f2; border-radius:10px;">
                <p style="font-size:11px; color:#b91c1c; margin:0;">Insatisfaits</p>
                <p style="font-size:22px; font-weight:700; color:#ef4444; margin:4px 0 0;">{{ $pn['insatisfaits'] }}</p>
                <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">{{ $pn['taux_insatisfait'] }}%</p>
            </div>
        </div>

        {{-- Histogramme --}}
        <canvas id="chart-niveau" style="max-height:250px;"></canvas>
        <script id="data-niveau" type="application/json">{!! json_encode($chartData['parNiveau']) !!}</script>
        @endif

    </div>

    {{-- ===================================================
         SECTION 2 : Évolution temporelle (Courbe)
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        
        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Évolution du taux de satisfaction dans le temps
        </h3>

        {{-- Sélecteur période --}}
        <div style="display:flex; align-items:center; gap:3px; margin-top:3px">
            <label style="font-size:12px; font-weight:600; gap:8px; color:#9ca3af; text-transform:uppercase;">
                Période
            </label>
            <select wire:change="changePeriod($event.target.value)"
                style="border:1px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                       font-size:13px; background:#f9fafb; color:#374151;">
                <option value="day"   {{ $period === 'day'   ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="week"  {{ $period === 'week'  ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="year"  {{ $period === 'year'  ? 'selected' : '' }}>Cette année</option>
            </select>
        </div>

        <canvas id="chart-evolution" style="max-height:300px;"></canvas>
        <script id="data-evolution" type="application/json">{!! json_encode($chartData['evolution'] ?? []) !!}</script>

    </div>

    {{-- ===================================================
         SECTION 3 : Classement des sites
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Classement des sites par score de satisfaction
        </h3>

        @if(!empty($chartData['classement']))
            @foreach($chartData['classement'] as $index => $site)
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">

                {{-- Rang --}}
                <div style="width:28px; height:28px; border-radius:50%;
                            background: {{ $index === 0 ? '#fbbf24' : ($index === 1 ? '#9ca3af' : ($index === 2 ? '#b45309' : '#f3f4f6')) }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:12px; font-weight:700; color: {{ $index < 3 ? 'white' : '#6b7280' }};">
                    {{ $index + 1 }}
                </div>

                {{-- Nom du site --}}
                <div style="flex:1; min-width:0;">
                    <p style="font-size:13px; font-weight:600; color:#111827; margin:0;
                              white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $site['nom'] }}
                    </p>
                    <p style="font-size:11px; color:#9ca3af; margin:2px 0 0;">
                        {{ $site['total'] }} votes
                    </p>
                </div>

                {{-- Barre de progression --}}
                <div style="flex:2; height:8px; background:#f3f4f6; border-radius:999px;">
                    <div style="height:8px; border-radius:999px;
                                background: {{ $site['color'] }};
                                width: {{ $site['taux'] }}%;
                                transition:width 0.5s ease;">
                    </div>
                </div>

                {{-- Taux --}}
                <div style="width:50px; text-align:right;">
                    <span style="font-size:14px; font-weight:700; color: {{ $site['color'] }};">
                        {{ $site['taux'] }}%
                    </span>
                </div>

            </div>
            @endforeach
        @else
            <p style="color:#9ca3af; text-align:center; padding:20px;">Aucune donnée disponible.</p>
        @endif

    </div>

    {{-- ===================================================
         SECTION 4 : Anomalies détectées
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
            Détection automatique d'anomalies
        </h3>
        <p style="font-size:12px; color:#9ca3af; margin:0 0 16px;">
            Alerte si chute de satisfaction ≥ 20 points aujourd'hui vs moyenne des 7 derniers jours
        </p>

        @if(empty($chartData['anomalies']))
            <div style="display:flex; align-items:center; gap:10px; padding:12px;
                        background:#f0fdf4; border-radius:10px; border:1px solid #bbf7d0;">
                <span style="font-size:20px;">✅</span>
                <p style="font-size:13px; color:#15803d; margin:0; font-weight:500;">
                    Aucune anomalie détectée — tous les sites fonctionnent normalement.
                </p>
            </div>
        @else
            @foreach($chartData['anomalies'] as $anomalie)
            <div style="display:flex; align-items:center; gap:12px; padding:12px; margin-bottom:8px;
                        background: {{ $anomalie['niveau'] === 'critique' ? '#fef2f2' : '#fffbeb' }};
                        border:1px solid {{ $anomalie['niveau'] === 'critique' ? '#fecaca' : '#fde68a' }};
                        border-radius:10px;">

                <span style="font-size:20px;">
                    {{ $anomalie['niveau'] === 'critique' ? '🚨' : '⚠️' }}
                </span>

                <div style="flex:1;">
                    <p style="font-size:13px; font-weight:600;
                              color: {{ $anomalie['niveau'] === 'critique' ? '#b91c1c' : '#b45309' }};
                              margin:0;">
                        {{ $anomalie['site'] }}
                    </p>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0;">
                        Aujourd'hui : {{ $anomalie['taux_today'] }}%
                        — Moyenne 7j : {{ $anomalie['taux_week'] }}%
                    </p>
                </div>

                <div style="text-align:right;">
                    <span style="font-size:14px; font-weight:700;
                                 color: {{ $anomalie['niveau'] === 'critique' ? '#ef4444' : '#f59e0b' }};">
                        -{{ $anomalie['chute'] }}%
                    </span>
                    <p style="font-size:11px; color:#9ca3af; margin:2px 0 0;">
                        {{ $anomalie['niveau'] === 'critique' ? 'CRITIQUE' : 'ATTENTION' }}
                    </p>
                </div>

            </div>
            @endforeach
        @endif

    </div>

    {{-- ===================================================
         SECTION 5 : Heatmap horaire
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div>
                <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
                    Heatmap horaire d'insatisfaction
                </h3>
                <p style="font-size:12px; color:#9ca3af; margin:0;">
                    Identifier les heures de pic d'insatisfaction par jour de la semaine
                </p>
            </div>

            {{-- Sélecteur site pour heatmap --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">Site</label>
                <select wire:change="changeHeatmapSite($event.target.value)"
                    style="border:1px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                           font-size:13px; background:#f9fafb; color:#374151;">
                    @foreach($sitesOptions as $id => $nom)
                        <option value="{{ $id }}" {{ $selectedSiteId == $id ? 'selected' : '' }}>
                            {{ $nom }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Légende heatmap --}}
        <div style="display:flex; gap:12px; margin-bottom:12px; font-size:11px; color:#6b7280;">
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:2px; display:inline-block;"></span>
                Aucun vote
            </span>
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#22c55e; border-radius:2px; display:inline-block;"></span>
                Faible insatisfaction
            </span>
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#f59e0b; border-radius:2px; display:inline-block;"></span>
                Insatisfaction modérée
            </span>
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#ef4444; border-radius:2px; display:inline-block;"></span>
                Fort taux d'insatisfaction
            </span>
        </div>

        {{-- Grille heatmap --}}
        @if(!empty($chartData['heatmap']))
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:11px;">

                {{-- En-tête heures --}}
                <thead>
                    <tr>
                        <th style="width:40px; padding:4px; color:#9ca3af; font-weight:500;"></th>
                        @for($h = 0; $h <= 23; $h++)
                        <th style="padding:2px 1px; color:#9ca3af; font-weight:500; text-align:center;">
                            {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                        </th>
                        @endfor
                    </tr>
                </thead>

                {{-- Lignes jours --}}
                <tbody>
                    @foreach($chartData['heatmap'] as $ligne)
                    <tr>
                        <td style="padding:2px 8px 2px 0; color:#374151; font-weight:600; font-size:11px; white-space:nowrap;">
                            {{ $ligne['jour'] }}
                        </td>
                        @foreach($ligne['heures'] as $cell)
                        <td style="padding:1px;">
                            <div title="H{{ str_pad($cell['heure'], 2, '0', STR_PAD_LEFT) }} — {{ $cell['total'] }} votes — {{ $cell['taux'] }}% insatisfaits"
                                 style="width:100%; aspect-ratio:1; min-width:16px; border-radius:3px;
                                        background: {{ $cell['color'] }}; cursor:default;">
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        @else
            <p style="color:#9ca3af; text-align:center; padding:20px;">Sélectionnez un site pour voir la heatmap.</p>
        @endif

    </div>

</div>

{{-- ===================================================
     Scripts Chart.js
=================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dessiner tous les graphiques au chargement
document.addEventListener("DOMContentLoaded", function() {
    drawAllCharts();
});

// Redessiner après mise à jour Livewire
window.addEventListener("chartDataLoaded", function(event) {
    console.log("chartDataLoaded recu");
    setTimeout(drawAllCharts, 100);
});

// Dessiner tous les graphiques
function drawAllCharts() {
    drawNiveauChart();
    drawEvolutionChart();
}

// -----------------------------------------------
// Histogramme : répartition par niveau
// -----------------------------------------------
function drawNiveauChart() {
    let dataEl = document.getElementById("data-niveau");
    let canvas = document.getElementById("chart-niveau");
    if (!dataEl || !canvas) return;

    let data = JSON.parse(dataEl.textContent || "{}");

    if (window.chartNiveau instanceof Chart) {
        window.chartNiveau.destroy();
    }

    window.chartNiveau = new Chart(canvas.getContext("2d"), {
        type: "bar",
        data: {
            labels: ["Satisfait", "Moyen", "Insatisfait"],
            datasets: [{
                label: "Nombre de votes",
                data: [data.satisfaits, data.moyens, data.insatisfaits],
                backgroundColor: ["#22c55e", "#f59e0b", "#ef4444"],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        // Afficher le taux en plus du nombre
                        afterBody: function(items) {
                            let taux = [data.taux_satisfait, data.taux_moyen, data.taux_insatisfait];
                            return "Taux : " + taux[items[0].dataIndex] + "%";
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: "Votes" } },
                x: { grid: { display: false } }
            }
        }
    });
}

// -----------------------------------------------
// Courbe : évolution temporelle
// -----------------------------------------------
function drawEvolutionChart() {
    let dataEl = document.getElementById("data-evolution");
    let canvas = document.getElementById("chart-evolution");
    if (!dataEl || !canvas) return;

    let evolution = JSON.parse(dataEl.textContent || "[]");

    if (window.chartEvolution instanceof Chart) {
        window.chartEvolution.destroy();
    }

    window.chartEvolution = new Chart(canvas.getContext("2d"), {
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
                    pointRadius: 3,
                    yAxisID: "y"
                },
                {
                    label: "Nombre de votes",
                    data: evolution.map(function(d) { return d.total; }),
                    borderColor: "#3b82f6",
                    backgroundColor: "rgba(59,130,246,0.1)",
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
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
}
</script>

</x-filament-panels::page>