<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispositif;
use App\Models\Vote;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function store(Request $request): JsonResponse {

        // 1. Valider la requête
        $validated = $request->validate([
            'adresse_mac' => 'required|string',
            'niveau'      => 'required|in:satisfait,neutre,insatisfait',
        ]);

        // 2. Identifier le dispositif via son adresse MAC
        $dispositif = Dispositif::where('adresse_mac', $validated['adresse_mac'])
            ->where('statut', true)
            ->first();

        if (!$dispositif) {
            return response()->json([
                'success' => false,
                'message' => 'Dispositif non reconnu ou inactif.',
            ], 404);
        }

        // 3. Enregistrer le vote
        $vote = Vote::create([
            'dispositif_id' => $dispositif->id,
            'site_id'       => $dispositif->site_id,
            'niveau'        => $validated['niveau'],
            'created_by'    => $dispositif->created_by,
        ]);

        // 4. Mettre à jour le statut du dispositif
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

    // Endpoint pour vérifier si le dispositif est enregistré
    public function check(Request $request): JsonResponse {

        $request->validate([
            'adresse_mac' => 'required|string',
        ]);

        $dispositif = Dispositif::where('adresse_mac', $request->adresse_mac)->first();

        if (!$dispositif) {
            return response()->json([
                'success'  => false,
                'message'  => 'Dispositif non enregistré.',
            ], 404);
        }

        $dispositif->update([
            'derniere_connexion' => now(),
            'en_ligne'           => true,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'dispositif_id' => $dispositif->id,
                'site'          => $dispositif->site->nom,
                'statut'        => $dispositif->statut,
            ],
        ]);
    }
}
