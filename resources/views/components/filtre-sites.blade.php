{{-- Composant réutilisable pour filtrer les sites --}}
{{-- Paramètres : $prefix (export ou rapport), $user, $paysOptions, $regionsOptions, $villesOptions, $sitesOptions --}}

@php
    $niveauProp   = $prefix . 'FiltreNiveau';
    $paysProp     = $prefix . 'FiltrePaysId';
    $regionProp   = $prefix . 'FiltreRegionId';
    $villeProp    = $prefix . 'FiltreVilleId';
    $siteProp     = $prefix . 'FiltreSiteId';
    $changeAction = 'changer' . ucfirst($prefix) . 'FiltreNiveau';
@endphp

<div style="margin-bottom:16px;">
    <label style="font-size:11px; font-weight:600; color:#9ca3af;
                  text-transform:uppercase; display:block; margin-bottom:8px;">
        Sélection des sites
    </label>

    {{-- Boutons de niveau --}}
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">

        {{-- Tous les sites --}}
        <button wire:click="{{ $changeAction }}('tous')"
            style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                   cursor:pointer; border:1px solid #e5e7eb;
                   background: {{ $niveauProp === 'tous' ? '#111827' : 'white' }};
                   color: {{ $niveauProp === 'tous' ? 'white' : '#374151' }};
                   ">
            Tous les sites
        </button>

        {{-- Par pays — Super admin et Admin national --}}
        @if(!empty($paysOptions))
        <button wire:click="{{ $changeAction }}('pays')"
            style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                   cursor:pointer; border:1px solid #e5e7eb;
                   background: {{ $niveauProp === 'pays' ? '#111827' : 'white' }};
                   color: {{ $niveauProp === 'pays' ? 'white' : '#374151' }}">     
            Par pays
        </button>
        @endif

        {{-- Par région --}}
        @if(!empty($regionsOptions))
        <button wire:click="{{ $changeAction }}('region')"
            style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                   cursor:pointer; border:1px solid #e5e7eb;
                   background: {{ $niveauProp === 'region' ? '#111827' : 'white' }};
                   color: {{ $niveauProp === 'region' ? 'white' : '#374151' }};">
            Par région
        </button>
        @endif

        {{-- Par ville --}}
        @if(!empty($villesOptions))
        <button wire:click="{{ $changeAction }}('ville')"
            style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                   cursor:pointer; border:1px solid #e5e7eb;
                   background:{{ $niveauProp === 'ville' ? '#111827' : 'white' }};
                   color:{{ $niveauProp === 'ville' ? 'white' : '#374151' }};">
            Par ville
        </button>
        @endif

        {{-- Par site spécifique --}}
        @if(count($sitesOptions) > 1)
        <button wire:click="{{ $changeAction }}('site')"
            style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                   cursor:pointer; border:1px solid #e5e7eb;
                   background:{{ $niveauProp === 'site' ? '#111827' : 'white' }};
                   color:{{ $niveauProp === 'site' ? 'white' : '#374151' }};">
            Site spécifique
        </button>
        @endif
    </div>

    {{-- Select selon le niveau choisi --}}
    @if($niveauProp === 'pays' && !empty($paysOptions))
    <select wire:model="{{ $paysProp }}"
        style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
               font-size:13px; background:#f9fafb; min-width:200px;">
        <option value="">Sélectionner un pays</option>
        @foreach($paysOptions as $id => $nom)
            <option value="{{ $id }}">{{ $nom }}</option>
        @endforeach
    </select>
    @endif

    @if($niveauProp === 'region' && !empty($regionsOptions))
    <select wire:model="{{ $regionProp }}"
        style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
               font-size:13px; background:#f9fafb; min-width:200px;">
        <option value="">Sélectionner une région</option>
        @foreach($regionsOptions as $id => $nom)
            <option value="{{ $id }}">{{ $nom }}</option>
        @endforeach
    </select>
    @endif

    @if($niveauProp === 'ville' && !empty($villesOptions))
    <select wire:model="{{ $villeProp }}"
        style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
               font-size:13px; background:#f9fafb; min-width:200px;">
        <option value="">Sélectionner une ville</option>
        @foreach($villesOptions as $id => $nom)
            <option value="{{ $id }}">{{ $nom }}</option>
        @endforeach
    </select>
    @endif

    @if($niveauProp === 'site' && !empty($sitesOptions))
    <select wire:model="{{ $siteProp }}"
        style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
               font-size:13px; background:#f9fafb; min-width:200px;">
        <option value="">Sélectionner un site</option>
        @foreach($sitesOptions as $id => $nom)
            <option value="{{ $id }}">{{ $nom }}</option>
        @endforeach
    </select>
    @endif

    {{-- Résumé --}}
    <p style="font-size:11px; color:#9ca3af; margin-top:8px;">
        @if($niveauProp === 'tous')
            Tous les sites accessibles seront inclus
        @elseif($niveauProp === 'pays' && $paysProp)
            Sites du pays sélectionné
        @elseif($niveauProp === 'region' && $regionProp)
            Sites de la région sélectionnée
        @elseif($niveauProp === 'ville' && $villeProp)
            Sites de la ville sélectionnée
        @elseif($niveauProp === 'site' && $siteProp)
            Site spécifique sélectionné
        @else
            Veuillez sélectionner un filtre
        @endif
    </p>

</div>