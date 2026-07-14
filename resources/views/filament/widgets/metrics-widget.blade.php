<div>
@if(!empty($metrics))
@php
    $taux             = $metrics['taux'] ?? 0;
    $taux_insatisfait = $metrics['taux_insatisfait'] ?? 0;
    $colorTaux        = $taux >= 70 ? '#16a34a' : ($taux >= 40 ? '#d97706' : '#ef4444');
    $colorMeilleur    = '#16a34a';
    $colorMoinsbon    = '#ef4444';
@endphp

{{-- Ligne 1 : 3 métriques --}}
<div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; 
            margin-top:-14px; margin-bottom:16px;">

    {{-- Total avis --}}
    <div style="background: #3b82f6; border:1px solid #e5e7eb; border-radius:14px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06); height: 90%;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color:white; margin:0 0 6px; font-weight:500;"> Avis collectés </p>
                <p style="font-size:28px; font-weight:700; color:white; margin:0;">{{ number_format($metrics['total']) }}</p>
            </div>
            <div style="background:#eff6ff; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Taux satisfaction --}}
    <div style="background: #6083c7; border:1px solid #e5e7eb; border-radius:14px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06);height: 90%;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color: white; margin:0 0 6px; font-weight:500;"> Avis satisfaits </p>
                <p style="font-size:28px; font-weight:700; color: white; margin:0;">{{ $metrics['satisfaits'] }}</p>
            </div>
            <div style="border-radius:8px; padding:8px;">
                {{-- svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                    path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                /svg> --}}
                <p style="font-size:24px; font-weight:700; color: white; margin:0;">{{ $taux }}%</p>
                {{-- color: {{ $colorTaux }} --}}
            </div>
        </div>
    </div>

    {{-- Sites actifs --}}
    <div style="background: #4e638f; border:1px solid #e5e7eb; border-radius:14px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06); height: 90%;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color: white; margin:0 0 6px; font-weight:500;">Sites opérationnels</p>
                <p style="font-size:28px; font-weight:700; color: white; margin:0;">
                    {{ $metrics['sitesActifs'] }}</p>
                {{-- p style="font-size:14px; color:#6b7280; margin:0 0 6px;">Sites opérationnels/p> --}}
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Sites totals --}}
    <div style="background: purple; border:1px solid #e5e7eb; border-radius:14px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06); height: 90%;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color:white; margin:0 0 6px; font-weight:500;">Sites totals</p>
                <p style="font-size:28px; font-weight:700; color:white; margin:0;">
                    {{ $metrics['sitesTotals'] }}</p>
                {{-- p style="font-size:14px; color:#6b7280; margin:0 0 6px;">Sites opérationnels/p> --}}
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@php
    /*
        Attendu :
        $metrics['sites'] = [
            ['nom' => 'Site A', 'taux' => 92],
            ['nom' => 'Site B', 'taux' => 87],
            ...
        ];
    */

    $sites = collect($metrics['sites'] ?? []);

    $topSites = $sites
        ->sortByDesc('taux')
        ->take(4)
        ->values();

    $lowSites = $sites
        ->sortBy('taux')
        ->take(4)
        ->values();
@endphp

