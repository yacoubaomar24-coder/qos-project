<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         EN-TÊTE
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">
            Paramètres & Configuration
        </h2>
        <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">
            Personnalisez le système selon vos besoins
        </p>
    </div>

    {{-- Message de confirmation --}}
    @if($message)
    <div style="background:{{ str_contains($message, 'Erreur') ? '#fef2f2' : '#f0fdf4' }};
                border:1px solid {{ str_contains($message, 'Erreur') ? '#fecaca' : '#bbf7d0' }};
                border-radius:10px; padding:12px 16px;
                color:{{ str_contains($message, 'Erreur') ? '#b91c1c' : '#15803d' }};
                font-size:13px; font-weight:500;">
        {{ $message }}
    </div>
    @endif

    {{-- ===================================================
         SECTION 1 : Libellés des boutons IoT
         Ces libellés s'affichent sur l'écran du dispositif
         physique et dans les rapports
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
            Libellés des boutons de satisfaction
        </h3>
        <p style="font-size:12px; color:#9ca3af; margin:0 0 16px;">
            Ces textes s'affichent sur les boutons physiques du dispositif IoT
            et dans les statistiques
        </p>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px;">

            {{-- Satisfait --}}
            <div style="border:1px solid #bbf7d0; border-radius:12px; padding:16px; background:#f0fdf4;">
                <label style="font-size:11px; font-weight:600; color:#15803d;
                              text-transform:uppercase; display:block; margin-bottom:8px;">
                    Bouton positif
                </label>
                {{-- Libellé --}}
                <input type="text" wire:model="libellesatisfait"
                    placeholder="Satisfait"
                    style="width:100%; border:1px solid #bbf7d0; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:white;
                           margin-bottom:8px; box-sizing:border-box;">
                {{-- Couleur --}}
                <div style="display:flex; align-items:center; gap:8px;">
                    <label style="font-size:11px; color:#6b7280;">Couleur :</label>
                    <input type="color" wire:model="couleurSatisfait"
                        value="{{ $couleurSatisfait }}"
                        style="width:40px; height:32px; border:none;
                               border-radius:6px; cursor:pointer;">
                    <span style="font-size:12px; color:#6b7280;">{{ $couleurSatisfait }}</span>
                </div>
                {{-- Aperçu du bouton --}}
                <div style="margin-top:10px; padding:10px; border-radius:8px;
                            background:{{ $couleurSatisfait }}; text-align:center;
                            color:white; font-size:13px; font-weight:600;">
                    {{ $libellesatisfait ?: 'Satisfait' }}
                </div>
            </div>

            {{-- Neutre --}}
            <div style="border:1px solid #fde68a; border-radius:12px; padding:16px; background:#fffbeb;">
                <label style="font-size:11px; font-weight:600; color:#b45309;
                              text-transform:uppercase; display:block; margin-bottom:8px;">
                    Bouton moyen
                </label>
                <input type="text" wire:model="libelleMoyen"
                    placeholder="Moyennement satisfait"
                    style="width:100%; border:1px solid #fde68a; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:white;
                           margin-bottom:8px; box-sizing:border-box;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <label style="font-size:11px; color:#6b7280;">Couleur :</label>
                    <input type="color" wire:model="couleurMoyen"
                        value="{{ $couleurMoyen }}"
                        style="width:40px; height:32px; border:none;
                               border-radius:6px; cursor:pointer;">
                    <span style="font-size:12px; color:#6b7280;">{{ $couleurMoyen }}</span>
                </div>
                <div style="margin-top:10px; padding:10px; border-radius:8px;
                            background:{{ $couleurMoyen }}; text-align:center;
                            color:white; font-size:13px; font-weight:600;">
                    {{ $libelleMoyen ?: 'Moyennement satisfait' }}
                </div>
            </div>

            {{-- Insatisfait --}}
            <div style="border:1px solid #fecaca; border-radius:12px; padding:16px; background:#fef2f2;">
                <label style="font-size:11px; font-weight:600; color:#b91c1c;
                              text-transform:uppercase; display:block; margin-bottom:8px;">
                    Bouton négatif
                </label>
                <input type="text" wire:model="libelleInsatisfait"
                    placeholder="Insatisfait"
                    style="width:100%; border:1px solid #fecaca; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:white;
                           margin-bottom:8px; box-sizing:border-box;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <label style="font-size:11px; color:#6b7280;">Couleur :</label>
                    <input type="color" wire:model="couleurInsatisfait"
                        value="{{ $couleurInsatisfait }}"
                        style="width:40px; height:32px; border:none;
                               border-radius:6px; cursor:pointer;">
                    <span style="font-size:12px; color:#6b7280;">{{ $couleurInsatisfait }}</span>
                </div>
                <div style="margin-top:10px; padding:10px; border-radius:8px;
                            background:{{ $couleurInsatisfait }}; text-align:center;
                            color:white; font-size:13px; font-weight:600;">
                    {{ $libelleInsatisfait ?: 'Insatisfait' }}
                </div>
            </div>

        </div>

        <button wire:click="sauvegarderLibelles"
            style="background:#22c55e; color:white; border:none; border-radius:8px;
                   padding:8px 20px; font-size:13px; font-weight:600; cursor:pointer;">
            Sauvegarder les libellés
        </button>

    </div>

    {{-- ===================================================
         SECTION 2 : Plages horaires d'activité
         Définit quand les dispositifs IoT acceptent les votes
         En dehors de ces plages, l'API refuse les votes
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
            Plages horaires d'activité des dispositifs
        </h3>
        <p style="font-size:12px; color:#9ca3af; margin:0 0 16px;">
            En dehors de ces plages, les dispositifs IoT n'accepteront pas de votes
        </p>

        {{-- Heures --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Heure d'ouverture
                </label>
                <input type="time" wire:model="heureDebut"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Heure de fermeture
                </label>
                <input type="time" wire:model="heureFin"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>
        </div>

        {{-- Jours actifs --}}
        <div style="margin-bottom:16px;">
            <label style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; display:block; margin-bottom:8px;">
                Jours d'activité
            </label>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                @foreach([
                    1 => 'Lun', 2 => 'Mar', 3 => 'Mer',
                    4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 0 => 'Dim'
                ] as $num => $label)
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer;
                              padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px;
                              background:{{ in_array($num, $joursActifs) ? '#f0fdf4' : 'white' }};
                              border-color:{{ in_array($num, $joursActifs) ? '#bbf7d0' : '#e5e7eb' }};">
                    <input type="checkbox" wire:model="joursActifs" value="{{ $num }}">
                    <span style="font-size:13px; font-weight:600;
                                 color:{{ in_array($num, $joursActifs) ? '#15803d' : '#374151' }};">
                        {{ $label }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Aperçu de la plage --}}
        <div style="background:#f9fafb; border-radius:10px; padding:12px; margin-bottom:16px;">
            <p style="font-size:13px; color:#374151; margin:0;">
                Les dispositifs seront actifs de
                <strong>{{ $heureDebut }}</strong> à <strong>{{ $heureFin }}</strong>
                @if(!empty($joursActifs))
                    les
                    @php
                        $jourLabels = [0=>'Dim',1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam'];
                        $jours = array_map(fn($j) => $jourLabels[$j] ?? '', $joursActifs);
                    @endphp
                    <strong>{{ implode(', ', $jours) }}</strong>
                @endif
            </p>
        </div>

        <button wire:click="sauvegarderHoraires"
            style="background:#3b82f6; color:white; border:none; border-radius:8px;
                   padding:8px 20px; font-size:13px; font-weight:600; cursor:pointer;">
            Sauvegarder les horaires
        </button>

    </div>

    {{-- ===================================================
         SECTION 3 : Personnalisation de l'interface
         Logo et couleurs affichés dans le panel Filament
         et sur les rapports PDF
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 4px;">
            Personnalisation de l'interface
        </h3>
        <p style="font-size:12px; color:#9ca3af; margin:0 0 16px;">
            Logo et couleurs de votre organisation — apparaissent dans le panel et les rapports PDF
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

            {{-- Nom de l'organisation --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Nom de l'organisation
                </label>
                <input type="text" wire:model="organisationNom"
                    placeholder="Mon Organisation"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                           padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>

            {{-- Couleur primaire --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Couleur primaire (boutons, accents)
                </label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <input type="color" wire:model="couleurPrimaire"
                        value="{{ $couleurPrimaire }}"
                        style="width:50px; height:38px; border:none;
                               border-radius:8px; cursor:pointer;">
                    <input type="text" wire:model="couleurPrimaire"
                        placeholder="#f59e0b"
                        style="flex:1; border:1px solid #e5e7eb; border-radius:8px;
                               padding:8px 12px; font-size:13px; background:#f9fafb;">
                </div>
            </div>

        </div>

        {{-- Logo --}}
        <div style="margin-bottom:16px;">
            <label style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; display:block; margin-bottom:8px;">
                Logo de l'organisation
            </label>

            @if($logoActuel)
            {{-- Logo actuel --}}
            <div style="margin-bottom:12px;">
                <p style="font-size:12px; color:#6b7280; margin:0 0 8px;">Logo actuel :</p>
                <img src="{{ Storage::url($logoActuel) }}"
                     alt="Logo"
                     style="max-height:80px; border-radius:8px;
                            border:1px solid #e5e7eb; padding:8px;">
            </div>
            @endif

            {{-- Upload --}}
            <input type="file" wire:model="organisationLogo"
                accept="image/png,image/jpeg,image/svg+xml"
                style="border:1px solid #e5e7eb; border-radius:8px;
                       padding:8px 12px; font-size:13px; background:#f9fafb; width:100%;">
            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">
                Formats acceptés : PNG, JPG, SVG — Max 2MB
            </p>
        </div>

        {{-- Aperçu --}}
        <div style="background:#f9fafb; border-radius:12px; padding:16px; margin-bottom:16px;
                    border:1px solid #e5e7eb;">
            <p style="font-size:11px; font-weight:600; color:#9ca3af;
                      text-transform:uppercase; margin:0 0 12px;">
                Aperçu
            </p>
            <div style="display:flex; align-items:center; gap:12px;">
                {{-- Simulation barre de navigation --}}
                <div style="background:{{ $couleurPrimaire }}; border-radius:8px;
                            padding:8px 16px; color:white; font-size:13px; font-weight:600;">
                    {{ $organisationNom ?: 'Mon Organisation' }}
                </div>
                <div style="background:{{ $couleurPrimaire }}; border-radius:8px;
                            padding:8px 16px; color:white; font-size:13px; font-weight:600;
                            opacity:0.8;">
                    Bouton exemple
                </div>
            </div>
        </div>

        <div style="display:flex; gap:12px;">
            <button wire:click="sauvegarderInterface"
                style="background:#f59e0b; color:white; border:none; border-radius:8px;
                       padding:8px 20px; font-size:13px; font-weight:600; cursor:pointer;">
                Sauvegarder l'interface
            </button>
        </div>

    </div>

    {{-- ===================================================
         SECTION 4 : Récapitulatif de la configuration actuelle
         Vue d'ensemble de tous les paramètres en un coup d'oeil
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0 0 16px;">
            Récapitulatif de la configuration
        </h3>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">

            {{-- Boutons IoT --}}
            <div style="background:#f9fafb; border-radius:10px; padding:12px;">
                <p style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; margin:0 0 8px;">Boutons IoT</p>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-size:12px; padding:4px 8px; border-radius:6px;
                                 background:{{ $couleurSatisfait }}; color:white; font-weight:600;">
                        ✅ {{ $libellesatisfait ?: 'Satisfait' }}
                    </span>
                    <span style="font-size:12px; padding:4px 8px; border-radius:6px;
                                 background:{{ $couleurMoyen }}; color:white; font-weight:600;">
                        😐 {{ $libelleMoyen ?: 'Moyen' }}
                    </span>
                    <span style="font-size:12px; padding:4px 8px; border-radius:6px;
                                 background:{{ $couleurInsatisfait }}; color:white; font-weight:600;">
                        ❌ {{ $libelleInsatisfait ?: 'Insatisfait' }}
                    </span>
                </div>
            </div>

            {{-- Horaires --}}
            <div style="background:#f9fafb; border-radius:10px; padding:12px;">
                <p style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; margin:0 0 8px;">Horaires d'activité</p>
                <p style="font-size:13px; font-weight:600; color:#111827; margin:0 0 4px;">
                    {{ $heureDebut }} → {{ $heureFin }}
                </p>
                @php
                    $jourLabels = [0=>'Dim',1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam'];
                    $jours = array_map(fn($j) => $jourLabels[$j] ?? '', $joursActifs);
                @endphp
                <p style="font-size:12px; color:#6b7280; margin:0;">
                    {{ implode(', ', $jours) }}
                </p>
            </div>

            {{-- Organisation --}}
            <div style="background:#f9fafb; border-radius:10px; padding:12px;">
                <p style="font-size:11px; font-weight:600; color:#9ca3af;
                          text-transform:uppercase; margin:0 0 8px;">Organisation</p>
                <p style="font-size:13px; font-weight:600; color:#111827; margin:0 0 4px;">
                    {{ $organisationNom ?: 'Non configuré' }}
                </p>
                <div style="display:flex; gap:8px; margin-top:6px;">
                    <span style="width:20px; height:20px; border-radius:4px;
                                 background:{{ $couleurPrimaire }}; display:inline-block;"
                          title="Couleur primaire"></span>
                </div>
            </div>

        </div>

    </div>

</div>
</x-filament-panels::page>