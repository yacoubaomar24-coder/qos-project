<x-filament-widgets::widget>
    
    {{-- Sélecteur Période --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; 
            padding:10px; display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; 
            box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; flex-direction:column; gap:4px;">
        <label style="font-size:14px; font-weight:600; text-transform:uppercase; 
                            letter-spacing:0.05em; color:#9ca3af;">Période</label>
        <select wire:model.live="period"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; font-size:14px; 
                background:#f9fafb; color:#374151; min-width:200px;">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="year">Cette année</option>
        </select>        
        </div>
    </div>
    
</x-filament-widgets::widget>
