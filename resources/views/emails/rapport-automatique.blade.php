<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif; background:#f9fafb; padding:24px; margin:0;">
<div style="max-width:600px; margin:0 auto; background:white;
            border-radius:12px; padding:24px; border:1px solid #e5e7eb;">

    {{-- En-tête --}}
    <div style="background:#1e40af; border-radius:8px; padding:16px; margin-bottom:20px;">
        <h2 style="color:white; margin:0; font-size:16px;">
            Rapport de Satisfaction Client
        </h2>
        <p style="color:#bfdbfe; margin:4px 0 0; font-size:13px;">
            Période : {{ $debut }} → {{ $fin }}
        </p>
    </div>

    {{-- Résumé global --}}
    @php
        $totalGlobal = array_sum(array_column($donnees, 'total'));
        $satisfaitsGlobal = array_sum(array_column($donnees, 'satisfaits'));
        $tauxGlobal = $totalGlobal > 0
            ? round(($satisfaitsGlobal / $totalGlobal) * 100, 1) : 0;
        $colorGlobal = $tauxGlobal >= 70 ? '#16a34a'
            : ($tauxGlobal >= 40 ? '#d97706' : '#ef4444');
    @endphp

    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <div style="flex:1; background:#f0fdf4; border-radius:8px; padding:12px; text-align:center;">
            <p style="font-size:11px; color:#6b7280; margin:0;">Total avis</p>
            <p style="font-size:24px; font-weight:700; color:#111827; margin:4px 0 0;">
                {{ $totalGlobal }}
            </p>
        </div>
        <div style="flex:1; background:#f0fdf4; border-radius:8px; padding:12px; text-align:center;">
            <p style="font-size:11px; color:#6b7280; margin:0;">Taux satisfaction</p>
            <p style="font-size:24px; font-weight:700; color:{{ $colorGlobal }}; margin:4px 0 0;">
                {{ $tauxGlobal }}%
            </p>
        </div>
        <div style="flex:1; background:#f0fdf4; border-radius:8px; padding:12px; text-align:center;">
            <p style="font-size:11px; color:#6b7280; margin:0;">Sites analysés</p>
            <p style="font-size:24px; font-weight:700; color:#111827; margin:4px 0 0;">
                {{ count($donnees) }}
            </p>
        </div>
    </div>

    {{-- Tableau des sites --}}
    <table style="width:100%; border-collapse:collapse; font-size:12px;">
        <thead>
            <tr style="background:#f9fafb;">
                <th style="padding:8px; text-align:left; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                    Site
                </th>
                <th style="padding:8px; text-align:center; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                    Total
                </th>
                <th style="padding:8px; text-align:center; color:#16a34a; border-bottom:1px solid #e5e7eb;">
                    Satisfaits
                </th>
                <th style="padding:8px; text-align:center; color:#16a34a; border-bottom:1px solid #e5e7eb;">
                    Moyens
                </th>
                <th style="padding:8px; text-align:center; color:#ef4444; border-bottom:1px solid #e5e7eb;">
                    Insatisfaits
                </th>
                <th style="padding:8px; text-align:center; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                    Taux de satisfaction
                </th>
                <th style="padding:8px; text-align:center; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                    Taux moyen
                </th>
                <th style="padding:8px; text-align:center; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                    Taux d'insatisfaction
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($donnees as $ligne)
            @php
                $color = $ligne['taux_satisfaction'] >= 70 ? '#16a34a'
                    : ($ligne['taux_satisfaction'] >= 40 ? '#d97706' : '#ef4444');
            @endphp
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:8px; font-weight:600; color:#111827;">
                    {{ $ligne['site'] }}
                    <br>
                    <span style="font-size:11px; color:#9ca3af; font-weight:normal;">
                        {{ $ligne['region'] }}
                    </span>
                </td>
                <td style="padding:8px; text-align:center; color:#111827;">{{ $ligne['total'] }}</td>
                <td style="padding:8px; text-align:center; color:#16a34a; font-weight:600;">
                    {{ $ligne['satisfaits'] }}
                </td>
                <td style="padding:8px; text-align:center; color:#16a34a; font-weight:600;">
                    {{ $ligne['moyens'] }}
                </td>
                <td style="padding:8px; text-align:center; color:#ef4444; font-weight:600;">
                    {{ $ligne['insatisfaits'] }}
                </td>
                <td style="padding:8px; text-align:center; font-weight:700; color:{{ $color }};">
                    {{ $ligne['taux_satisfaction'] }}%
                </td>
                <td style="padding:8px; text-align:center; font-weight:700; color:{{ $color }};">
                    {{ $ligne['taux_moyen'] }}%
                </td>
                <td style="padding:8px; text-align:center; font-weight:700; color:{{ $color }};">
                    {{ $ligne['taux_insatisfaction'] }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:11px; color:#9ca3af; margin-top:20px; text-align:center;">
        QoS — Système de Collecte de Satisfaction Client —
        Généré le {{ now()->format('d/m/Y H:i') }}
    </p>

</div>
</body>
</html>