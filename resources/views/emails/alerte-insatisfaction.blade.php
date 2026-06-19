<!DOCTYPE html>
<html>
<body style="font-family:sans-serif; background:#f9fafb; padding:24px;">
    <div style="max-width:500px; margin:0 auto; background:white;
                border-radius:12px; padding:24px; border:1px solid #e5e7eb;">

        <div style="background:#fef2f2; border-radius:8px; padding:16px; margin-bottom:16px;">
            <h2 style="color:#b91c1c; margin:0;">🚨 Alerte Insatisfaction</h2>
        </div>

        <p style="color:#374151;">
            Le site <strong>{{ $site->nom }}</strong> a dépassé le seuil d'insatisfaction configuré.
        </p>

        <table style="width:100%; border-collapse:collapse; margin:16px 0;">
            <tr style="background:#f9fafb;">
                <td style="padding:8px 12px; font-weight:600; color:#6b7280;">Site</td>
                <td style="padding:8px 12px; color:#111827;">{{ $site->nom }}</td>
            </tr>
            <tr>
                <td style="padding:8px 12px; font-weight:600; color:#6b7280;">Taux insatisfaction</td>
                <td style="padding:8px 12px; color:#ef4444; font-weight:700;">
                    {{ $alerte->taux_insatisfaction }}%
                </td>
            </tr>
            <tr style="background:#f9fafb;">
                <td style="padding:8px 12px; font-weight:600; color:#6b7280;">Seuil configuré</td>
                <td style="padding:8px 12px; color:#111827;">{{ $alerte->seuil_configure }}%</td>
            </tr>
            <tr>
                <td style="padding:8px 12px; font-weight:600; color:#6b7280;">Total votes</td>
                <td style="padding:8px 12px; color:#111827;">{{ $alerte->total_votes }}</td>
            </tr>
            <tr style="background:#f9fafb;">
                <td style="padding:8px 12px; font-weight:600; color:#6b7280;">Date</td>
                <td style="padding:8px 12px; color:#111827;">{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <p style="color:#6b7280; font-size:13px;">
            Connectez-vous au tableau de bord pour plus de détails.
        </p>

    </div>
</body>
</html>