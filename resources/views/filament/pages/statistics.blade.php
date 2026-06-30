<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         SECTION 1 : Répartition par niveau
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                margin:0px;padding:10px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div>
                <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
                    Répartition globale par niveau de votes
                </h3>
            </div>

            {{-- Sélecteur période --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <p>
                    <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                        Période</label>
                    <select wire:change="changePeriod($event.target.value)"
                        style="border:1px solid #e5e7eb; border-radius:8px; padding:6px 12px;
                            font-size:13px; background:#f9fafb; color:#374151;">
                        <option value="day"   {{ $period === 'day'   ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week"  {{ $period === 'week'  ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="year"  {{ $period === 'year'  ? 'selected' : '' }}>Cette année</option>
                    </select>
                </p>
            </div>
        </div>
                
        @if(!empty($chartData['parNiveau']))
        @php $pn = $chartData['parNiveau']; @endphp

        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:10px;">

            {{-- Total des votes --}}
            <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:10px;
                background:linear-gradient(135deg,#ffffff,#f9fafb);border:2px solid #e5e7eb;border-radius:16px;
                box-shadow:0 2px 8px rgba(0,0,0,0.05);height:95px;">
                
                {{-- Titre --}}
                <p style="font-size:12px;font-weight:600;letter-spacing:0.04em;color:#9ca3af;
                    text-transform:uppercase;margin:0;">Total</p>

                {{-- Valeur --}}
                <p style="font-size:32px;font-weight:800;color:#111827;margin:8px 0 2px;line-height:1;">
                    {{ $pn['total'] }}
                </p>

                {{-- Sous texte --}}
                <span style=" font-size:13px;color:#6b7280;font-weight:500;">Votes</span>
            </div>
            
            {{-- Total des satifactions --}}
            <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:10px;
                background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0;border-radius:16px;
                box-shadow:0 2px 8px rgba(22,163,74,0.08);height:95px;">
                {{-- ✅ Libellé dynamique --}}
                <p style=" font-size:12px;font-weight:700;letter-spacing:0.05em;color:#15803d;
                    text-transform:uppercase;margin:0;">
                    {{ \App\Helpers\ConfigHelper::libelleSatisfait() }}
                </p>
                <p style=" font-size:32px;font-weight:800;color:#16a34a;margin:8px 0 4px;line-height:1;">
                    {{ $pn['satisfaits'] }}
                </p>
                <p style=" display:flex;align-items:center;gap:6px;margin-top:4px;">
                    <span style="font-size:13px;font-weight:600;color:#166534;">{{ $pn['taux_satisfait'] }}% </span>
                </p>
            </div>

            {{-- Total des satifactions moyennes --}}
            <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:10px;
                background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fde68a;border-radius:16px;
                box-shadow:0 2px 8px rgba(217,119,6,0.08);height:95px;">
            
                {{-- Titre --}}
                <p style=" font-size:12px;font-weight:700;letter-spacing:0.05em;color:#b45309;
                    text-transform:uppercase;margin:0;">
                    {{ \App\Helpers\ConfigHelper::libelleMoyen() }}
                </p>

                {{-- Valeur --}}
                <p style=" font-size:32px;font-weight:800;color:#d97706;margin:8px 0 4px;line-height:1;">
                    {{ $pn['moyens'] }}
                </p>
                
                {{-- Taux --}}
                <p style=" display:flex;align-items:center;gap:6px;margin-top:4px;">
                    <span style="font-size:13px;font-weight:600;color:#92400e;">{{ $pn['taux_moyen'] }}% </span>
                </p>
            </div>

            {{-- Total des insatifactions #fecaca --}}
            <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;padding:10px;
                background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1px solid #fecaca;border-radius:16px;
                box-shadow:0 2px 8px rgba(239,68,68,0.08);height:95px;
                color: {{ \App\Helpers\ConfigHelper::couleurInsatisfait()}};">

                {{-- ✅ Libellé dynamique --}}
                <p style="font-size:12px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;margin:0;
                        color: {{ \App\Helpers\ConfigHelper::couleurInsatisfait() }};">
                    {{ \App\Helpers\ConfigHelper::libelleInsatisfait() }}
                </p>
                <p style=" font-size:32px;font-weight:800;margin:8px 0 4px;line-height:1;
                           color: {{ \App\Helpers\ConfigHelper::couleurInsatisfait() }};">
                    {{ $pn['insatisfaits'] }}
                </p>
                <p style=" display:flex;align-items:center;gap:6px;margin-top:4px;">
                    <span style="font-size:13px;font-weight:600;
                        color: {{ \App\Helpers\ConfigHelper::couleurInsatisfait() }};">
                        {{ $pn['taux_insatisfait'] }}% </span>
                </p>
            </div>
            
        </div>
        @endif

    </div>

    {{-- ===================================================
         SECTION 2 : Évolution temporelle (Courbe et histogramme)
    =================================================== --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; padding:0px;margin:0px;width:100%">
        <div style="background:white; border:1px solid #e5e7eb; grid-column: span 3;
                border-radius:16px;padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <p style="font-size:15px; font-weight:600; color:#374151;margin-bottom:6px">
                Évolution des taux  dans le temps
            </p>
        
            <canvas id="chart-evolution" style="max-height:250px;"></canvas>
            <script id="data-evolution" type="application/json">{!! json_encode($chartData['evolution'] ?? []) !!}</script>
        </div>
        {{-- Histogramme --}}
        <div style="background:white; border:1px solid #e5e7eb;border-radius:16px;
                padding:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06); height:100%;">
            <p style="font-size:15px; font-weight:600; color:#374151; margin-bottom:6px">
                Histogrammes
            </p>

            {{-- Conteneur graphique --}}
            <div style="position:relative;height:250px;width:100%;">
                <canvas id="chart-niveau"></canvas>
            </div>
            <script id="data-niveau" type="application/json">{!! json_encode($chartData['parNiveau']) !!}</script>
        </div>
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

            {{-- Conteneur des lignes — chaque ligne a un attribut data-page --}}
            <div id="classement-liste">
                @foreach($chartData['classement'] as $index => $site)
                @php $page = intdiv($index, 5) + 1; @endphp
                <div class="classement-ligne" data-page="{{ $page }}"
                    style="display:flex; align-items:center; gap:12px; margin-bottom:12px; 
                    {{ $page > 1 ? 'display:none;' : '' }}">

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
            </div>

            {{-- Pagination --}}
            @php $totalPages = (int) ceil(count($chartData['classement']) / 5); @endphp

            @if($totalPages > 1)
            <div id="classement-pagination"
                style="display:flex; justify-content:center; align-items:center; gap:6px; margin-top:16px;">

                {{-- Bouton précédent --}}
                <button onclick="changerPageClassement(-1)"
                    style="border:1px solid #e5e7eb; background:white; border-radius:6px;
                        padding:6px 10px; font-size:12px; cursor:pointer; color:#374151;">
                    ‹
                </button>
            
                {{-- Numéros de page --}}
                @for($i = 1; $i <= $totalPages; $i++)
                <button onclick="allerPageClassement({{ $i }})"
                    class="page-btn-classement"
                    data-numero="{{ $i }}"
                    style="border:1px solid #e5e7eb; border-radius:6px;
                        padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer;
                        background:{{ $i === 1 ? '#111827' : 'white' }};
                        color:{{ $i === 1 ? 'white' : '#374151' }};">
                    {{ $i }}
                </button>
                @endfor
            
                {{-- Bouton suivant --}}
                <button onclick="changerPageClassement(1)"
                    style="border:1px solid #e5e7eb; background:white; border-radius:6px;
                        padding:6px 10px; font-size:12px; cursor:pointer; color:#374151;">
                    ›
                </button>
            </div>
            @endif
        @else
            <p style="color:#9ca3af; text-align:center; padding:20px;">Aucune donnée disponible.</p>
        @endif
    </div>

    {{-- ===================================================
         SECTION 4 : Heatmap horaire
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
                Insatisfaction modérée (30-59%)
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

{{-- Script de pagination — placer une seule fois sur la page --}}
<script>
    let pageClassementActuelle = 1;
    const totalPagesClassement = {{ $totalPages ?? 1 }};

    function allerPageClassement(numero) {
        pageClassementActuelle = numero;

        // Masquer toutes les lignes
        document.querySelectorAll('.classement-ligne').forEach(function(ligne) {
            ligne.style.display = (parseInt(ligne.dataset.page) === numero) ? 'flex' : 'none';
        });
        // Mettre à jour le style des boutons de page
        document.querySelectorAll('.page-btn-classement').forEach(function(btn) {
            const estActif = parseInt(btn.dataset.numero) === numero;
            btn.style.background = estActif ? '#111827' : 'white';
            btn.style.color      = estActif ? 'white' : '#374151';
        });
    }

    function changerPageClassement(direction) {
        let nouvellePage = pageClassementActuelle + direction;
        if (nouvellePage < 1) nouvellePage = 1;
        if (nouvellePage > totalPagesClassement) nouvellePage = totalPagesClassement;
        allerPageClassement(nouvellePage);
    }
</script>

{{-- ===================================================
     Scripts Chart.js
=================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dessiner tous les graphiques au chargement
document.addEventListener("DOMContentLoaded", function() {
    //awAllCharts();
    drawNiveauChart();
    drawEvolutionChart();
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
            maintainAspectRatio: false,
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
                    label: "Satisfaction (%)",
                    data: evolution.map(function(d) { return d.taux_satisfait; }),
                    borderColor: "#22c55e",
                    backgroundColor: "rgba(34,197,94,0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    yAxisID: "y"
                },
                {
                    label: "Moyen (%)",
                    data: evolution.map(function(d) { return d.taux_moyen; }),
                    borderColor: "#f59e0b",
                    backgroundColor: "rgba(245,158,11,0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    yAxisID: "y"
                },
                {
                    label: "Insatisfaction (%)",
                    data: evolution.map(function(d) { return d.taux_insatisfait; }),
                    borderColor: "#ef4444",
                    backgroundColor: "rgba(239,68,68,0.1)",
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