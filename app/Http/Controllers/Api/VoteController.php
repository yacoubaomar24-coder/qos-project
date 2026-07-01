<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispositif;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{

    // GET /api/v1/votes — Liste tous les votes
    public function index(): JsonResponse
    {
        $votes = Vote::with(['dispositif', 'site'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $votes,
        ]);
    }

    // -----------------------------------------------
    // POST /api/v1/votes
    // Header: Authorization: Bearer {token}
    // Body: { "niveau": "satisfait" }
    // -----------------------------------------------
    public function store(Request $request): JsonResponse {

        // ✅ 1. Extraire le token depuis le header Authorization
        $token = $this->extraireToken($request);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant. Ajoutez Authorization: Bearer {token} dans les headers.',
            ], 401);
        }

        // ✅ 2. Identifier le dispositif via son token
        $dispositif = Dispositif::where('token', $token)
            ->where('statut', true)
            ->first();
        
        if (!$dispositif) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide ou dispositif inactif.',
            ], 401);
        }

        // 3. Valider la requête (le niveau de satisfaction)
        $validated = $request->validate([
            'niveau' => 'required|in:satisfait,neutre,insatisfait',
        ]);

        // Vérifier si le dispositif est dans sa plage horaire d'activité
        if (!\App\Filament\Pages\Parametres::dispositifEstActif($dispositif->created_by)) {


            return response()->json([
                'success' => false,
                'message' => 'Le dispositif est en dehors de sa plage horaire d\'activité. ' .
                             'Votes acceptés entre ' .
                             substr(\App\Models\Configuration::where('created_by', $dispositif->created_by)
                                ->value('heure_debut'), 0, 5) .
                             ' et ' .
                             substr(\App\Models\Configuration::where('created_by', $dispositif->created_by)
                                ->value('heure_fin'), 0, 5) . '.',
            ], 403);
        }

        // Enregistrer le vote
        $vote = Vote::create([
            'dispositif_id' => $dispositif->id,
            'site_id'       => $dispositif->site_id,
            'niveau'        => $validated['niveau'],
            'created_by'    => $dispositif->created_by,
        ]);

        // ✅ Récupérer les libellés configurés
        $config = \App\Models\Configuration::where('created_by', $dispositif->created_by)->first();

        // Mettre à jour le statut du dispositif
        $dispositif->update([
            'derniere_connexion' => now(),
            'en_ligne'           => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vote enregistré avec succès.',
            'data'    => [
                'dispositif_id'       => $dispositif->id,
                'vote_id'   => $vote->id,
                'site'      => $dispositif->site->nom,
                'niveau'    => $vote->niveau,
                'statut'              => $dispositif->statut,
                // ✅ Libellés et couleurs pour l'affichage sur le dispositif
                'libelle_satisfait'   => $config?->libelle_satisfait   ?? 'Satisfait',
                'libelle_moyen'      => $config?->libelle_moyen       ?? 'Moyennement satisfait',
                'libelle_insatisfait' => $config?->libelle_insatisfait  ?? 'Insatisfait',
                'couleur_satisfait'   => $config?->couleur_satisfait    ?? '#22c55e',
                'couleur_moyen'      => $config?->couleur_moyen       ?? '#f59e0b',
                'couleur_insatisfait' => $config?->couleur_insatisfait  ?? '#ef4444',
                // Plage horaire active
                'actif_maintenant'    => \App\Filament\Pages\Parametres::dispositifEstActif($dispositif->created_by),
                'timestamp' => $vote->created_at,
            ],
        ], 201);
    }

    // -----------------------------------------------
    // GET /api/v1/dispositifs/info
    // Header: Authorization: Bearer {token}
    // Vérifie le dispositif et retourne sa configuration
    // -----------------------------------------------
    public function check(Request $request): JsonResponse
    {
        $token = $this->extraireToken($request);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token manquant.',
            ], 401);
        }

        $dispositif = Dispositif::where('token', $token)->first();

        if (!$dispositif) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide.',
            ], 401);
        }

        $config = \App\Models\Configuration::where('created_by', $dispositif->created_by)->first();

        $dispositif->update([
            'derniere_connexion' => now(),
            'en_ligne'           => true,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'dispositif_id'       => $dispositif->id,
                'site'                => $dispositif->site->nom,
                'statut'              => $dispositif->statut,
                // Libellés configurés
                'libelle_satisfait'   => $config?->libelle_satisfait   ?? 'Satisfait',
                'libelle_moyen'      => $config?->libelle_moyen       ?? 'Moyennement satisfait',
                'libelle_insatisfait' => $config?->libelle_insatisfait  ?? 'Insatisfait',
                // Couleurs configurées
                'couleur_satisfait'   => $config?->couleur_satisfait   ?? '#22c55e',
                'couleur_moyen'      => $config?->couleur_moyen       ?? '#f59e0b',
                'couleur_insatisfait' => $config?->couleur_insatisfait  ?? '#ef4444',
                // Plage horaire
                'actif_maintenant'   => \App\Filament\Pages\Parametres::dispositifEstActif($dispositif->created_by),
                'heure_debut'        => $config ? substr($config->heure_debut, 0, 5) : '08:00',
                'heure_fin'          => $config ? substr($config->heure_fin, 0, 5) : '17:00',
            ],
        ]);
    }

    // -----------------------------------------------
    // Extraire le token Bearer depuis le header
    // Authorization: Bearer site5-a3f8b2c1d9e4...
    // -----------------------------------------------
    private function extraireToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7); // supprimer "Bearer "
    }
}
