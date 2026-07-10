<div class="hidden md:flex" 
     style="position: absolute !important; 
            left: 50% !important; 
            transform: translateX(-50%) !important; 
            z-index: 10 !important; 
            display: flex !important;
            flex-direction: row !important; /* Force l'alignement sur une seule ligne */
            align-items: center !important; /* Aligne le titre et la période verticalement au centre */
            gap: 20px !important; 
            white-space: nowrap !important;">
    
    {{-- Titre Tableau de bord --}}
    <div style="display: inline-flex !important; align-items: center !important; gap: 6px !important; 
            font-size: 20px; font-weight: 600; color: #1e293b;">
        <span>Tableau de bord</span>
    </div>

    {{-- Sélecteur de Période --}}
    <div style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important;">
        <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b;">
            Période
        </span>
        
        <select wire:model.live="period"
            style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px 10px; font-size: 13px;
                background: white; color: #1e293b; outline: none; cursor: pointer; min-width: 130px;">
            <option value="today">Aujourd'hui</option>
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
        </select>
    </div>
    
</div>