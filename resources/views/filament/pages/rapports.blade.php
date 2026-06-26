<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         EN-TÊTE
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">
            Export & Rapports
        </h2>
        <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">
            Exportez vos données de satisfaction en PDF, Excel ou CSV
        </p>
    </div>

    {{-- ===================================================
         SECTION 1 : Configuration de l'export
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Configurer l'export
        </h3>

        {{-- Sélection de la période --}}
        <div style="margin-bottom:16px;">
            <label style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; display:block; margin-bottom:8px;">
                Période
            </label>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                @foreach([
                    'day'    => "Aujourd'hui",
                    'week'   => 'Cette semaine',
                    'month'  => 'Ce mois',
                    'year'   => 'Cette année',
                    'custom' => 'Personnalisée',
                ] as $val => $label)
                <button wire:click="changerPeriode('{{ $val }}')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px;
                           font-weight:600; cursor:pointer; border:1px solid #e5e7eb;
                           background:{{ $exportPeriode === $val ? '#111827' : 'white' }};
                           color:{{ $exportPeriode === $val ? 'white' : '#374151' }};">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Dates personnalisées --}}
        @if($exportPeriode === 'custom')
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Date début
                </label>
                <input type="date" wire:model="exportDateDebut"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Date fin
                </label>
                <input type="date" wire:model="exportDateFin"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>
        </div>
        @endif

        {{-- Sélection des sites --}}
        <div style="margin-bottom:20px;">
            <label style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; display:block; margin-bottom:8px;">
                Sites à inclure
            </label>

            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                <button wire:click="changerFiltreNiveau('tous')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                        cursor:pointer; border:1px solid #e5e7eb;
                        background:{{ $filtreNiveau === 'tous' ? '#111827' : 'white' }};
                        color:{{ $filtreNiveau === 'tous' ? 'white' : '#374151' }};">
                    Tous les sites
                </button>
                @if(!empty($paysOptions))
                <button wire:click="changerFiltreNiveau('pays')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                        cursor:pointer; border:1px solid #e5e7eb;
                        background:{{ $filtreNiveau === 'pays' ? '#111827' : 'white' }};
                        color:{{ $filtreNiveau === 'pays' ? 'white' : '#374151' }};">
                    Par pays
                </button>
                @endif
                @if(!empty($regionsOptions))
                <button wire:click="changerFiltreNiveau('region')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                        cursor:pointer; border:1px solid #e5e7eb;
                        background:{{ $filtreNiveau === 'region' ? '#111827' : 'white' }};
                        color:{{ $filtreNiveau === 'region' ? 'white' : '#374151' }};">
                    Par région
                </button>
                @endif
                @if(!empty($villesOptions))
                <button wire:click="changerFiltreNiveau('ville')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                        cursor:pointer; border:1px solid #e5e7eb;
                        background:{{ $filtreNiveau === 'ville' ? '#111827' : 'white' }};
                        color:{{ $filtreNiveau === 'ville' ? 'white' : '#374151' }};">
                    Par ville
                </button>
                @endif
                @if(count($sitesOptions) > 1)
                <button wire:click="changerFiltreNiveau('site')"
                    style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                        cursor:pointer; border:1px solid #e5e7eb;
                        background:{{ $filtreNiveau === 'site' ? '#111827' : 'white' }};
                        color:{{ $filtreNiveau === 'site' ? 'white' : '#374151' }};">
                    Site spécifique
                </button>
                @endif
            </div>
        </div>

        {{-- Select selon le niveau --}}
        @if($filtreNiveau === 'pays' && !empty($paysOptions))
        <select wire:model="filtrePaysId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner un pays</option>
            @foreach($paysOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif

        @if($filtreNiveau === 'region' && !empty($regionsOptions))
        <select wire:model="filtreRegionId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner une région</option>
            @foreach($regionsOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif

        @if($filtreNiveau === 'ville' && !empty($villesOptions))
        <select wire:model="filtreVilleId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                    font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner une ville</option>
            @foreach($villesOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif 
        @if($filtreNiveau === 'site' && !empty($sitesOptions))
        <select wire:model="filtreSiteId"
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
            @if($filtreNiveau === 'tous')
                Tous les sites accessibles seront inclus
            @elseif($filtreNiveau === 'pays' && $filtrePaysId)
                Sites du pays sélectionné
            @elseif($filtreNiveau === 'region' && $filtreRegionId)
                Sites de la région sélectionnée
            @elseif($filtreNiveau === 'ville' && $filtreVilleId)
                Sites de la ville sélectionnée
            @elseif($filtreNiveau === 'site' && $filtreSiteId)
                Site spécifique sélectionné
            @else
                Veuillez sélectionner un filtre
            @endif
        </p>

        {{-- Format et boutons d'export --}}
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">

            {{-- Export PDF --}}
            <button wire:click="exporterPdf"
                style="display:flex; align-items:center; gap:8px;
                       background:#ef4444; color:white; border:none;
                       border-radius:8px; padding:10px 20px;
                       font-size:13px; font-weight:600; cursor:pointer;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Exporter PDF
            </button>

            {{-- Export Excel --}}
            <button wire:click="exporterExcel"
                style="display:flex; align-items:center; gap:8px;
                       background:#16a34a; color:white; border:none;
                       border-radius:8px; padding:10px 20px;
                       font-size:13px; font-weight:600; cursor:pointer;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter Excel
            </button>

            {{-- Export CSV --}}
            <button wire:click="exporterCsv"
                style="display:flex; align-items:center; gap:8px;
                       background:#3b82f6; color:white; border:none;
                       border-radius:8px; padding:10px 20px;
                       font-size:13px; font-weight:600; cursor:pointer;">
                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter CSV
            </button>

        </div>

    </div>

    {{-- ===================================================
         SECTION 2 : Aperçu des données
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Aperçu des données
        </h3>

        @php $apercu = $this->getApercu(); @endphp

        @if(empty($apercu))
            <p style="color:#9ca3af; text-align:center; padding:20px;">
                Aucune donnée pour la période sélectionnée.
            </p>
        @else

        {{-- Résumé global --}}
        @php
            $totalGlobal      = array_sum(array_column($apercu, 'total'));
            $satisfaitsGlobal = array_sum(array_column($apercu, 'satisfaits'));
            $tauxGlobal       = $totalGlobal > 0
                ? round(($satisfaitsGlobal / $totalGlobal) * 100, 1) : 0;
            $colorGlobal      = $tauxGlobal >= 70 ? '#16a34a' : ($tauxGlobal >= 40 ? '#d97706' : '#ef4444');
        @endphp

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">
            <div style="background:#f9fafb; border-radius:10px; padding:12px; text-align:center;">
                <p style="font-size:11px; color:#6b7280; margin:0;">Total avis</p>
                <p style="font-size:24px; font-weight:700; color:#111827; margin:4px 0 0;">
                    {{ $totalGlobal }}
                </p>
            </div>
            <div style="background:#f9fafb; border-radius:10px; padding:12px; text-align:center;">
                <p style="font-size:11px; color:#6b7280; margin:0;">Taux satisfaction global</p>
                <p style="font-size:24px; font-weight:700; color:{{ $colorGlobal }}; margin:4px 0 0;">
                    {{ $tauxGlobal }}%
                </p>
            </div>
            <div style="background:#f9fafb; border-radius:10px; padding:12px; text-align:center;">
                <p style="font-size:11px; color:#6b7280; margin:0;">Sites inclus</p>
                <p style="font-size:24px; font-weight:700; color:#111827; margin:4px 0 0;">
                    {{ count($apercu) }}
                </p>
            </div>
        </div>

        {{-- Tableau d'aperçu --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;">
                        <th style="padding:10px 12px; text-align:left; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">#</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Site</th>
                        <th style="padding:10px 12px; text-align:left; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Région</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Total</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#16a34a; border-bottom:1px solid #e5e7eb;">Satisfaits</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#d97706; border-bottom:1px solid #e5e7eb;">Moyens</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#ef4444; border-bottom:1px solid #e5e7eb;">Insatisfaits</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Taux satisfait</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Taux moyen</th>
                        <th style="padding:10px 12px; text-align:center; font-weight:600;
                                   color:#6b7280; border-bottom:1px solid #e5e7eb;">Taux insatisfait</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apercu as $index => $ligne)
                    @php
                        $colorTaux = $ligne['taux_satisfaction'] >= 70 ? '#16a34a'
                            : ($ligne['taux_satisfaction'] >= 40 ? '#d97706' : '#ef4444');
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 12px; color:#9ca3af;">{{ $index + 1 }}</td>
                        <td style="padding:10px 12px; font-weight:600; color:#111827;">
                            {{ $ligne['site'] }}
                        </td>
                        <td style="padding:10px 12px; color:#6b7280;">{{ $ligne['region'] }}</td>
                        <td style="padding:10px 12px; text-align:center; color:#111827;">
                            {{ $ligne['total'] }}
                        </td>
                        <td style="padding:10px 12px; text-align:center; color:#16a34a; font-weight:600;">
                            {{ $ligne['satisfaits'] }}
                        </td>
                        <td style="padding:10px 12px; text-align:center; color:#d97706; font-weight:600;">
                            {{ $ligne['moyens'] }}
                        </td>
                        <td style="padding:10px 12px; text-align:center; color:#ef4444; font-weight:600;">
                            {{ $ligne['insatisfaits'] }}
                        </td>
                        <td style="padding:10px 12px; text-align:center;
                                   font-weight:700; color:{{ $colorTaux }};">
                            {{ $ligne['taux_satisfaction'] }}%
                        </td>
                        <td style="padding:10px 12px; text-align:center;
                                   font-weight:700; color:{{ $colorTaux }};">
                            {{ $ligne['taux_moyen'] }}%
                        </td>
                        <td style="padding:10px 12px; text-align:center;
                                   font-weight:700; color:{{ $ligne['taux_insatisfait'] >= 60 ? '#ef4444' : ($ligne['taux_insatisfait'] >= 30 ? '#d97706' : '#16a34a') }};">
                            {{ $ligne['taux_insatisfait'] }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @endif

    </div>

    {{-- ===================================================
     SECTION 2 : Rapports automatiques
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Rapports automatiques par email
        </h3>

        {{-- Formulaire de configuration --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">

            {{-- Fréquence --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Fréquence
                </label>
                <select wire:model="rapportFrequence"
                    style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                        font-size:13px; background:#f9fafb;">
                    <option value="quotidien">Quotidien (chaque jour à 8h)</option>
                    <option value="hebdomadaire">Hebdomadaire (chaque lundi à 8h)</option>
                    <option value="mensuel">Mensuel (1er du mois à 8h)</option>
                </select>
            </div>

            {{-- Email --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Email de destination
                </label>
                <input type="email" wire:model="rapportEmail"
                    placeholder="admin@example.com"
                    style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                        font-size:13px; background:#f9fafb;">
            </div>

            {{-- Bouton --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af;
                            text-transform:uppercase; opacity:0;">Actions</label>
                <button wire:click="sauvegarderRapportAuto"
                    style="background:#3b82f6; color:white; border:none; border-radius:8px;
                        padding:8px 20px; font-size:13px; font-weight:600; cursor:pointer;">
                    Configurer le rapport
                </button>
            </div>

        </div>

        {{-- Sites à inclure --}}
        <div style="margin-bottom:16px;">
            <label style="font-size:11px; font-weight:600; color:#9ca3af;
                        text-transform:uppercase; display:block; margin-bottom:8px;">
                Sélection des sites
            </label>
            <button wire:click="changerRapportFiltreNiveau('tous')"
                style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                    cursor:pointer; border:1px solid #e5e7eb;
                    background:{{ $rapportFiltreNiveau === 'tous' ? '#111827' : 'white' }};
                    color:{{ $rapportFiltreNiveau === 'tous' ? 'white' : '#374151' }};">
                Tous les sites
            </button>
            @if(!empty($paysOptions))
            <button wire:click="changerRapportFiltreNiveau('pays')"
                style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                    cursor:pointer; border:1px solid #e5e7eb;
                    background:{{ $rapportFiltreNiveau === 'pays' ? '#111827' : 'white' }};
                    color:{{ $rapportFiltreNiveau === 'pays' ? 'white' : '#374151' }};">
                Par pays
            </button>
            @endif
            @if(!empty($regionsOptions))
            <button wire:click="changerRapportFiltreNiveau('region')"
                style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                    cursor:pointer; border:1px solid #e5e7eb;
                    background:{{ $rapportFiltreNiveau === 'region' ? '#111827' : 'white' }};
                    color:{{ $rapportFiltreNiveau === 'region' ? 'white' : '#374151' }};">
                Par région
            </button>
            @endif
            @if(!empty($villesOptions))
            <button wire:click="changerRapportFiltreNiveau('ville')"
                style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                    cursor:pointer; border:1px solid #e5e7eb;
                    background:{{ $rapportFiltreNiveau === 'ville' ? '#111827' : 'white' }};
                    color:{{ $rapportFiltreNiveau === 'ville' ? 'white' : '#374151' }};">
                Par ville
            </button>
            @endif
            @if(count($sitesOptions) > 1)
            <button wire:click="changerRapportFiltreNiveau('site')"
                style="padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600;
                    cursor:pointer; border:1px solid #e5e7eb;
                    background:{{ $rapportFiltreNiveau === 'site' ? '#111827' : 'white' }};
                    color:{{ $rapportFiltreNiveau === 'site' ? 'white' : '#374151' }};">
                Site spécifique
            </button>
            @endif

        </div>

        @if($rapportFiltreNiveau === 'pays' && !empty($paysOptions))
        <select wire:model="rapportFiltrePaysId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner un pays</option>
            @foreach($paysOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif
        @if($rapportFiltreNiveau === 'region' && !empty($regionsOptions))
        <select wire:model="rapportFiltreRegionId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner une région</option>
            @foreach($regionsOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif
        @if($rapportFiltreNiveau === 'ville' && !empty($villesOptions))
        <select wire:model="rapportFiltreVilleId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner une ville</option>
            @foreach($villesOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif
        @if($rapportFiltreNiveau === 'site' && !empty($sitesOptions))
        <select wire:model="rapportFiltreSiteId"
            style="border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px;
                font-size:13px; background:#f9fafb; min-width:200px;">
            <option value="">Sélectionner un site</option>
            @foreach($sitesOptions as $id => $nom)
                <option value="{{ $id }}">{{ $nom }}</option>
            @endforeach
        </select>
        @endif
        <p style="font-size:11px; color:#9ca3af; margin-top:8px;">
            @if($rapportFiltreNiveau === 'tous')
                Tous les sites accessibles seront inclus
            @elseif($rapportFiltreNiveau === 'pays' && $rapportFiltrePaysId)
                Sites du pays sélectionné
            @elseif($rapportFiltreNiveau === 'region' && $rapportFiltreRegionId)
                Sites de la région sélectionnée
            @elseif($rapportFiltreNiveau === 'ville' && $rapportFiltreVilleId)
                Sites de la ville sélectionnée
            @elseif($rapportFiltreNiveau === 'site' && $rapportFiltreSiteId)
                Site spécifique sélectionné
            @else
                Veuillez sélectionner un filtre
            @endif
        </p>

        {{-- Rapports configurés --}}
        @if(!empty($rapportsAuto))
        <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-top:16px;">
            <p style="font-size:13px; font-weight:600; color:#374151; margin:0 0 12px;">
                Rapports configurés
            </p>

            @foreach($rapportsAuto as $rapport)
            <div style="display:flex; align-items:center; gap:12px; padding:12px;
                        background:#f9fafb; border-radius:8px; margin-bottom:8px;
                        border:1px solid #e5e7eb;">

                {{-- Icône fréquence --}}
                <span style="font-size:20px;">
                    {{ $rapport['frequence'] === 'quotidien' ? '📅' : ($rapport['frequence'] === 'hebdomadaire' ? '📆' : '🗓️') }}
                </span>

                <div style="flex:1;">
                    <p style="font-size:13px; font-weight:600; color:#111827; margin:0;">
                        Rapport {{ $rapport['frequence'] }}
                    </p>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0;">
                        Envoyé à : {{ $rapport['email_destination'] }}
                        @if($rapport['derniere_execution'])
                            — Dernier envoi : {{ \Carbon\Carbon::parse($rapport['derniere_execution'])->format('d/m/Y H:i') }}
                        @else
                            — Jamais envoyé
                        @endif
                    </p>
                </div>

                {{-- Statut --}}
                <span style="background:{{ $rapport['actif'] ? '#dcfce7' : '#fee2e2' }};
                            color:{{ $rapport['actif'] ? '#15803d' : '#b91c1c' }};
                            font-size:11px; font-weight:600; padding:2px 8px; border-radius:999px;">
                    {{ $rapport['actif'] ? 'Actif' : 'Inactif' }}
                </span>

                {{-- Tester maintenant --}}
                <button wire:click="testerRapport({{ $rapport['id'] }})"
                    style="background:#f59e0b; color:white; border:none; border-radius:6px;
                        padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    Tester
                </button>

                {{-- Supprimer --}}
                <button wire:click="supprimerRapportAuto({{ $rapport['id'] }})"
                    style="background:#ef4444; color:white; border:none; border-radius:6px;
                        padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    Supprimer
                </button>

            </div>
            @endforeach
        </div>
        @endif

    </div>

</div>
</x-filament-panels::page>