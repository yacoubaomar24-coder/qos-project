<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
     EN-TÊTE
    =================================================== --}}
    <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);
                border:1px solid #e5e7eb; border-radius:18px;
                padding:20px 22px; box-shadow:0 8px 22px rgba(15,23,42,0.06);
                display:flex; align-items:center; justify-content:space-between; gap:18px;">

        <div style="display:flex; align-items:center; gap:14px; min-width:0;">
            <div style="width:46px; height:46px; border-radius:14px;
                        background:#eef2ff; color:#4f46e5;
                        display:flex; align-items:center; justify-content:center;
                        flex-shrink:0; box-shadow:inset 0 0 0 1px rgba(79,70,229,0.12);">
                <svg style="width:24px; height:24px;" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="5" width="16" height="14" rx="3" />
                    <path d="M8 9h8" />
                    <path d="M8 13h5" />
                    <path d="M16 13h.01" />
                </svg>
            </div>

            <div style="min-width:0;">
                <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0; line-height:1.2;">
                    Paramètres & Configuration
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:5px 0 0; line-height:1.45;">
                    Ajustez les préférences, l’apparence et le comportement du système.
                </p>
            </div>
        </div>

        <span style="font-size:12px; font-weight:800; color:#4338ca;
                    background:#eef2ff; border:1px solid #c7d2fe;
                    border-radius:999px; padding:6px 11px; white-space:nowrap;">
            Configuration
        </span>
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

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px;">
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:42px; height:42px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:inset 0 0 0 1px rgba(37,99,235,0.10);">
                    <svg style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h4M10 17h4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h3M4 16h3M17 8h3M17 16h3" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:18px; font-weight:800; color:#111827; margin:0;">
                        Libellés des boutons de satisfaction
                    </h3>
                    <p style="font-size:13px; color:#6b7280; margin:5px 0 0; line-height:1.45;">
                        Personnalisez les textes et les couleurs affichés dans les statistiques et autres.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe; border-radius:999px; padding:5px 10px; white-space:nowrap;">
                IoT
            </span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px;">

            {{-- Satisfait --}}
            <div style="position:relative; overflow:hidden; border:1px solid #bbf7d0; border-radius:16px; 
                        padding:18px; background:linear-gradient(180deg,#ffffff 0%,#f0fdf4 100%); 
                        box-shadow:0 8px 20px rgba(22,163,74,0.08);">

                <div style="position:absolute; top:0; left:0; right:0; height:4px; 
                        background:{{ $couleurSatisfait }};"></div>

                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
                    <div style="display:flex; align-items:center; gap:11px;">
                        <div style="width:38px; height:38px; border-radius:12px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px rgba(21,128,61,0.12);">
                            <svg style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a10 10 0 100-20 10 10 0 000 20z" />
                            </svg>
                        </div>

                        <div>
                            <p style="font-size:15px; font-weight:800; color:#111827; margin:0;">
                                Bouton positif
                            </p>
                            <p style="font-size:12px; color:#166534; margin:3px 0 0; font-weight:600;">
                                Réponse favorable
                            </p>
                        </div>
                    </div>

                    <span style="font-size:11px; font-weight:800; color:#166534; background:#dcfce7; border:1px solid #bbf7d0; border-radius:999px; padding:4px 9px; white-space:nowrap;">
                        Positif
                    </span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:11px; font-weight:800; color:#14532d; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:7px;">
                        Libellé du bouton
                    </label>

                    <input type="text" wire:model="libellesatisfait"
                        placeholder="Satisfait"
                        style="width:100%; border:1px solid #bbf7d0; border-radius:10px; padding:10px 12px; font-size:13px; background:#ffffff; color:#111827; box-sizing:border-box; outline:none;">
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px; border:1px solid #bbf7d0; border-radius:12px; background:rgba(255,255,255,0.72); margin-bottom:14px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <span style="width:32px; height:32px; border-radius:10px; background:{{ $couleurSatisfait }}; border:3px solid #ffffff; box-shadow:0 0 0 1px rgba(0,0,0,0.08); flex-shrink:0;"></span>

                        <div style="min-width:0;">
                            <p style="font-size:12px; font-weight:800; color:#111827; margin:0;">
                                Couleur d’affichage
                            </p>
                            <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">
                                {{ $couleurSatisfait }}
                            </p>
                        </div>
                    </div>

                    <input type="color" wire:model="couleurSatisfait"
                        value="{{ $couleurSatisfait }}"
                        style="width:42px; height:36px; border:1px solid #e5e7eb; border-radius:10px; cursor:pointer; background:white; padding:2px; flex-shrink:0;">
                </div>

                <div>
                    <label style="font-size:11px; font-weight:800; color:#14532d; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:8px;">
                        Aperçu utilisateur
                    </label>

                    <div style="display:flex; justify-content:center;">
                        <div style="min-width:190px; padding:11px 16px; border-radius:999px; background:{{ $couleurSatisfait }}; color:white; text-align:center; font-size:13px; font-weight:800; box-shadow:0 8px 18px rgba(22,163,74,0.25);">
                            {{ $libellesatisfait ?: 'Satisfait' }}
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Moyen --}}
            <div style="position:relative; overflow:hidden; border:1px solid #f8e3a3; border-radius:16px; padding:18px; background:linear-gradient(180deg,#ffffff 0%,#fffbeb 100%); box-shadow:0 8px 20px rgba(146,64,14,0.08);">

                {{-- Accent discret --}}
                <div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{ $couleurMoyen }};"></div>

                {{-- En-tête --}}
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
                    <div style="display:flex; align-items:center; gap:11px;">
                        <div style="width:38px; height:38px; border-radius:12px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px rgba(180,83,9,0.12);">
                            <svg style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a10 10 0 100-20 10 10 0 000 20z" />
                            </svg>
                        </div>

                        <div>
                            <p style="font-size:15px; font-weight:800; color:#111827; margin:0;">
                                Bouton moyen
                            </p>
                            <p style="font-size:12px; color:#92400e; margin:3px 0 0; font-weight:600;">
                                Réponse intermédiaire
                            </p>
                        </div>
                    </div>

                    <span style="font-size:11px; font-weight:800; color:#92400e; background:#fef3c7; border:1px solid #fde68a; border-radius:999px; padding:4px 9px; white-space:nowrap;">
                        Moyen
                    </span>
                </div>

                {{-- Libellé --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:11px; font-weight:800; color:#78350f; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:7px;">
                        Libellé du bouton
                    </label>

                    <input type="text" wire:model="libelleMoyen"
                        placeholder="Moyennement satisfait"
                        style="width:100%; border:1px solid #f3d675; border-radius:10px; padding:10px 12px; font-size:13px; background:#ffffff; color:#111827; box-sizing:border-box; outline:none;">
                </div>

                {{-- Couleur --}}
                <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px; border:1px solid #fde68a; border-radius:12px; background:rgba(255,255,255,0.72); margin-bottom:14px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <span style="width:32px; height:32px; border-radius:10px; background:{{ $couleurMoyen }}; border:3px solid #ffffff; box-shadow:0 0 0 1px rgba(0,0,0,0.08); flex-shrink:0;"></span>

                        <div style="min-width:0;">
                            <p style="font-size:12px; font-weight:800; color:#111827; margin:0;">
                                Couleur d’affichage
                            </p>
                            <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">
                                {{ $couleurMoyen }}
                            </p>
                        </div>
                    </div>

                    <input type="color" wire:model="couleurMoyen"
                        value="{{ $couleurMoyen }}"
                        style="width:42px; height:36px; border:1px solid #e5e7eb; border-radius:10px; cursor:pointer; background:white; padding:2px; flex-shrink:0;">
                </div>

                {{-- Aperçu --}}
                <div>
                    <label style="font-size:11px; font-weight:800; color:#78350f; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:8px;">
                        Aperçu utilisateur
                    </label>

                    <div style="display:flex; justify-content:center;">
                        <div style="min-width:190px; padding:11px 16px; border-radius:999px; background:{{ $couleurMoyen }}; color:white; text-align:center; font-size:13px; font-weight:800; box-shadow:0 8px 18px rgba(245,158,11,0.25);">
                            {{ $libelleMoyen ?: 'Moyennement satisfait' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Insatisfait --}}
            <div style="position:relative; overflow:hidden; border:1px solid #fecaca; border-radius:16px; padding:18px; background:linear-gradient(180deg,#ffffff 0%,#fef2f2 100%); box-shadow:0 8px 20px rgba(185,28,28,0.08);">

                <div style="position:absolute; top:0; left:0; right:0; height:4px; background:{{ $couleurInsatisfait }};"></div>

                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
                    <div style="display:flex; align-items:center; gap:11px;">
                        <div style="width:38px; height:38px; border-radius:12px; background:#fee2e2; color:#b91c1c; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px rgba(185,28,28,0.12);">
                            <svg style="width:20px; height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a10 10 0 100-20 10 10 0 000 20z" />
                            </svg>
                        </div>

                        <div>
                            <p style="font-size:15px; font-weight:800; color:#111827; margin:0;">
                                Bouton négatif
                            </p>
                            <p style="font-size:12px; color:#991b1b; margin:3px 0 0; font-weight:600;">
                                Réponse défavorable
                            </p>
                        </div>
                    </div>

                    <span style="font-size:11px; font-weight:800; color:#991b1b; background:#fee2e2; border:1px solid #fecaca; border-radius:999px; padding:4px 9px; white-space:nowrap;">
                        Négatif
                    </span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:11px; font-weight:800; color:#7f1d1d; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:7px;">
                        Libellé du bouton
                    </label>

                    <input type="text" wire:model="libelleInsatisfait"
                        placeholder="Insatisfait"
                        style="width:100%; border:1px solid #fecaca; border-radius:10px; padding:10px 12px; font-size:13px; background:#ffffff; color:#111827; box-sizing:border-box; outline:none;">
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px; border:1px solid #fecaca; border-radius:12px; background:rgba(255,255,255,0.72); margin-bottom:14px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <span style="width:32px; height:32px; border-radius:10px; background:{{ $couleurInsatisfait }}; border:3px solid #ffffff; box-shadow:0 0 0 1px rgba(0,0,0,0.08); flex-shrink:0;"></span>

                        <div style="min-width:0;">
                            <p style="font-size:12px; font-weight:800; color:#111827; margin:0;">
                                Couleur d’affichage
                            </p>
                            <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">
                                {{ $couleurInsatisfait }}
                            </p>
                        </div>
                    </div>

                    <input type="color" wire:model="couleurInsatisfait"
                        value="{{ $couleurInsatisfait }}"
                        style="width:42px; height:36px; border:1px solid #e5e7eb; border-radius:10px; cursor:pointer; background:white; padding:2px; flex-shrink:0;">
                </div>

                <div>
                    <label style="font-size:11px; font-weight:800; color:#7f1d1d; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:8px;">
                        Aperçu utilisateur
                    </label>

                    <div style="display:flex; justify-content:center;">
                        <div style="min-width:190px; padding:11px 16px; border-radius:999px; background:{{ $couleurInsatisfait }}; color:white; text-align:center; font-size:13px; font-weight:800; box-shadow:0 8px 18px rgba(239,68,68,0.25);">
                            {{ $libelleInsatisfait ?: 'Insatisfait' }}
                        </div>
                    </div>
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

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#eff6ff; color:#2563eb;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 6v6l4 2" />
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Plages horaires d’activité
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0; line-height:1.4;">
                        Définissez quand les dispositifs IoT peuvent accepter des votes.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#1d4ed8;
                        background:#dbeafe; border:1px solid #bfdbfe;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                IoT
            </span>
        </div>

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
                    1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi',
                    4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 0 => 'Dimanche'
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
        <div style="background: #f9fafb; border-radius:10px; padding:12px; margin-bottom:16px;">
            <p style="font-size:14px; color: #374151; margin:0;">
                Les dispositifs seront actifs de
                <strong>{{ $heureDebut }}</strong> à <strong>{{ $heureFin }}</strong>
                @if(!empty($joursActifs))
                    les
                    @php
                        $jourLabels = [0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'];
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

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#f5f3ff; color:#7c3aed;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3h7a2 2 0 012 2v7" />
                        <path d="M12 21H5a2 2 0 01-2-2v-7" />
                        <path d="M16 3v5h5" />
                        <path d="M8 21v-5H3" />
                        <path d="M8 8l8 8" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Personnalisation de l’interface
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0; line-height:1.4;">
                        Logo et couleurs de votre organisation, appliqués au panel et aux rapports PDF.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#6d28d9;
                        background:#f3e8ff; border:1px solid #e9d5ff;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                Branding
            </span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px;">

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

            {{-- Couleur secondaire --}}
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Couleur secondaire (boutons, accents)
                </label>
                <div style="display:flex; align-items:center; gap:10px;">
                    <input type="color" wire:model="couleurSecondaire"
                        value="{{ $couleurSecondaire }}"
                        style="width:50px; height:38px; border:none;
                               border-radius:8px; cursor:pointer;">
                    <input type="text" wire:model="couleurSecondaire"
                        placeholder="#111827"
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

            <input type="file"
                wire:model="organisationLogo"
                accept="image/png,image/jpeg,image/svg+xml"
                style="border:1px solid #e5e7eb; border-radius:8px;
                    padding:8px 12px; font-size:13px; background:#f9fafb; width:100%;">

            {{-- Indicateur de chargement --}}
            <div wire:loading wire:target="organisationLogo"
                style="font-size:12px; color:#6b7280; margin-top:4px;">
                Chargement en cours...
            </div>

            {{-- Aperçu avant sauvegarde --}}
            @if($organisationLogo)
                <div style="margin-top:8px;">
                    <p style="font-size:12px; color:#6b7280; margin:0 0 4px;">Aperçu :</p>
                    <img src="{{ $organisationLogo->temporaryUrl() }}"
                        style="max-height:60px; border-radius:6px; border:1px solid #e5e7eb;">
                </div>
            @endif

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
                <div style="background:{{ $couleurSecondaire }}; border-radius:8px;
                            padding:8px 16px; color:white; font-size:13px; font-weight:600;">
                    {{ $organisationNom ?: 'Mon Organisation' }}
                </div>
                <div style="background:{{ $couleurPrimaire }}; border-radius:8px;
                            padding:8px 16px; color:white; font-size:13px; font-weight:600;
                            opacity:0.8;">
                    Bouton primaire exemple
                </div>
                <div style="background:{{ $couleurSecondaire }}; border-radius:8px;
                            padding:8px 16px; color:white; font-size:13px; font-weight:600;
                            opacity:0.8;">
                    Bouton secondaire exemple
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
        SECTION 4 : Configuration des seuils d'alerte
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#fef2f2; color:#dc2626;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v4" />
                        <path d="M12 17h.01" />
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Seuils d’alerte d’insatisfaction
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0; line-height:1.4;">
                        Déclenchez une alerte lorsque le taux d’insatisfaction dépasse le seuil défini.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#b91c1c;
                        background:#fee2e2; border:1px solid #fecaca;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                Alerte
            </span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">

            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Site (vide = global)
                </label>
                <select wire:model="seuilSiteId"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                        padding:8px 12px; font-size:13px; background:#f9fafb;">
                    <option value="">Tous les sites</option>
                    @foreach($sitesOptions as $id => $nom)
                        <option value="{{ $id }}">{{ $nom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Seuil insatisfaction (%)
                </label>
                <input type="number" wire:model="seuilPourcentage"
                    min="1" max="100"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                        padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>

            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="font-size:11px; font-weight:600; color:#9ca3af; text-transform:uppercase;">
                    Période (heures)
                </label>
                <input type="number" wire:model="seuilPeriode"
                    min="1" max="168"
                    style="border:1px solid #e5e7eb; border-radius:8px;
                        padding:8px 12px; font-size:13px; background:#f9fafb;">
            </div>

        </div>

        <button wire:click="sauvegarderSeuil"
            style="background:#22c55e; color:white; border:none; border-radius:8px;
                padding:8px 20px; font-size:13px; font-weight:600; cursor:pointer;">
            Sauvegarder le seuil
        </button>

        {{-- Seuils existants --}}
        @if(!empty($seuils))
        <div style="margin-top:16px; border-top:1px solid #f3f4f6; padding-top:16px;">
            @foreach($seuils as $seuil)
            <div style="display:flex; align-items:center; gap:12px; padding:10px 12px;
                        background:#f9fafb; border-radius:8px; margin-bottom:8px;
                        border:1px solid #e5e7eb;">
                <div style="flex:1;">
                    <p style="font-size:13px; font-weight:600; color:#111827; margin:0;">
                        {{ $seuil['site_id'] ? ($seuil['site']['nom'] ?? 'N/A') : 'Global' }}
                    </p>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0;">
                        Seuil : {{ $seuil['seuil_insatisfaction'] }}% —
                        Période : {{ $seuil['periode_heures'] }}h
                    </p>
                </div>
                <button wire:click="modifierSeuil({{ $seuil['id'] }})"
                    style="background:#3b82f6; color:white; border:none; border-radius:6px;
                        padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    Modifier
                </button>
                <button wire:click="toggleSeuil({{ $seuil['id'] }})"
                    style="background:{{ $seuil['actif'] ? '#ef4444' : '#22c55e' }};
                        color:white; border:none; border-radius:6px;
                        padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                    {{ $seuil['actif'] ? 'Désactiver' : 'Activer' }}
                </button>
                <span style="background:{{ $seuil['actif'] ? '#dcfce7' : '#fee2e2' }};
                            color:{{ $seuil['actif'] ? '#15803d' : '#b91c1c' }};
                            font-size:11px; padding:2px 8px; border-radius:999px; font-weight:600;">
                    {{ $seuil['actif'] ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ===================================================
         SECTION 5 : Récapitulatif de la configuration actuelle
         Vue d'ensemble de tous les paramètres en un coup d'oeil
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px;
                            background:#f8fafc; color:#475569;
                            display:flex; align-items:center; justify-content:center;
                            flex-shrink:0; border:1px solid #e5e7eb;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                    </svg>
                </div>

                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Récapitulatif de la configuration
                    </h3>
                    <p style="font-size:12px; color:#6b7280; margin:3px 0 0;">
                        Vérifiez les paramètres appliqués à votre organisation.
                    </p>
                </div>
            </div>

            <span style="font-size:11px; font-weight:800; color:#334155;
                        background:#f1f5f9; border:1px solid #e2e8f0;
                        border-radius:999px; padding:4px 8px; white-space:nowrap;">
                Synthèse
            </span>
        </div>

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