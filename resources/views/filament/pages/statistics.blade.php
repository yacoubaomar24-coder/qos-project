<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         SECTION 1 : Répartition par niveau (Histogramme)
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Répartition globale par niveau de votes
        </h3>

        @if(!empty($chartData['parNiveau']))
        @php $pn = $chartData['parNiveau']; @endphp

        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:10px;">

            {{-- Total des votes --}}
            <div style="
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                padding:18px;
                background:linear-gradient(135deg,#ffffff,#f9fafb);
                border:1px solid #e5e7eb;
                border-radius:16px;
                box-shadow:0 2px 8px rgba(0,0,0,0.05);
                min-height:120px;
            ">

                {{-- Titre --}}
                <p style="
                    font-size:12px;
                    font-weight:600;
                    letter-spacing:0.04em;
                    color:#9ca3af;
                    text-transform:uppercase;
                    margin:0;
                ">
                    Total
                </p>

                {{-- Valeur --}}
                <p style="
                    font-size:32px;
                    font-weight:800;
                    color:#111827;
                    margin:8px 0 2px;
                    line-height:1;
                ">
                    {{ $pn['total'] }}
                </p>

                {{-- Sous texte --}}
                <span style="
                    font-size:13px;
                    color:#6b7280;
                    font-weight:500;
                ">
                    Votes
                </span>

            </div>
            
            {{-- Total des satifactions --}}
            <div style="
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                padding:18px;
                background:linear-gradient(135deg,#f0fdf4,#dcfce7);
                border:1px solid #bbf7d0;
                border-radius:16px;
                box-shadow:0 2px 8px rgba(22,163,74,0.08);
                min-height:120px;
            ">

                {{-- Titre --}}
                <p style="
                    font-size:12px;
                    font-weight:700;
                    letter-spacing:0.05em;
                    color:#15803d;
                    text-transform:uppercase;
                    margin:0;
                ">
                    Satisfaits
                </p>

                {{-- Valeur --}}
                <p style="
                    font-size:32px;
                    font-weight:800;
                    color:#16a34a;
                    margin:8px 0 4px;
                    line-height:1;
                ">
                    {{ $pn['satisfaits'] }}
                </p>

                {{-- Taux --}}
                <div style="
                    display:flex;
                    align-items:center;
                    gap:6px;
                    margin-top:4px;
                ">

                    <span style="
                        font-size:13px;
                        font-weight:600;
                        color:#166534;
                    ">
                        {{ $pn['taux_satisfait'] }}%
                    </span>

                </div>

            </div>

            {{-- Total des satifactions moyennes --}}
            <div style="
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                padding:18px;
                background:linear-gradient(135deg,#fffbeb,#fef3c7);
                border:1px solid #fde68a;
                border-radius:16px;
                box-shadow:0 2px 8px rgba(217,119,6,0.08);
                min-height:120px;
            ">

                {{-- Titre --}}
                <p style="
                    font-size:12px;
                    font-weight:700;
                    letter-spacing:0.05em;
                    color:#b45309;
                    text-transform:uppercase;
                    margin:0;
                ">
                    Moyens
                </p>

                {{-- Valeur --}}
                <p style="
                    font-size:32px;
                    font-weight:800;
                    color:#d97706;
                    margin:8px 0 4px;
                    line-height:1;
                ">
                    {{ $pn['moyens'] }}
                </p>

                {{-- Taux --}}
                <div style="
                    display:flex;
                    align-items:center;
                    gap:6px;
                    margin-top:4px;
                ">

                    <span style="
                        font-size:13px;
                        font-weight:600;
                        color:#92400e;
                    ">
                        {{ $pn['taux_moyen'] }}%
                    </span>
                </div>
            </div>

            {{-- Total des insatifactions --}}
            <div style="
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                padding:18px;
                background:linear-gradient(135deg,#fef2f2,#fee2e2);
                border:1px solid #fecaca;
                border-radius:16px;
                box-shadow:0 2px 8px rgba(239,68,68,0.08);
                min-height:120px;
            ">

                {{-- Titre --}}
                <p style="
                    font-size:12px;
                    font-weight:700;
                    letter-spacing:0.05em;
                    color:#b91c1c;
                    text-transform:uppercase;
                    margin:0;
                ">
                    Insatisfaits
                </p>

                {{-- Valeur --}}
                <p style="
                    font-size:32px;
                    font-weight:800;
                    color:#ef4444;
                    margin:8px 0 4px;
                    line-height:1;
                ">
                    {{ $pn['insatisfaits'] }}
                </p>

                {{-- Taux --}}
                <div style="
                    display:flex;
                    align-items:center;
                    gap:6px;
                    margin-top:4px;
                ">

                    <span style="
                        font-size:13px;
                        font-weight:600;
                        color:#991b1b;
                    ">
                        {{ $pn['taux_insatisfait'] }}%
                    </span>
                </div>
            </div>

            <div>
                {{-- Histogramme --}}
                <canvas id="chart-niveau" style="max-height:250px;"></canvas>
                <script id="data-niveau" type="application/json">{!! json_encode($chartData['parNiveau']) !!}</script>
            </div>
        </div>
        @endif

    </div>

    {{-- ===================================================
         SECTION 2 : Évolution temporelle (Courbe)
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        
        <p style="font-size:15px; font-weight:600; color:#374151; margin-bottom:16px;">
            Évolution du taux de satisfaction dans le temps
        

             {{-- Sélecteur période --}}
            <label style="font-size:12px; font-weight:600; gap:8px; color:#9ca3af; 
                            text-transform:uppercase; margin-left:60px">
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
        </p>

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
                        border: 1px solid {{ $anomalie['niveau'] === 'critique' ? '#fecaca' : '#fde68a' }};
                        border-radius:10px;">

                {{--    critique → 🚨  (sirène rouge — situation grave)
                        warning  → ⚠️  (triangle orange — attention requise)    --}}
                <span style="font-size:20px;">
                    {{ $anomalie['niveau'] === 'critique' ? '🚨' : '⚠️' }}
                </span>

                <div style="flex:1;">

                    {{-- Nom du site --}}
                    <p style="font-size:13px; font-weight:600;
                              color: {{ $anomalie['niveau'] === 'critique' ? '#b91c1c' : '#b45309' }};
                              margin:0;">
                        {{ $anomalie['site'] }}
                    </p>

                    {{-- Comparaison chiffrée --}}
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
                Faible insatisfaction (-30%)
            </span>
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#f59e0b; border-radius:2px; display:inline-block;"></span>
                Insatisfaction modérée (30-60%)
            </span>
            <span style="display:flex; align-items:center; gap:4px;">
                <span style="width:12px; height:12px; background:#ef4444; border-radius:2px; display:inline-block;"></span>
                Fort taux d'insatisfaction (+60%)
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