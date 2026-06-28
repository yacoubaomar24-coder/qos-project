<div style="display:flex; align-items:center; gap:10px;">
    <img src="{{ $url }}"
         alt="Logo"
         style="height:60px; width:auto; object-fit:contain;">
    
    {{-- ✅ Nom affiché à côté du logo --}}
    @if($nom)
    <span style="font-size:22px; font-weight:700; color:#111827;">
        {{ $nom }}
    </span>
    @endif
</div>