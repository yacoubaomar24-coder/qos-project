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

    // GET /api/v1/votes/{id} — Détail d'un vote
    public function show(int $id): JsonResponse
    {
        $vote = Vote::with(['dispositif', 'site'])->find($id);

        if (!$vote) {
            return response()->json([
                'success' => false,
                'message' => 'Vote introuvable.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $vote,
        ]);
    }

    // GET /api/v1/votes/stats — Statistiques globales
    // -----------------------------------------------
    public function stats(Request $request): JsonResponse
    {
        $periode = $request->get('periode', 'today');

        $query = Vote::query();

        // Filtre par période
        match ($periode) {
            'today' => $query->whereDate('created_at', today()),
            'week'  => $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]),
            'month' => $query->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year),
            default => $query->whereDate('created_at', today()),
        };

        $total       = (clone $query)->count();
        $satisfaits  = (clone $query)->where('niveau', 'satisfait')->count();
        $neutres     = (clone $query)->where('niveau', 'moyen')->count();
        $insatisfaits = (clone $query)->where('niveau', 'insatisfait')->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'periode'      => $periode,
                'total'        => $total,
                'satisfaits'   => $satisfaits,
                'neutres'      => $neutres,
                'insatisfaits' => $insatisfaits,
                'taux_satisfaction' => $total > 0
                    ? round(($satisfaits / $total) * 100, 1)
                    : 0,
            ],
        ]);
    }

    // GET /api/v1/votes/par-site — Votes groupés par site
    public function parSite(): JsonResponse
    {
        $votes = Vote::with('site')
            ->selectRaw('site_id, niveau, COUNT(*) as total')
            ->groupBy('site_id', 'niveau')
            ->get()
            ->groupBy('site_id')
            ->map(function ($items) {
                $site        = $items->first()->site;
                $total       = $items->sum('total');
                $satisfaits  = $items->where('niveau', 'satisfait')->sum('total');

                return [
                    'site'              => $site?->nom,
                    'total'             => $total,
                    'satisfaits'        => $satisfaits,
                    'neutres'           => $items->where('niveau', 'moyen')->sum('total'),
                    'insatisfaits'      => $items->where('niveau', 'insatisfait')->sum('total'),
                    'taux_satisfaction' => $total > 0
                        ? round(($satisfaits / $total) * 100, 1)
                        : 0,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $votes,
        ]);
    }
}
