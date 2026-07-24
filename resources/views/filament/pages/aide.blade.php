<x-filament-panels::page>
<div style="display:flex; gap:24px; align-items:flex-start;">

    {{-- ===================================================
         NAVIGATION LATÉRALE — sections de l'aide
    =================================================== --}}
    <div style="width:200px; flex-shrink:0; position:sticky; top:80px;">

        <p style="font-size:11px; font-weight:600; color:#9ca3af;
                  text-transform:uppercase; letter-spacing:0.06em; margin:0 0 8px;">
            Sections
        </p>

        <div style="display:flex; flex-direction:column; gap:2px;">
            @foreach([
                'demarrage'  => ['icon' => '🚀', 'label' => 'Démarrage'],
                'roles'      => ['icon' => '👥', 'label' => 'Rôles'],
                'dispositifs'=> ['icon' => '📱', 'label' => 'Dispositifs'],
                'alertes'    => ['icon' => '🔔', 'label' => 'Alertes'],
                'rapports'   => ['icon' => '📊', 'label' => 'Rapports'],
                'faq'        => ['icon' => '❓', 'label' => 'FAQ'],
            ] as $id => $item)
            <button onclick="afficherSection('{{ $id }}')"
                id="nav-{{ $id }}"
                style="text-align:left; padding:8px 12px; border-radius:8px;
                       font-size:13px; border:none; cursor:pointer; width:100%;
                       display:flex; align-items:center; gap:8px;
                       background:{{ $id === 'demarrage' ? '#eff6ff' : 'transparent' }};
                       color:{{ $id === 'demarrage' ? '#1d4ed8' : '#6b7280' }};
                       font-weight:{{ $id === 'demarrage' ? '600' : '400' }};">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </button>
            @endforeach
        </div>

    </div>

    {{-- ===================================================
         CONTENU PRINCIPAL
    =================================================== --}}
    <div style="flex:1; min-width:0;">

        {{-- -----------------------------------------------
             SECTION 1 : Démarrage rapide
        ----------------------------------------------- --}}
        <div id="s-demarrage" class="aide-section">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Démarrage rapide
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    Configurez le système en 4 étapes
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">

                @foreach([
                    ['num' => 1, 'titre' => 'Créer la hiérarchie géographique',
                     'desc' => 'Dans le menu : Pays → Région → Ville → Site. Commencez toujours par le pays avant de créer les niveaux inférieurs.'],
                    ['num' => 2, 'titre' => 'Ajouter les dispositifs IoT',
                     'desc' => 'Créer un dispositif par site — un token unique est généré automatiquement. Copiez-le immédiatement car il ne sera plus affiché en clair.'],
                    ['num' => 3, 'titre' => 'Configurer les paramètres',
                     'desc' => 'Dans Paramètres : personnalisez les libellés des boutons (Satisfait, Moyen, Insatisfait), les horaires d\'activité des dispositifs, le logo et les couleurs.'],
                    ['num' => 4, 'titre' => 'Configurer les alertes et rapports',
                     'desc' => 'Définissez les seuils d\'insatisfaction dans Paramètres et configurez les rapports automatiques par email dans Export & Rapports.'],
                ] as $etape)
                <div style="display:flex; gap:16px; align-items:flex-start; padding:16px;
                            background:white; border:1px solid #e5e7eb; border-radius:12px;
                            box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    {{-- Numéro étape --}}
                    <div style="width:32px; height:32px; border-radius:50%;
                                background:#eff6ff; color:#1d4ed8;
                                display:flex; align-items:center; justify-content:center;
                                font-size:14px; font-weight:700; flex-shrink:0;">
                        {{ $etape['num'] }}
                    </div>
                    <div>
                        <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 4px;">
                            {{ $etape['titre'] }}
                        </p>
                        <p style="font-size:13px; color:#6b7280; margin:0; line-height:1.5;">
                            {{ $etape['desc'] }}
                        </p>
                    </div>
                </div>
                @endforeach

            </div>

        </div>

        {{-- -----------------------------------------------
             SECTION 2 : Rôles et permissions
        ----------------------------------------------- --}}
        <div id="s-roles" class="aide-section" style="display:none;">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Rôles et permissions
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    5 niveaux d'accès hiérarchiques
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">

                @foreach([
                    ['niveau' => 1, 'role' => 'Admin',
                     'color_bg' => '#eff6ff', 'color_txt' => '#1d4ed8', 'color_border' => '#3b82f6',
                     'desc' => 'Peut uniquement créer des Super admins. N\'a pas accès aux pages de statistiques, alertes ou rapports.'],
                    ['niveau' => 2, 'role' => 'Super admin',
                     'color_bg' => '#f5f3ff', 'color_txt' => '#7c3aed', 'color_border' => '#8b5cf6',
                     'desc' => 'Accès complet à ses ressources. Crée les sites, dispositifs, admins nationaux. Configure les paramètres et personnalise l\'interface.'],
                    ['niveau' => 3, 'role' => 'Admin national',
                     'color_bg' => '#f0fdf4', 'color_txt' => '#15803d', 'color_border' => '#22c55e',
                     'desc' => 'Gère son pays entier. Voit toutes les statistiques de son pays. Crée les admins régionaux et de site.'],
                    ['niveau' => 4, 'role' => 'Admin régional',
                     'color_bg' => '#fffbeb', 'color_txt' => '#b45309', 'color_border' => '#f59e0b',
                     'desc' => 'Gère sa région uniquement. Voit les statistiques de sa région. Crée les admins de site.'],
                    ['niveau' => 5, 'role' => 'Admin de site',
                     'color_bg' => '#fef2f2', 'color_txt' => '#b91c1c', 'color_border' => '#ef4444',
                     'desc' => 'Voit uniquement son site. Consulte les statistiques du site. Les votes sont collectés automatiquement par le dispositif IoT.'],
                ] as $role)
                <div style="padding:14px 16px; background:white; border:1px solid #e5e7eb;
                            border-left:4px solid {{ $role['color_border'] }};
                            border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <p style="font-size:14px; font-weight:600; color:#111827; margin:0;">
                            {{ $role['role'] }}
                        </p>
                        <span style="font-size:11px; font-weight:600; padding:2px 10px;
                                     border-radius:999px;
                                     background:{{ $role['color_bg'] }};
                                     color:{{ $role['color_txt'] }};">
                            Niveau {{ $role['niveau'] }}
                        </span>
                    </div>
                    <p style="font-size:13px; color:#6b7280; margin:0; line-height:1.5;">
                        {{ $role['desc'] }}
                    </p>
                </div>
                @endforeach

            </div>

        </div>

        {{-- -----------------------------------------------
             SECTION 3 : Dispositifs IoT
        ----------------------------------------------- --}}
        <div id="s-dispositifs" class="aide-section" style="display:none;">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Dispositifs IoT
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    Configuration et utilisation de l'API
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">

                {{-- Token --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 8px;">
                        🔑 Token d'authentification
                    </p>
                    <p style="font-size:13px; color:#6b7280; margin:0 0 10px; line-height:1.5;">
                        Généré automatiquement à la création du dispositif.
                        Copiez-le immédiatement depuis la notification — il ne sera plus affiché en clair.
                        Format du token :
                    </p>
                    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                        <code style="font-size:12px; color:#374151; font-family:monospace;">
                            site{id}-{32 caractères aléatoires}
                        </code>
                    </div>
                    <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">
                        ⚠️ Si le token est compromis, régénérez-le depuis la liste des dispositifs.
                    </p>
                </div>

                {{-- API Vote --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 8px;">
                        📡 Enregistrer un vote
                    </p>
                    <div style="background:#1e293b; border-radius:8px; padding:12px; margin-bottom:8px;">
                        <code style="font-size:12px; color:#e2e8f0; font-family:monospace; line-height:1.8; display:block;">
                            <span style="color:#94a3b8;">POST</span> /api/v1/votes<br>
                            <span style="color:#94a3b8;">Authorization:</span> Bearer {token}<br>
                            <span style="color:#94a3b8;">Content-Type:</span> application/json<br><br>
                            {<br>
                            &nbsp;&nbsp;<span style="color:#86efac;">"niveau"</span>: <span style="color:#fca5a5;">"satisfait"</span><br>
                            }
                        </code>
                    </div>
                    <p style="font-size:12px; color:#9ca3af; margin:0;">
                        Valeurs acceptées : <strong>satisfait</strong>, <strong>neutre</strong>, <strong>insatisfait</strong>
                    </p>
                </div>

                {{-- API Info --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 8px;">
                        ℹ️ Vérifier la configuration du dispositif
                    </p>
                    <div style="background:#1e293b; border-radius:8px; padding:12px; margin-bottom:8px;">
                        <code style="font-size:12px; color:#e2e8f0; font-family:monospace; line-height:1.8; display:block;">
                            <span style="color:#94a3b8;">GET</span> /api/v1/dispositifs/info<br>
                            <span style="color:#94a3b8;">Authorization:</span> Bearer {token}
                        </code>
                    </div>
                    <p style="font-size:12px; color:#9ca3af; margin:0;">
                        Retourne les libellés, couleurs, plage horaire et statut du dispositif
                    </p>
                </div>

                {{-- Plages horaires --}}
                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px;">
                    <p style="font-size:14px; font-weight:600; color:#b45309; margin:0 0 6px;">
                        ⏰ Plages horaires d'activité
                    </p>
                    <p style="font-size:13px; color:#b45309; margin:0; line-height:1.5;">
                        Les dispositifs n'acceptent les votes que pendant les heures configurées
                        dans Paramètres. En dehors, l'API retourne une erreur 403.
                    </p>
                </div>

            </div>

        </div>

        {{-- -----------------------------------------------
             SECTION 4 : Alertes
        ----------------------------------------------- --}}
        <div id="s-alertes" class="aide-section" style="display:none;">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Alertes & Notifications
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    Surveillance automatique des seuils d'insatisfaction
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">

                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 10px;">
                        ⚙️ Configuration d'un seuil
                    </p>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px; flex-shrink:0;">1️⃣</span>
                            <p style="font-size:13px; color:#6b7280; margin:0;">
                                Aller dans <strong>Paramètres → Seuils d'alerte</strong>
                            </p>
                        </div>
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px; flex-shrink:0;">2️⃣</span>
                            <p style="font-size:13px; color:#6b7280; margin:0;">
                                Choisir un site (ou global pour tous les sites)
                            </p>
                        </div>
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px; flex-shrink:0;">3️⃣</span>
                            <p style="font-size:13px; color:#6b7280; margin:0;">
                                Définir le % d'insatisfaction et la période en heures
                            </p>
                        </div>
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px; flex-shrink:0;">4️⃣</span>
                            <p style="font-size:13px; color:#6b7280; margin:0;">
                                Les mails sont envoyés automatiquement à tous les admins concernés
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Cycle de vie --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 12px;">
                        🔄 Cycle de vie d'une alerte
                    </p>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <div style="text-align:center;">
                            <span style="display:inline-block; padding:6px 14px; background:#fef2f2;
                                         color:#b91c1c; border-radius:999px; font-size:12px; font-weight:600;">
                                🚨 Nouvelle
                            </span>
                            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">Vient d'être détectée</p>
                        </div>
                        <span style="font-size:18px; color:#9ca3af;">→</span>
                        <div style="text-align:center;">
                            <span style="display:inline-block; padding:6px 14px; background:#fffbeb;
                                         color:#b45309; border-radius:999px; font-size:12px; font-weight:600;">
                                ⚠️ Vue
                            </span>
                            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">Lue mais non résolue</p>
                        </div>
                        <span style="font-size:18px; color:#9ca3af;">→</span>
                        <div style="text-align:center;">
                            <span style="display:inline-block; padding:6px 14px; background:#f0fdf4;
                                         color:#15803d; border-radius:999px; font-size:12px; font-weight:600;">
                                ✅ Résolue
                            </span>
                            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">Problème traité</p>
                        </div>
                    </div>
                </div>

                {{-- Destinataires --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 10px;">
                        📧 Destinataires automatiques des mails
                    </p>
                    <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        @foreach(['Admin de site', 'Admin régional', 'Admin national', 'Super admin'] as $dest)
                        <span style="padding:4px 12px; background:#f3f4f6; color:#374151;
                                     border-radius:999px; font-size:12px; font-weight:500;">
                            {{ $dest }}
                        </span>
                        @if($dest !== 'Super admin')
                        <span style="color:#9ca3af; font-size:14px;">→</span>
                        @endif
                        @endforeach
                    </div>
                    <p style="font-size:12px; color:#9ca3af; margin:10px 0 0;">
                        Uniquement les comptes actifs reçoivent les notifications
                    </p>
                </div>

            </div>

        </div>

        {{-- -----------------------------------------------
             SECTION 5 : Rapports
        ----------------------------------------------- --}}
        <div id="s-rapports" class="aide-section" style="display:none;">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Rapports & Exports
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    Export manuel et envoi automatique par email
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">

                {{-- Formats --}}
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                                padding:16px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                        <p style="font-size:24px; margin:0 0 8px;">📄</p>
                        <p style="font-size:14px; font-weight:600; color:#ef4444; margin:0 0 4px;">PDF</p>
                        <p style="font-size:12px; color:#6b7280; margin:0;">Rapport formaté en paysage avec résumé et tableau</p>
                    </div>
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                                padding:16px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                        <p style="font-size:24px; margin:0 0 8px;">📊</p>
                        <p style="font-size:14px; font-weight:600; color:#16a34a; margin:0 0 4px;">Excel</p>
                        <p style="font-size:12px; color:#6b7280; margin:0;">Feuille de calcul avec mise en forme et colonnes</p>
                    </div>
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                                padding:16px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                        <p style="font-size:24px; margin:0 0 8px;">📋</p>
                        <p style="font-size:14px; font-weight:600; color:#3b82f6; margin:0 0 4px;">CSV</p>
                        <p style="font-size:12px; color:#6b7280; margin:0;">Données brutes séparées par point-virgule</p>
                    </div>
                </div>

                {{-- Rapports automatiques --}}
                <div style="background:white; border:1px solid #e5e7eb; border-radius:12px;
                            padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 12px;">
                        ⏰ Rapports automatiques par email
                    </p>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach([
                            ['freq' => 'Quotidien', 'moment' => 'Chaque jour à 8h00', 'desc' => 'Données du jour précédent'],
                            ['freq' => 'Hebdomadaire', 'moment' => 'Chaque lundi à 8h00', 'desc' => 'Données de la semaine précédente'],
                            ['freq' => 'Mensuel', 'moment' => 'Le 1er du mois à 8h00', 'desc' => 'Données du mois précédent'],
                        ] as $r)
                        <div style="display:flex; justify-content:space-between; align-items:center;
                                    padding:10px 12px; background:#f9fafb; border-radius:8px;">
                            <div>
                                <p style="font-size:13px; font-weight:600; color:#111827; margin:0;">
                                    {{ $r['freq'] }}
                                </p>
                                <p style="font-size:11px; color:#9ca3af; margin:2px 0 0;">{{ $r['desc'] }}</p>
                            </div>
                            <span style="font-size:12px; color:#6b7280; font-weight:500;">
                                {{ $r['moment'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        {{-- -----------------------------------------------
             SECTION 6 : FAQ
        ----------------------------------------------- --}}
        <div id="s-faq" class="aide-section" style="display:none;">

            <div style="margin-bottom:20px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827; margin:0 0 4px;">
                    Questions fréquentes
                </h2>
                <p style="font-size:13px; color:#6b7280; margin:0;">
                    Réponses aux problèmes courants
                </p>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">

                @foreach([
                    [
                        'q' => 'Mon token ne fonctionne pas',
                        'r' => 'Vérifiez que le dispositif est actif (statut = Actif) et que vous êtes dans la plage horaire configurée dans Paramètres. Copiez le token depuis la colonne "Token" en cliquant sur l\'icône de copie — pas depuis la version tronquée affichée.',
                    ],
                    [
                        'q' => 'Les alertes ne se déclenchent pas',
                        'r' => 'Vérifiez qu\'un seuil est configuré et actif dans Paramètres → Seuils. Le système nécessite au minimum 5 votes sur la période pour déclencher une alerte. Une alerte par site est limitée à une par heure pour éviter le spam.',
                    ],
                    [
                        'q' => 'Je ne reçois pas les emails',
                        'r' => 'Vérifiez la configuration SMTP dans le fichier .env (MAIL_MAILER=smtp, MAIL_HOST, MAIL_PASSWORD). Pour Gmail, un mot de passe d\'application de 16 caractères est obligatoire — le mot de passe normal ne fonctionne pas. Vérifiez aussi les spams.',
                    ],
                    [
                        'q' => 'Comment désactiver un compte',
                        'r' => 'Dans Utilisateurs, modifiez le compte et basculez le statut sur "Inactif". La désactivation est en cascade : désactiver un Super admin désactive automatiquement tous ses admins. La réactivation fonctionne de la même façon.',
                    ],
                    [
                        'q' => 'La carte interactive ne s\'affiche pas',
                        'r' => 'Vérifiez que les sites ont des coordonnées GPS (latitude/longitude) renseignées. La carte utilise Leaflet et nécessite une connexion internet pour charger les tuiles OpenStreetMap.',
                    ],
                    [
                        'q' => 'Le dispositif accepte les votes hors horaires',
                        'r' => 'Vérifiez que le timezone est bien configuré dans config/app.php (Africa/Niamey). Lancez "php artisan config:clear" après toute modification du .env ou config.',
                    ],
                    [
                        'q' => 'Comment régénérer un token compromis',
                        'r' => 'Dans la liste des Dispositifs, cliquez sur le bouton "Régénérer token" sur la ligne concernée. L\'ancien token est immédiatement invalidé. Le nouveau token s\'affiche dans une notification — copiez-le et reconfigurez le dispositif IoT.',
                    ],
                ] as $index => $item)
                <div style="background:white; border:1px solid #e5e7eb; border-radius:10px;
                            overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                    <button onclick="toggleFaq({{ $index }})"
                        style="width:100%; text-align:left; padding:14px 16px;
                               background:white; border:none; cursor:pointer;
                               display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:14px; font-weight:600; color:#111827;">
                            {{ $item['q'] }}
                        </span>
                        <span id="faq-icon-{{ $index }}"
                              style="font-size:18px; color:#9ca3af; transition:transform 0.2s;
                                     display:inline-block;">
                            ›
                        </span>
                    </button>
                    <div id="faq-answer-{{ $index }}"
                         style="display:none; padding:0 16px 14px;
                                border-top:1px solid #f3f4f6;">
                        <p style="font-size:13px; color:#6b7280; margin:12px 0 0; line-height:1.6;">
                            {{ $item['r'] }}
                        </p>
                    </div>
                </div>
                @endforeach

            </div>

        </div>

    </div>

</div>

{{-- ===================================================
     Scripts navigation et FAQ
=================================================== --}}
<script>
// -----------------------------------------------
// Navigation entre les sections
// -----------------------------------------------
function afficherSection(id) {
    // Masquer toutes les sections
    document.querySelectorAll('.aide-section').forEach(function(s) {
        s.style.display = 'none';
    });

    // Afficher la section sélectionnée
    const section = document.getElementById('s-' + id);
    if (section) section.style.display = 'block';

    // Mettre à jour le style des boutons de navigation
    ['demarrage','roles','dispositifs','alertes','rapports','faq'].forEach(function(navId) {
        const btn = document.getElementById('nav-' + navId);
        if (!btn) return;

        const actif = navId === id;
        btn.style.background  = actif ? '#eff6ff' : 'transparent';
        btn.style.color       = actif ? '#1d4ed8'  : '#6b7280';
        btn.style.fontWeight  = actif ? '600'      : '400';
    });
}

// -----------------------------------------------
// Accordéon FAQ — ouvrir/fermer une question
// -----------------------------------------------
function toggleFaq(index) {
    const answer = document.getElementById('faq-answer-' + index);
    const icon   = document.getElementById('faq-icon-' + index);
    if (!answer || !icon) return;

    const estOuvert = answer.style.display === 'block';

    // Fermer toutes les autres questions
    document.querySelectorAll('[id^="faq-answer-"]').forEach(function(a) {
        a.style.display = 'none';
    });
    document.querySelectorAll('[id^="faq-icon-"]').forEach(function(i) {
        i.style.transform = '';
        i.textContent     = '›';
    });

    // Ouvrir ou fermer la question cliquée
    if (!estOuvert) {
        answer.style.display  = 'block';
        icon.style.transform  = 'rotate(90deg)';
    }
}
</script>

</x-filament-panels::page>