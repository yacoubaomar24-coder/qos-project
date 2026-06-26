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

    // POST /api/v1/votes — Créer un vote
    public function store(Request $request): JsonResponse {

        // Valider la requête
        $validated = $request->validate([
            'adresse_mac' => 'required|string',
            'niveau'      => 'required|in:satisfait,moyen,insatisfait',
        ]);

        // Identifier le dispositif via son adresse MAC
        $dispositif = Dispositif::where('adresse_mac', $validated['adresse_mac'])
            ->where('statut', true)
            ->first();

        if (!$dispositif) {
            return response()->json([
                'success' => false,
                'message' => 'Dispositif non reconnu ou inactif.',
            ], 404);
        }

        // ✅ Vérifier si le dispositif est dans sa plage horaire d'activité
        if (!\App\Filament\Pages\Parametres::dispositifEstActif($dispositif->created_by)) {
            return response()->json([
                'success' => false,
                'message' => 'Le dispositif est en dehors de sa plage horaire d\'activité.',
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
}
