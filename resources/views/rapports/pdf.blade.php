<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body    { font-family: sans-serif; font-size: 12px; color: #111827; }
        h1      { font-size: 18px; color: #111827; margin-bottom: 4px; }
        p.meta  { font-size: 11px; color: #6b7280; margin: 0 0 16px; }
        table   { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th      { background: #374151; color: white; padding: 8px 10px;
                  text-align: left; font-size: 11px; }
        td      { padding: 7px 10px; border-bottom: 1px solid #e5e7eb;
                  font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }

        /* Couleurs selon le taux */
        .vert   { color: #16a34a; font-weight: bold; }
        .orange { color: #d97706; font-weight: bold; }
        .rouge  { color: #ef4444; font-weight: bold; }

        .footer { margin-top: 24px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

    {{-- En-tête --}}
    <h1>Rapport de Satisfaction Client</h1>
    <p class="meta">
        Période : {{ $debut }} → {{ $fin }} |
        Généré le : {{ $genere }}
    </p>

    {{-- Résumé global --}}
    @php
        $totalGlobal      = array_sum(array_column($donnees, 'total'));
        $satisfaitsGlobal = array_sum(array_column($donnees, 'satisfaits'));
        $tauxGlobal       = $totalGlobal > 0
            ? round(($satisfaitsGlobal / $totalGlobal) * 100, 1)
            : 0;
    @endphp

    <table style="width:auto; margin-bottom:16px;">
        <tr>
            <td style="padding:6px 12px; background:#f0fdf4; border:1px solid #bbf7d0;">
                <strong>Total avis :</strong> {{ $totalGlobal }}
            </td>
            <td style="padding:6px 12px; background:#f0fdf4; border:1px solid #bbf7d0;">
                <strong>Taux satisfaction global :</strong>
                <span class="{{ $tauxGlobal >= 70 ? 'vert' : ($tauxGlobal >= 40 ? 'orange' : 'rouge') }}">
                    {{ $tauxGlobal }}%
                </span>
            </td>
            <td style="padding:6px 12px; background:#f0fdf4; border:1px solid #bbf7d0;">
                <strong>Nombre de sites :</strong> {{ count($donnees) }}
            </td>
        </tr>
    </table>

    {{-- Tableau détaillé --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Site</th>
                <th>Ville</th>
                <th>Région</th>
                <th>Total</th>
                <th>Satisfaits</th>
                <th>Moyens</th>
                <th>Insatisfaits</th>
                <th>Taux satisfaction</th>
                <th>Taux moyen</th>
                <th>Taux insatisfaction</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donnees as $index => $ligne)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $ligne['site'] }}</strong></td>
                <td>{{ $ligne['ville'] }}</td>
                <td>{{ $ligne['region'] }}</td>
                <td style="text-align:center;">{{ $ligne['total'] }}</td>
                <td style="text-align:center; color:#16a34a;">{{ $ligne['satisfaits'] }}</td>
                <td style="text-align:center; color:#d97706;">{{ $ligne['moyens'] }}</td>
                <td style="text-align:center; color:#ef4444;">{{ $ligne['insatisfaits'] }}</td>
                <td style="text-align:center;">
                    <span class="{{ $ligne['taux_satisfaction'] >= 70 ? 'vert' : ($ligne['taux_satisfaction'] >= 40 ? 'orange' : 'rouge') }}">
                        {{ $ligne['taux_satisfaction'] }}%
                    </span>
                </td>
                <td style="text-align:center;">
                    <span class="{{ $ligne['taux_moyen'] >= 70 ? 'orange' : ($ligne['taux_moyen'] < 40 ? 'rouge' : 'vert') }}">
                        {{ $ligne['taux_moyen'] }}%
                    </span>
                </td>
                <td style="text-align:center;">
                    <span class="{{ $ligne['taux_insatisfait'] >= 60 ? 'rouge' : ($ligne['taux_insatisfait'] >= 40 ? 'orange' : 'vert') }}">
                        {{ $ligne['taux_insatisfait'] }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        QoS — Système de Collecte de Satisfaction Client
    </p>

</body>
</html>