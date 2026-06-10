<div>
@if(!empty($metrics))
@php
    $taux        = $metrics['taux'] ?? 0;
    $colorTaux   = $taux >= 70 ? '#16a34a' : ($taux >= 40 ? '#d97706' : '#ef4444');
    $colorMeilleur = '#16a34a';
    $colorMoinsbon = '#ef4444';
@endphp

{{-- Ligne 1 : 3 métriques --}}
<div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:12px;">

    {{-- Total avis --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color:#6b7280; margin:0 0 6px; font-weight:500;">Total avis</p>
                <p style="font-size:28px; font-weight:700; color:#111827; margin:0;">{{ number_format($metrics['total']) }}</p>
                <p style="font-size:14px; color:#6b7280; margin:0 0 6px;">Avis collectés sur la période</p>
            </div>
            <div style="background:#eff6ff; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Taux satisfaction --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color:#6b7280; margin:0 0 6px; font-weight:500;">Taux de satisfaction</p>
                <p style="font-size:28px; font-weight:700; color: {{ $colorTaux }}; margin:0;">{{ $taux }}%</p>
                <p style="font-size:14px; color:#6b7280; margin:0 0 6px;">{{ $metrics['satisfaits'] }} avis satisfaits</p>
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Sites actifs --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; 
                padding:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:16px; color:#6b7280; margin:0 0 6px; font-weight:500;">Sites actifs</p>
                <p style="font-size:28px; font-weight:700; color:#111827; margin:0;">{{ $metrics['sitesActifs'] }}</p>
                <p style="font-size:14px; color:#6b7280; margin:0 0 6px;">Sites opérationnels</p>
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:8px;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- Ligne 2 : Meilleur + Moins bon --}}
<div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px;">

    {{-- Meilleur site --}}
    <div style="background:white; border:1px solid #e5e7eb; border-left:4px solid #22c55e; 
                border-radius:12px; padding:30px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="background:#f0fdf4; border-radius:8px; padding:8px;">
                    <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#22c55e" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:16px; color:#6b7280; margin:0; font-weight:500;">Meilleur site</p>
                    <p style="font-size:20px; font-weight:600; color:#111827; 
                                margin:4px 0 0;">{{ $metrics['meilleur']['nom'] }}</p>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-size:24px; font-weight:700; color:#16a34a; margin:0;">{{ $metrics['meilleur']['taux'] }}%</p>
                <p style="font-size:14px; color:#6b7280; margin:2px 0 0;">Taux satisfaction</p>
            </div>
        </div>
    </div>

    {{-- Site à améliorer --}}
    <div style="background:white; border:1px solid #e5e7eb; border-left:4px solid #ef4444; 
                border-radius:12px; padding:30px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="background:#fef2f2; border-radius:8px; padding:8px;">
                    <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:16px; color:#6b7280; margin:0; font-weight:500;">Site à améliorer</p>
                    <p style="font-size:20px; font-weight:600; color:#111827; margin:4px 0 0;">{{ $metrics['moinsbon']['nom'] }}</p>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-size:24px; font-weight:700; color:#ef4444; margin:0;">{{ $metrics['moinsbon']['taux'] }}%</p>
                <p style="font-size:14px; color:#6b7280; margin:2px 0 0;">Taux satisfaction</p>
            </div>
        </div>
    </div>

</div>
@endif
</div>