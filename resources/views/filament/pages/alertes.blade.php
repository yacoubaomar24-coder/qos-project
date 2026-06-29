<x-filament-panels::page>
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- ===================================================
         EN-TÊTE
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:16px 20px; display:flex; justify-content:space-between;
                align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div>
            <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">
                Alertes & Notifications
            </h2>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">
                Surveillance en temps réel des seuils d'insatisfaction
            </p>
        </div>

        {{-- Bouton test manuel --}}
        <button wire:click="testerSeuils"
            style="background:#f59e0b; color:white; border:none; border-radius:8px;
                   padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer;">
            Vérifier les alertes
        </button>
    </div>

    {{-- ===================================================
         SECTION 2 : Historique des alertes
    =================================================== --}}
    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px;
                padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:15px; font-weight:600; color:#374151; margin:0;">
                Historique des alertes
            </h3>

            {{-- Filtres statut --}}
            <div style="display:flex; gap:8px;">
                @foreach(['toutes' => 'Toutes', 'nouvelle' => 'Nouvelles', 'vue' => 'Vues', 'resolue' => 'Résolues'] as $val => $label)
                <button wire:click="changerFiltre('{{ $val }}')"
                    style="padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600;
                           cursor:pointer; border:1px solid #e5e7eb;
                           background:{{ $filtreStatut === $val ? '#111827' : 'white' }};
                           color:{{ $filtreStatut === $val ? 'white' : '#374151' }};">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        @if(empty($alertes))
            <div style="text-align:center; padding:32px; color:#9ca3af;">
                <p style="font-size:14px;">✅ Aucune alerte pour le moment.</p>
            </div>
        @else
            @foreach($alertes as $alerte)
            <div style="display:flex; align-items:center; gap:12px; padding:12px;
                        margin-bottom:8px; border-radius:10px;
                        background:{{ $alerte['statut'] === 'nouvelle' ? '#fef2f2' : ($alerte['statut'] === 'vue' ? '#fffbeb' : '#f0fdf4') }};
                        border:1px solid {{ $alerte['statut'] === 'nouvelle' ? '#fecaca' : ($alerte['statut'] === 'vue' ? '#fde68a' : '#bbf7d0') }};">

                {{-- Icône --}}
                <span style="font-size:20px; flex-shrink:0;">
                    {{ $alerte['statut'] === 'nouvelle' ? '🚨' : ($alerte['statut'] === 'vue' ? '⚠️' : '✅') }}
                </span>

                {{-- Infos --}}
                <div style="flex:1;">
                    <p style="font-size:13px; font-weight:600; color:#111827; margin:0;">
                        {{ $alerte['site']['nom'] ?? 'N/A' }}
                    </p>
                    <p style="font-size:12px; color:#6b7280; margin:4px 0 0;">
                        Taux insatisfaction : <strong style="color:#ef4444;">{{ $alerte['taux_insatisfaction'] }}%</strong>
                        — Seuil : {{ $alerte['seuil_configure'] }}%
                        — {{ $alerte['total_votes'] }} votes
                    </p>
                    <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">
                        {{ \Carbon\Carbon::parse($alerte['created_at'])->timezone('Africa/Niamey')->format('d/m/Y H:i') }}
                        — Email : {{ $alerte['email_envoye'] ? '✅ envoyé' : '❌ non envoyé' }}
                    </p>
                </div>

                {{-- Actions --}}
                <div style="display:flex; gap:8px; flex-shrink:0;">
                    @if($alerte['statut'] === 'nouvelle')
                    <button wire:click="marquerVue({{ $alerte['id'] }})"
                        style="background:#f59e0b; color:white; border:none; border-radius:6px;
                               padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                        Marquer vue
                    </button>
                    @endif
                    @if($alerte['statut'] !== 'resolue')
                    <button wire:click="marquerResolue({{ $alerte['id'] }})"
                        style="background:#22c55e; color:white; border:none; border-radius:6px;
                               padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                        Résoudre
                    </button>
                    <button wire:click="renvoyerNotification({{ $alerte['id'] }})"
                        style="background:#3b82f6; color:white; border:none; border-radius:6px;
                            padding:4px 10px; font-size:11px; font-weight:600; cursor:pointer;">
                        Renvoyer mail
                    </button>
                    @endif
                </div>

            </div>
            @endforeach
        @endif

    </div>

</div>
</x-filament-panels::page>