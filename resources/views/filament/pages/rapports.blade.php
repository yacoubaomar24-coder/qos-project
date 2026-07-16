<x-filament-panels::page>
<div wire:poll.300000ms="verifierEtEnvoyerRapports" style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
     EN-TÊTE
    =================================================== --}}
    <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);
                border:1px solid #e5e7eb; border-radius:18px;
                padding:20px 22px; box-shadow:0 8px 22px rgba(15,23,42,0.06);
                display:flex; align-items:center; justify-content:space-between; gap:18px;">

        <div style="display:flex; align-items:center; gap:14px; min-width:0;">
            <div style="width:46px; height:46px; border-radius:14px;
                        background:#ecfdf5; color:#16a34a;
                        display:flex; align-items:center; justify-content:center;
                        flex-shrink:0; box-shadow:inset 0 0 0 1px rgba(22,163,74,0.12);">
                <svg style="width:24px; height:24px;" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3v12" />
                    <path d="M8 11l4 4 4-4" />
                    <path d="M5 19h14" />
                    <path d="M7 19a2 2 0 01-2-2v-1" />
                    <path d="M17 19a2 2 0 002-2v-1" />
                </svg>
            </div>

            <div style="min-width:0;">
                <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0; line-height:1.2;">
                    Export & Rapports
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:5px 0 0; line-height:1.45;">
                    Générez et téléchargez vos données de vote aux formats PDF, Excel ou CSV.
                </p>
            </div>
        </div>

        <span style="font-size:12px; font-weight:800; color:#15803d;
                    background:#dcfce7; border:1px solid #bbf7d0;
                    border-radius:999px; padding:6px 11px; white-space:nowrap;">
            Export
        </span>
    </div>

    {{-- ===================================================
         SECTION 1 : Configuration de l'export
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#ecfdf5; color:#16a34a;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v10" />
                        <path d="M8 9l4 4 4-4" />
                        <path d="M5 18h14" />
                        <path d="M7 18a2 2 0 01-2-2" />
                        <path d="M17 18a2 2 0 002-2" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Configurer l’export
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:3px 0 0;">
                        Choisissez la période, les sites concernés et le format du fichier.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#15803d;
                        background:#dcfce7; border:1px solid #bbf7d0;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                Export
            </span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; align-items:start;">

            {{-- Période --}}
            <div style="padding:16px; border:1px solid #eef2f7; border-radius:12px; background: #cbcdd1;">
                <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:10px;">
                    Période d’export
                </label>

                <div style="display:flex; align-items:center; gap:2px; padding:2px; background: #9ca3af; 
                            border:1px solid #e5e7eb; border-radius:8px; flex-wrap:wrap;">
                    @foreach([
                        'day'    => "Aujourd’hui",
                        'week'   => 'Cette semaine',
                        'month'  => 'Ce mois',
                        'year'   => 'Cette année',
                        'custom' => 'Personnalisée',
                    ] as $val => $label)
                        <button
                            type="button"
                            wire:click="changerPeriode('{{ $val }}')"
                            style="padding:7px 12px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none;
                                background:{{ $exportPeriode === $val ? '#111827' : 'transparent' }};
                                color:{{ $exportPeriode === $val ? '#ffffff' : '#4b5563' }};
                                box-shadow:{{ $exportPeriode === $val ? '0 1px 3px rgba(0,0,0,0.16)' : 'none' }};
                                white-space:nowrap;">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                @if($exportPeriode === 'custom')
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-top:14px;">
                        <div>
                            <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; display:block; margin-bottom:6px;">
                                Date début
                            </label>
                            <input type="date" wire:model="exportDateDebut"
                                style="width:100%; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#ffffff; color:#111827;">
                        </div>

                        <div>
                            <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; display:block; margin-bottom:6px;">
                                Date fin
                            </label>
                            <input type="date" wire:model="exportDateFin"
                                style="width:100%; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#ffffff; color:#111827;">
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sites --}}
            <div style="padding:16px; border:1px solid #eef2f7; border-radius:12px; background: #87a8d0;">
                <label style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:10px;">
                    Sites à inclure
                </label>

                <div style="display:flex; flex-wrap:wrap; gap:2px; margin-bottom:12px;">
                    @foreach([
                        'tous' => 'Tous les sites',
                        'pays' => 'Par pays',
                        'region' => 'Par région',
                        'ville' => 'Par ville',
                        'site' => 'Site spécifique',
                    ] as $val => $label)

                        @continue($val === 'pays' && empty($paysOptions))
                        @continue($val === 'region' && empty($regionsOptions))
                        @continue($val === 'ville' && empty($villesOptions))
                        @continue($val === 'site' && count($sitesOptions) <= 1)

                        <button
                            type="button"
                            wire:click="changerFiltreNiveau('{{ $val }}')"
                            style="padding:7px 13px; border-radius:999px; font-size:12px; font-weight:700; cursor:pointer;
                                border:1px solid {{ $filtreNiveau === $val ? '#111827' : '#e5e7eb' }};
                                background:{{ $filtreNiveau === $val ? '#111827' : '#ffffff' }};
                                color:{{ $filtreNiveau === $val ? '#ffffff' : '#374151' }};">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                @if($filtreNiveau === 'pays' && !empty($paysOptions))
                    <select wire:model="filtrePaysId" style="width:100%; max-width:320px; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#f9fafb;">
                        <option value="">Sélectionner un pays</option>
                        @foreach($paysOptions as $id => $nom)
                            <option value="{{ $id }}">{{ $nom }}</option>
                        @endforeach
                    </select>
                @endif

                @if($filtreNiveau === 'region' && !empty($regionsOptions))
                    <select wire:model="filtreRegionId" style="width:100%; max-width:320px; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#f9fafb;">
                        <option value="">Sélectionner une région</option>
                        @foreach($regionsOptions as $id => $nom)
                            <option value="{{ $id }}">{{ $nom }}</option>
                        @endforeach
                    </select>
                @endif

                @if($filtreNiveau === 'ville' && !empty($villesOptions))
                    <select wire:model="filtreVilleId" style="width:100%; max-width:320px; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#f9fafb;">
                        <option value="">Sélectionner une ville</option>
                        @foreach($villesOptions as $id => $nom)
                            <option value="{{ $id }}">{{ $nom }}</option>
                        @endforeach
                    </select>
                @endif

                @if($filtreNiveau === 'site' && !empty($sitesOptions))
                    <select wire:model="filtreSiteId" style="width:100%; max-width:320px; border:1px solid #d1d5db; border-radius:9px; padding:9px 11px; font-size:13px; background:#f9fafb;">
                        <option value="">Sélectionner un site</option>
                        @foreach($sitesOptions as $id => $nom)
                            <option value="{{ $id }}">{{ $nom }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

        </div>

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
     SECTION 2 : Rapports automatiques
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#eff6ff; color:#2563eb;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16v12H4z" />
                        <path d="M4 7l8 6 8-6" />
                        <path d="M18 3v4" />
                        <path d="M16 5h4" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Rapports automatiques par email
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:3px 0 0;">
                        Planifiez l’envoi régulier des rapports aux destinataires définis.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#1d4ed8;
                        background:#dbeafe; border:1px solid #bfdbfe;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                Email
            </span>
        </div>

        {{-- Indicateur de modification en cours --}}
            @if($rapportEnCoursId)
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;
                        padding:8px 12px; margin-top:8px; font-size:12px; color:#1d4ed8;">
                ✏️ Mode modification — cliquez "Configurer le rapport" pour sauvegarder
                <button wire:click="$set('rapportEnCoursId', null)"
                    style="background:none; border:none; color:#6b7280; cursor:pointer;
                        font-size:11px; margin-left:8px; text-decoration:underline;">
                    Annuler
                </button>
            </div>
            @endif
            
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

                {{-- Bouton modifier — charge les valeurs dans le formulaire --}}
                <button wire:click="modifierRapport({{ $rapport['id'] }})"
                    style="background:#3b82f6; color:white; border:none; border-radius:6px;
                        padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    Modifier
                </button>

                {{-- Bouton activer/désactiver --}}
                <button wire:click="toggleRapport({{ $rapport['id'] }})"
                    style="background:{{ $rapport['actif'] ? '#ef4444' : '#22c55e' }}; color:white;
                        border:none; border-radius:6px; padding:4px 10px;
                        font-size:11px; font-weight:600; cursor:pointer;">
                    {{ $rapport['actif'] ? 'Désactiver' : 'Activer' }}
                </button>

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

            {{-- Indicateur de modification en cours --}}
            @if($rapportEnCoursId)
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;
                        padding:8px 12px; margin-top:8px; font-size:12px; color:#1d4ed8;">
                ✏️ Mode modification — cliquez "Configurer le rapport" pour sauvegarder
                <button wire:click="$set('rapportEnCoursId', null)"
                    style="background:none; border:none; color:#6b7280; cursor:pointer;
                        font-size:11px; margin-left:8px; text-decoration:underline;">
                    Annuler
                </button>
            </div>
            @endif
            
        </div>
        @endif

    </div>

</div>
</x-filament-panels::page>