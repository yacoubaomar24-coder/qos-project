<x-filament-panels::page>

    {{-- HEADER PERSONNALISÉ --}}
    <div style="
        background: linear-gradient(135deg, #195ae6);
        border-radius: 20px;
        padding: 12px;
        margin-bottom: 12px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    ">

        <div style="position:relative; z-index:2;">

            {{-- Badge --}}
            <div style="
                display:inline-flex;
                align-items:center;
                gap:8px;
                background:rgba(255,255,255,0.15);
                padding:6px 14px;
                border-radius:999px;
                font-size:20px;
                font-weight:600;
                margin-bottom:10px;
                backdrop-filter: blur(4px);
            ">
                📡 Tableau de bord
            </div>

            {{-- Sous-titre --}}
            <p style="
                margin-top:2px;
                font-size:15px;
                color:rgba(255,255,255,0.9);
                max-width:700px;
            ">
                Vue d'ensemble en temps réel de tous les sites actifs
            </p>

        </div>
    </div>

    {{-- Widgets Filament --}}
    {{-- WIDGETS --}}
    <x-filament-widgets::widgets
        :widgets="$this->getVisibleWidgets()"
        :columns="$this->getColumns()"
    />

</x-filament-panels::page>