<x-filament-panels::page>

<div style="display:flex; flex-direction:column; gap:24px;">

    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px;
            padding: 20px 24px; display: flex; justify-content: space-between;
            align-items: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
    
        <div style="display: flex; align-items: flex-start; gap: 14px;">
            <!-- Indicateur d'état Live/Professionnel -->
            <div style="display: flex; align-items: center; justify-content: center; background: #fef2f2; border-radius: 8px; width: 40px; height: 40px; margin-top: 2px;">
                <span style="display: inline-block; width: 10px; height: 10px; background: #ef4444; border-radius: 50%; animate: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
            </div>

            <div>
                <h2 style="font-size: 20px; font-weight: 600; color: #0f172a; margin: 0; letter-spacing: -0.02em; line-height: 1.2;">
                    Gestion des Anomalies
                </h2>
                <p style="font-size: 13px; color: #64748b; margin: 6px 0 0; line-height: 1.4;">
                    Suivi analytique et surveillance des alertes système en temps réel
                </p>
            </div>
        </div>

    </div>

    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    
            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 20px">
                
                <!-- Icône d'alerte contextuelle stylisée -->
                <div style="display: flex; align-items: center; justify-content: center; background: #fff1f2; border: 1px solid #ffe4e6; border-radius: 10px; width: 44px; height: 44px; flex-shrink: 0; margin-top: -2px;">
                    <svg style="width: 22px; height: 22px; color: #e11d48;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>

                <div>
                    <!-- Titre plus sombre et légèrement plus grand -->
                    <h3 style="font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.01em;">
                        Seuils de Vigilance Automatiques
                    </h3>
                    
                    <!-- Description plus aérée et couleur adoucie -->
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;">
                        Alerte système si la baisse du score de satisfaction est supérieure ou égale <span style="font-weight: 600; color: #e11d48;"> 20 points</span> (Jour J vs Moyenne J-7)
                    </p>
                </div>
                
            </div>

    
        @if(empty($anomalies))
            <div style="display:flex; align-items:center; gap:10px; padding:12px;
                        background:#f0fdf4; border-radius:10px; border:1px solid #bbf7d0;">
                <span style="font-size:20px;">✅</span>
                <p style="font-size:13px; color:#15803d; margin:0; font-weight:500;">
                    Aucune anomalie détectée — tous les sites fonctionnent normalement.
                </p>
            </div>
        @else
            @foreach($anomalies as $anomalie)
            <div style="display:flex; align-items:center; gap:12px; padding:12px; margin-bottom:8px;
                        background: {{ $anomalie['niveau'] === 'critique' ? '#fef2f2' : '#fffbeb' }};
                        border: 1px solid {{ $anomalie['niveau'] === 'critique' ? '#fecaca' : '#fde68a' }};
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
</div>
</x-filament-panels::page>