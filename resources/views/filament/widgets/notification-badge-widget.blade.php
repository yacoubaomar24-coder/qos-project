<div wire:poll.30s="chargerAlertes"
     style="position:fixed; top:12px; right:80px; z-index:999;">
    <a href="{{ $urlAlertes }}"
       style="position:relative; display:inline-flex; align-items:center;
              text-decoration:none; padding:6px; border-radius:8px;
              color:{{ $nombreAlertes > 0 ? '#ef4444' : '#6b7280' }};"
       title="{{ $nombreAlertes }} alerte(s) nouvelle(s)">

        <svg style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($nombreAlertes > 0)
        <span style="position:absolute; top:-4px; right:-4px;
                     background:#ef4444; color:white;
                     font-size:10px; font-weight:700;
                     min-width:18px; height:18px; padding:0 4px;
                     border-radius:999px;
                     display:flex; align-items:center; justify-content:center;">
            {{ $nombreAlertes > 99 ? '99+' : $nombreAlertes }}
        </span>
        @endif

    </a>
</div>