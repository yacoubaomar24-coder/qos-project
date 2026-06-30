<x-filament-widgets::widget>

<div style="background: linear-gradient(135deg, #0f172a, #1e3a8a);
    border-radius:20px;padding:8px 12px; color:white;position:relative; overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);">

    {{-- Contenu principal --}}
    <div style="display:flex;justify-content:space-between;align-items:center;
        gap:20px;flex-wrap:wrap; position:relative;z-index:2;">

        {{-- Partie gauche --}}
        <div style="flex:1; min-width:280px;">

            {{-- Badge --}}
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);
                padding:6px 14px;border-radius:999px;font-size:20px;font-weight:600;margin-bottom:10px;
                backdrop-filter: blur(4px);">
                📡 Tableau de bord
            </div>

            {{-- Sous-titre --}}
            <p style="margin-top:2px;font-size:15px;color:rgba(255,255,255,0.9);max-width:700px;">
                Vue d'ensemble en temps réel de tous les sites actifs
            </p>
        </div>

        {{-- Partie droite --}}
        <div style=" display:flex;flex-direction:column;gap:6px;min-width:220px;">
            <label style="font-size:12px;font-weight:600;text-transform:uppercase;
                letter-spacing:0.05em;color:rgba(255,255,255,0.8);">
                Période
            </label>

            <select wire:model.live="period"
                style="border:none;border-radius:10px;padding:12px 14px;font-size:14px;
                    background:rgba(255,255,255,0.18);color:white;backdrop-filter: blur(6px);
                    min-width:220px;outline:none;">
                <option value="today" style="color:black;"> Aujourd'hui</option>
                <option value="week" style="color:black;">Cette semaine</option>
                <option value="month" style="color:black;">Ce mois</option>
                <option value="year" style="color:black;">Cette année</option>
            </select>
        </div>
    </div>
</div>

</x-filament-widgets::widget>