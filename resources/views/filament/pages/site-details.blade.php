<x-filament-panels::page>
{{-- Conteneur principal --}}
<div style="display: flex; flex-direction: column; gap: 16px; width: 100%; max-width: 1280px;
         margin: 0 auto; padding: 4px; box-sizing: border-box; font-family: ui-sans-serif, system-ui, 
         -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc;">

    {{-- LIGNE 1 : EN-TÊTE / TOP BAR --}}
    <div style="background: #2483b78f; border: 1px solid #f1f5f9; border-radius: 12px; padding: 8px 12px; 
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);">
        {{-- Gauche : Infos du Site Dynamique --}}
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="background: white; color: white; border-radius: 12px; width: 48px; height: 48px; 
                display: flex; align-items: center; justify-content: center; font-size: 22px; 
                box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);">
                🏢
            </div>
            <div>
                <span style="font-size: 12px; color: #252b32; font-weight: 500; text-transform: uppercase; 
                        letter-spacing: 0.05em;">Site sélectionné</span>
                
                {{-- Affichage sécurisé : Si $siteStats['nom'] n'est pas dispo, on affiche une valeur par défaut propre --}}
                <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 2px 0 4px 0; 
                    letter-spacing: -0.02em;">
                    {{ $siteStats['site'] ?? 'Aucun site sélectionné' }}
                </h2>
                
                @if(!empty($siteStats) && (isset($siteStats['ville']) || isset($siteStats['region']) || isset($siteStats['pays'])))
                    <p style="font-size: 13px; color: #404a58; margin: 0; display: flex; align-items: center; gap: 4px;">
                        {{ $siteStats['ville'] ?? '' }} {{ isset($siteStats['region']) ? '- '.$siteStats['region'] : '' }}
                        {{ isset($siteStats['pays']) ? '- '.$siteStats['pays'] : '' }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Droite : Selecteur de site & Total des avis --}}
        <div style="display: flex; gap: 4px;">
            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 16px; font-size: 13px; 
                    font-weight: 500; background: #ffffff; color: #334155; 
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer; outline: none; 
                    min-width: 220px;">
                {{ $this->form }}
            </div>
            {{--  div style="display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">
                    Changer de Site</span>
                
                {{-- Utilisation directe de wire:model si défini dans votre composant, sinon wire:change classique --}}
                {{-- select wire:change="changeSite($event.target.value)"
                    style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 16px; font-size: 13px; 
                    font-weight: 500; background: #ffffff; color: #334155; 
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer; outline: none; 
                    min-width: 220px;">
                    foreach($sitesOptions as $id => $nom)
                        {{-- On laisse l'attribut sélectionné se gérer via votre logique de composant --}}
                        {{-- option value="{{ $id }}">{{ $nom }}/option>
                    endforeach
                /select>
            /div --}}

            @if(!empty($siteStats) && isset($siteStats['total']))
            <div style="text-align: right; border-left: 2px solid #f1f5f9; padding-left: 24px;">
                <span style="font-size: 16px; color: #1b0c0eb3; font-weight: 500;">Total des avis</span>
                <p style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.1; letter-spacing: -0.03em;">{{ number_format($siteStats['total'], 0, ',', ' ') }}</p>
                <span style="font-size: 14px; color: #1b2129; font-weight: 500;">votes au total</span>
            </div>
            @endif
        </div>
    </div>

    @if(empty($siteStats))
        <div style="background: white; border-radius: 16px; padding: 48px; text-align: center; color: #94a3b8; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            Aucune donnée disponible pour ce site.
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

    {{-- LIGNE 2 : LES 3 CARTES KPIS ÉPURÉES --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        
        {{-- Card Satisfait --}}
        <div style="background: white; border: 1px solid #f1f5f9; border-radius: 10px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="background:#e8f5e9;color:#2e7d32; width:46px; height:46px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center; ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"   viewBox="0 0 24 24"
                            stroke-width="2"stroke="currentColor"style="width:24px; height:24px;">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #2e7d32; margin: 0; text-transform: capitalize;">Satisfait</h4>
                        <p style="font-size: 26px; font-weight: 800; color: #2e7d32; margin: 2px 0 0 0; letter-spacing: -0.03em;">{{ $tauxSatisfaction }}%</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 16px; font-weight: 700; color: #1e293b; display: block;">
                        {{ number_format($siteStats['satisfaits'] ?? 0, 0, ',', ' ') }}</span>
                    <p style="font-size: 12px; color: #94a3b8; margin: 0; font-weight: 500;">votes satifaits</p>
                </div>
            </div>
            <div style="height: 8px; border-radius: 999px; background: #f1f5f9; overflow: hidden;">
                <div style="height: 100%; border-radius: 999px; background: #22c55e; {{ $styleSatisfait }}"></div>
            </div>
        </div>

        {{-- Card Moyen --}}
        <div style="background: white; border: 1px solid #f1f5f9; border-radius: 10px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="background:#fff3e0; color:#f59e0b; width:46px; height:46px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center; ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" style="width:28px; height:28px;">

                            <circle cx="12" cy="12" r="9"></circle> 
                            <path stroke-linecap="round" d="M9 10h.01M15 10h.01"></path> 
                            <path stroke-linecap="round" d="M9 15h6"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #b45309; margin: 0; text-transform: capitalize;">Moyen</h4>
                        <p style="font-size: 26px; font-weight: 800; color: #b45309; margin: 2px 0 0 0; letter-spacing: -0.03em;">{{ $tauxMoyen }}%</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 16px; font-weight: 700; color: #1e293b; display: block;">{{ number_format($siteStats['moyens'] ?? 0, 0, ',', ' ') }}</span>
                    <p style="font-size: 12px; color: #94a3b8; margin: 0; font-weight: 500;">votes moyens</p>
                </div>
            </div>
            <div style="height: 8px; border-radius: 999px; background: #f1f5f9; overflow: hidden;">
                <div style="height: 100%; border-radius: 999px; background: #f59e0b; {{ $styleMoyen }}"></div>
            </div>
        </div>

        {{-- Card Insatisfait --}}
        <div style="background: white; border: 1px solid #f1f5f9; border-radius: 10px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="background:#ffebee; color:#ef4444; width:46px; height:46px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center; ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" style="width:24px; height:24px;">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 15s-1.125-1.5-3-1.5S9 15 9 15m.75-5.25h.008v.008H9.75V9.75zm4.5 0h.008v.008h-.008V9.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #dc2626; margin: 0; text-transform: capitalize;">Insatisfait</h4>
                        <p style="font-size: 26px; font-weight: 800; color: #dc2626; margin: 2px 0 0 0; letter-spacing: -0.03em;">{{ $tauxInsatisfait }}%</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 16px; font-weight: 700; color: #1e293b; display: block;">{{ number_format($siteStats['insatisfaits'] ?? 0, 0, ',', ' ') }}</span>
                    <p style="font-size: 12px; color: #94a3b8; margin: 0; font-weight: 500;">votes insatisfaits</p>
                </div>
            </div>
            <div style="height: 8px; border-radius: 999px; background: #f1f5f9; overflow: hidden;">
                <div style="height: 100%; border-radius: 999px; background: #ef4444; {{ $styleInsatisfait }}"></div>
            </div>
        </div>

    </div>

    {{-- LIGNE 3 : LE GRAPHIQUE AVEC LE FILTRE DE PÉRIODE INTÉGRÉ --}}
    <div style="background: white; border: 1px solid #f1f5f9; border-radius: 14px; padding: 20px; 
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">Évolution de votes</h3>
                <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Pourcentage de votes sur la période sélectionnée</p>
            </div>
            
            {{-- Filtre de période placé ici --}}
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; 
                      letter-spacing: 0.05em;">Période :</span>
                <select wire:change="changePeriod($event.target.value)"
                    style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 13px; font-weight: 600; background: #f8fafc; color: #334155; cursor: pointer; outline: none;">
                    <option value="day"   {{ $period === 'day'   ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week"  {{ $period === 'week'  ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>30 derniers jours</option>
                </select>
            </div>
        </div>

        <div style="height: 250px; width: 100%; position: relative;">
            <canvas id="evolution-chart"></canvas>
        </div>
        
        <script id="evolution-data" type="application/json">{!! json_encode($siteStats['evolution'] ?? []) !!}</script>
    </div>
    
    {{-- LIGNE 4 : LES BLOCS DE COMPARAISON --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 0px;">
        @php
            $diffRegion   = round($tauxSatisfaction - ($siteStats['moyenne_regionale'] ?? 0), 1);
            $diffNational = round($tauxSatisfaction - ($siteStats['moyenne_nationale'] ?? 0), 1);
            
            $colorRegion   = $diffRegion >= 0 ? '#15803d' : '#b91c1c';
            $bgRegionBadge = $diffRegion >= 0 ? '#dcfce7' : '#fee2e2';
            
            $colorNational = $diffNational >= 0 ? '#15803d' : '#b91c1c';
            $bgNationalBadge = $diffNational >= 0 ? '#dcfce7' : '#fee2e2';
        @endphp

        {{-- Bloc Régional --}}
        <div style="background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="background: #f0fdfa; color: #0d9488; font-size: 20px; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    👥
                </div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Comparaison régionale</h4>
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Par rapport à la moyenne des sites de la région</p>
                </div>
            </div>
            <div style="text-align: right; display: flex; align-items: center; gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #94a3b8; font-weight: 500; display: block; margin-bottom: 2px;">Moyenne</span>
                    <strong style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $siteStats['moyenne_regionale'] ?? 0 }}%</strong>
                </div>
                <div style="background: {{ $bgRegionBadge }}; color: {{ $colorRegion }}; padding: 8px 12px; border-radius: 10px; text-align: center; min-width: 90px; box-sizing: border-box;">
                    <span style="font-size: 14px; font-weight: 800; display: block;">{{ $diffRegion >= 0 ? '+' : '' }}{{ $diffRegion }}%</span>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em;">{{ $diffRegion >= 0 ? 'Au-dessus' : 'En-dessous' }}</span>
                </div>
            </div>
        </div>

        {{-- Bloc National --}}
        <div style="background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="background: #eff6ff; color: #2563eb; font-size: 20px; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    🌐
                </div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Comparaison nationale</h4>
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Par rapport à la moyenne nationale</p>
                </div>
            </div>
            <div style="text-align: right; display: flex; align-items: center; gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #94a3b8; font-weight: 500; display: block; margin-bottom: 2px;">Moyenne</span>
                    <strong style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $siteStats['moyenne_nationale'] ?? 0 }}%</strong>
                </div>
                <div style="background: {{ $bgNationalBadge }}; color: {{ $colorNational }}; padding: 8px 12px; border-radius: 10px; text-align: center; min-width: 90px; box-sizing: border-box;">
                    <span style="font-size: 14px; font-weight: 800; display: block;">{{ $diffNational >= 0 ? '+' : '' }}{{ $diffNational }}%</span>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em;">{{ $diffNational >= 0 ? 'Au-dessus' : 'En-dessous' }}</span>
                </div>
            </div>
        </div>

    </div>
    @endif

</div>

{{-- Script Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener("siteChanged", function(event) {
    let canvas = document.getElementById("evolution-chart");
    if (!canvas) return;
    let evolution = event.detail.evolution || [];

    if (window.evolutionChart instanceof Chart) {
        window.evolutionChart.destroy();
    }

    let ctx = canvas.getContext("2d");

    window.evolutionChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: evolution.map(function(d) { return d.label; }),
            datasets: [
                {
                    label: "Satisfait (%)",
                    data: evolution.map(function(d) { return d.taux_satisfait; }),
                    borderColor: "#10b981",
                    backgroundColor: "rgba(16, 185, 129, 0.04)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 2,
                    pointHoverRadius: 5
                },
                {
                    label: "Moyen (%)",
                    data: evolution.map(function(d) { return d.taux_moyen; }),
                    borderColor: "#f59e0b",
                    backgroundColor: "rgba(245, 158, 11, 0.02)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 2,
                    pointHoverRadius: 5
                },
                {
                    label: "Insatisfait (%)",
                    data: evolution.map(function(d) { return d.taux_insatisfait; }),
                    borderColor: "#ef4444",
                    backgroundColor: "rgba(239, 68, 68, 0.02)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 2,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            plugins: { 
                legend: { 
                    position: "top", 
                    labels: { boxWidth: 8, usePointStyle: true, font: { size: 12, weight: '500' } } 
                } 
            },
            scales: {
                y: { 
                    type: "linear", 
                    min: 0, 
                    max: 100, 
                    grid: { color: "#f1f5f9" },
                    ticks: { color: "#94a3b8", font: { size: 11 } } 
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: "#94a3b8", font: { size: 11 } } 
                }
            }
        }
    });
});
</script>
</x-filament-panels::page>