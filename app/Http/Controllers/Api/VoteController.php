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

        // Enregistrer le vote
        $vote = Vote::create([
            'dispositif_id' => $dispositif->id,
            'site_id'       => $dispositif->site_id,
            'niveau'        => $validated['niveau'],
            'created_by'    => $dispositif->created_by,
        ]);

        // Mettre à jour le statut du dispositif
        $dispositif->update([
            'derniere_connexion' => now(),
            'en_ligne'           => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vote enregistré avec succès.',
            'data'    => [
                'vote_id'   => $vote->id,
                'site'      => $dispositif->site->nom,
                'niveau'    => $vote->niveau,
                'timestamp' => $vote->created_at,
            ],
        ], 201);
    }
}
