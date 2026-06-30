<div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
            padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

    <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
        Détection automatique d'anomalies
    </h3>
    <p style="font-size:12px; color:#9ca3af; margin:0 0 16px;">
        Alerte si chute de satisfaction ≥ 20 points aujourd'hui vs moyenne des 7 derniers jours
    </p>

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