<div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin-top: -8px;">

    {{-- Top 4 meilleurs sites --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; 
                box-shadow:0 1px 3px rgba(0,0,0,0.06); height:90%;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:26px;">
            <div style="width:40px; height:40px; border-radius:10px; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0V4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5H3v3a4 4 0 004 4M19 5h2v3a4 4 0 01-4 4" />
                </svg>
            </div>

            <div>
                <h3 style="font-size:16px; color:#111827; margin:0; font-weight:700;">
                    Top 4 — meilleurs sites
                </h3>
                <p style="font-size:12px; color:#8b8b8b; margin:2px 0 0; font-weight:600;">
                    Taux de satisfaction
                </p>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom: 2px;">
            @foreach($topSites as $index => $site)
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="width:24px; height:22px; border-radius:999px; background: #dcfce7; 
                                    color: #16a34a; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:700;">
                                {{ $index + 1 }}
                            </span>
                            <span style="font-size:14px; font-weight:600; color: #111827;">
                                {{ $site['nom'] }}
                            </span>
                        </div>

                        <span style="font-size:15px; font-weight:700; color: #16a34a;">
                            {{ $site['taux'] }}%
                        </span>
                    </div>

                    <div style="height:6px; background: #dcfce7; border-radius:999px; overflow:hidden;">
                        <div style="height:100%; width:{{ $site['taux'] }}%; 
                                background:linear-gradient(90deg, #22c55e, #16a34a); border-radius:999px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Histogramme --}}
    <div style="background:white; border:1px solid #e5e7eb;border-radius:12px;
            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06); height:90%;">
        <p style="font-size:15px; font-weight:600; color:#374151;">
            Votes par niveau
        </p>

        <pre style="display:none" id="debug-period">{{ session('dashboard_period', 'today') }}</pre>
        {{-- Conteneur graphique --}}
        <div style="position:relative;height:250px;width:100%;">
            <canvas id="chart-niveau"></canvas>
        </div>
        <script id="data-niveau" type="application/json">{!! json_encode($chartData['parNiveau']) !!}</script>
    </div>

    {{-- 4 sites les moins performants --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; 
                box-shadow:0 1px 3px rgba(0,0,0,0.06);  height:90%;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:26px;">
            <div style="width:40px; height:40px; border-radius:10px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <div>
                <h3 style="font-size:16px; color:#111827; margin:0; font-weight:700;">
                    À améliorer en priorité
                </h3>
                <p style="font-size:12px; color:#8b8b8b; margin:2px 0 0; font-weight:600;">
                    Taux de satisfaction
                </p>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:8px;">
            @foreach($lowSites as $index => $site)
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="width:24px; height:22px; border-radius:999px; background:#fee2e2; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:700;">
                                {{ $index + 1 }}
                            </span>
                            <span style="font-size:14px; font-weight:600; color:#111827;">
                                {{ $site['nom'] }}
                            </span>
                        </div>

                        <span style="font-size:15px; font-weight:700; color:#dc2626;">
                            {{ $site['taux'] }}%
                        </span>
                    </div>

                    <div style="height:6px; background: #fee2e2; border-radius:999px; overflow:hidden;">
                        <div style="height:100%; width:{{ $site['taux'] }}%; 
                            background:linear-gradient(90deg,#f97316,#dc2626); border-radius:999px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px;
            margin-top:-12px">

    {{-- Total dispositifs IoT --}}
    <div style="background:white; border:1px solid #e5e7eb; border-left:8px solid #2563eb;
                border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="background: #eff6ff; border-radius:10px; padding:9px; color:#2563eb;">
                    <svg style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h4M10 17h4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h3M4 16h3M17 8h3M17 16h3"/>
                    </svg>
                </div>

                <div>
                    <p style="font-size:18px; color: #111827; margin:0; font-weight:600;">
                        Dispositifs totals
                    </p>
                    <p style="font-size:13px; color: #787d89; margin:6px 0 0; font-weight:700;">
                        Nombre total des dispositifs enregistrés
                    </p>
                </div>
            </div>

            <div style="text-align:right;">
                <p style="font-size:28px; font-weight:800; color:#111827; margin:0; line-height:1;">
                    {{ $metrics['dispositifsTotals'] ?? 0 }}
                </p>
                <p style="font-size:13px; color: #2563eb; margin:6px 0 0; font-weight:700;">
                    dispositifs
                </p>
            </div>
        </div>
    </div>

    {{-- Actifs dispositifs IoT --}}
    <div style="background:white; border:1px solid #e5e7eb; border-left:8px solid purple;
                border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="background:#eff6ff; border-radius:10px; padding:9px; color: purple;">
                    <svg style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h4M10 17h4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h3M4 16h3M17 8h3M17 16h3"/>
                    </svg>
                </div>

                <div>
                    <p style="font-size:18px; color: #111827; margin:0; font-weight:600;">
                        Dispositifs actifs
                    </p>
                    <p style="font-size:13px; color: #787d89; margin:6px 0 0; font-weight:700;">
                        Nombre total des dispositifs actifs
                    </p>
                </div>
            </div>

            <div style="text-align:right;">
                <p style="font-size:28px; font-weight:800; color: #111827; margin:0; line-height:1;">
                    {{ $metrics['dispositifsActifs'] ?? 0 }}
                </p>
                <p style="font-size:13px; color: purple; margin:6px 0 0; font-weight:700;">
                    dispositifs
                </p>
            </div>
        </div>
    </div>
</div>
@endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function redrawChartsLater() {
    setTimeout(function () {
        drawAllCharts();
    }, 150);
}

document.addEventListener("DOMContentLoaded", redrawChartsLater);
document.addEventListener("livewire:navigated", redrawChartsLater);

document.addEventListener("livewire:init", function () {
    Livewire.hook("morph.updated", function () {
        redrawChartsLater();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const url = new URL(window.location.href);

    if (url.searchParams.has('period')) {
        url.searchParams.delete('period');

        window.history.replaceState({}, '', url.toString());
    }

});

function drawAllCharts() {
    drawNiveauChart();
}

const centerTextPlugin = {
    id: "centerText",
    beforeDraw(chart) {
        const { width, height, ctx } = chart;
        const total = chart.data.datasets[0].data.reduce((sum, value) => sum + value, 0);

        ctx.save();
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        ctx.font = "700 20px Arial";
        ctx.fillStyle = "#111827";
        ctx.fillText(total, width / 2, height / 2 - 8);

        ctx.font = "600 11px Arial";
        ctx.fillStyle = "#6b7280";
        ctx.fillText("votes", width / 2, height / 2 + 12);

        ctx.restore();
    }
};

const segmentPercentPlugin = {
    id: "segmentPercent",
    afterDatasetsDraw(chart) {
        const { ctx } = chart;
        const dataset = chart.data.datasets[0];
        const total = dataset.data.reduce((sum, value) => sum + value, 0);

        if (total <= 0) return;

        chart.getDatasetMeta(0).data.forEach((arc, index) => {
            const value = dataset.data[index];

            if (value <= 0) return;

            const percentage = ((value / total) * 100).toFixed(1) + "%";
            const position = arc.tooltipPosition();

            ctx.save();
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.font = "700 14px Arial";
            ctx.fillStyle = "#ffffff";

            ctx.shadowColor = "rgba(0,0,0,0.25)";
            ctx.shadowBlur = 4;

            ctx.fillText(percentage, position.x, position.y);
            ctx.restore();
        });
    }
};

function drawNiveauChart() {
    const dataEl = document.getElementById("data-niveau");
    const canvas = document.getElementById("chart-niveau");

    if (!dataEl || !canvas || typeof Chart === "undefined") {
        return;
    }

    const data = JSON.parse(dataEl.textContent || "{}");

    const satisfaits = data.satisfaits ?? 0;
    const moyens = data.moyens ?? 0;
    const insatisfaits = data.insatisfaits ?? 0;

    const total = satisfaits + moyens + insatisfaits;

    if (window.chartNiveau instanceof Chart) {
        window.chartNiveau.destroy();
    }

    window.chartNiveau = new Chart(canvas.getContext("2d"), {
        type: "doughnut",
        data: {
            labels: ["Satisfait", "Moyen", "Insatisfait"],
            datasets: [{
                data: [satisfaits, moyens, insatisfaits],
                backgroundColor: ["#22c55e", "#f59e0b", "#ef4444"],
                borderColor: "#ffffff",
                borderWidth: 5,
                hoverOffset: 10,
                spacing: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "38%",
            layout: {
                padding: 8
            },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true,
                        pointStyle: "circle",
                        padding: 12,
                        font: {
                            size: 12,
                            weight: "600"
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw ?? 0;
                            const percentage = total > 0
                                ? ((value / total) * 100).toFixed(1)
                                : 0;

                            return context.label + " : " + value + " votes (" + percentage + "%)";
                        }
                    }
                }
            }
        },
        plugins: [centerTextPlugin, segmentPercentPlugin]
    });
}
</script>