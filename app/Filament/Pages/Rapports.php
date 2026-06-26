<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Models\Vote;
use App\Models\Utilisateur;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Rapports extends Page
{
    protected static ?string $navigationLabel = 'Export & Rapports';
    protected static ?string $title = 'Export & Rapports';
    protected static ?int $navigationSort  = 5;
    protected string $view = 'filament.pages.rapports';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-arrow-down';
    }

    // Masquer aux Admins
    public static function shouldRegisterNavigation(): bool
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return false;
        return !$user->hasRole('Admin');
    }

    // -----------------------------------------------
    // Propriétés du formulaire d'export
    // -----------------------------------------------
    public string $exportFormat = 'pdf';    // pdf, excel, csv
    public string $exportPeriode = 'week';   // day, week, month, year, custom
    public string $exportDateDebut = '';
    public string $exportDateFin = '';
    public array $rapportSiteIds = [];

    // -----------------------------------------------
    // Filtres hiérarchiques — partagés entre manuel et auto
    // -----------------------------------------------
    public string $filtreNiveau  = 'tous';    // tous, pays, region, ville, site
    public ?int   $filtrePaysId  = null;
    public ?int   $filtreRegionId = null;
    public ?int   $filtreVilleId  = null;
    public ?int   $filtreSiteId   = null;

    // Listes pour les selects
    // -----------------------------------------------
    public array $sitesOptions   = [];
    public array $paysOptions    = [];
    public array $regionsOptions = [];
    public array $villesOptions  = [];

    // -----------------------------------------------
    // Propriétés pour les rapports automatiques
    // -----------------------------------------------
    public string $rapportFrequence = 'hebdomadaire'; // quotidien, hebdomadaire, mensuel
    public string $rapportEmail = '';
    public string $rapportFiltreNiveau = 'tous';
    public ?int   $rapportFiltrePaysId  = null;
    public ?int   $rapportFiltreRegionId = null;
    public ?int   $rapportFiltreVilleId  = null;
    public ?int   $rapportFiltreSiteId   = null;
    public array $rapportsAuto = [];  // liste des rapports configurés

    public function mount(): void
    {
        // Charger toutes les options selon le rôle
        $this->chargerOptions();

        // Date par défaut — dernière semaine
        $this->exportDateDebut = now()->subWeek()->format('Y-m-d');
        $this->exportDateFin   = now()->format('Y-m-d');

        // Email par défaut — email de l'utilisateur connecté
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        $this->rapportEmail = $user?->email ?? '';

        // Charger les rapports automatiques configurés
        $this->loadRapportsAuto();
    }

    // -----------------------------------------------
    // Sites accessibles selon le rôle connecté
    // -----------------------------------------------
    private function chargerOptions(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return;

        if ($user->hasRole('Super admin')) {
            // Super admin — ses pays via ses régions
            $adminIds  = Utilisateur::where('created_by', $user->id)
                ->where('role', 'Admin national')->pluck('id')->toArray();
            $creatorIds = array_merge([$user->id], $adminIds);

            $regionIds = \App\Models\Region::whereIn('created_by', $creatorIds)->pluck('id');
            $paysIds = \App\Models\Region::whereIn('id', $regionIds)->pluck('pays_id')->unique();

            $this->paysOptions = \App\Models\Pays::whereIn('id', $paysIds)->pluck('nom', 'id')->toArray();
            $this->regionsOptions = \App\Models\Region::whereIn('created_by', $creatorIds)->pluck('nom', 'id')->toArray();
            $this->villesOptions  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('nom', 'id')->toArray();
            $this->sitesOptions   = Site::whereIn('created_by', $creatorIds)->pluck('nom', 'id')->toArray();

        } elseif ($user->hasRole('Admin national')) {
            // Admin national — son pays uniquement
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');

            $this->paysOptions    = \App\Models\Pays::where('id', $user->pays_id)->pluck('nom', 'id')->toArray();
            $this->regionsOptions = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('nom', 'id')->toArray();
            $this->villesOptions  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('nom', 'id')->toArray();
            $this->sitesOptions   = Site::whereIn('ville_id', $villeIds)->pluck('nom', 'id')->toArray();

        } elseif ($user->hasRole('Admin régional')) {
            // Admin régional — sa région uniquement
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');

            $this->regionsOptions = \App\Models\Region::where('id', $user->region_id)->pluck('nom', 'id')->toArray();
            $this->villesOptions  = \App\Models\Ville::where('region_id', $user->region_id)->pluck('nom', 'id')->toArray();
            $this->sitesOptions   = Site::whereIn('ville_id', $villeIds)->pluck('nom', 'id')->toArray();

        } elseif ($user->hasRole('Admin de site')) {
            // Admin de site — son seul site
            $this->sitesOptions = Site::where('id', $user->site_id)->pluck('nom', 'id')->toArray();
        }
    }

    // Récupérer les IDs des sites selon les filtres
    // -----------------------------------------------
    private function getSiteIdsFiltres(
        string $niveau,
        ?int   $paysId,
        ?int   $regionId,
        ?int   $villeId,
        ?int   $siteId
    ): array {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        // Base — tous les sites accessibles
        $query = Site::query();

        if ($user->hasRole('Super admin')) {
            $adminIds   = Utilisateur::where('created_by', $user->id)
                ->where('role', 'Admin national')->pluck('id')->toArray();
            $creatorIds = array_merge([$user->id], $adminIds);
            $query->whereIn('created_by', $creatorIds);
        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            return [$user->site_id];
        }

        // Appliquer le filtre hiérarchique sélectionné
        match ($niveau) {
            // Filtrer par pays → régions → villes → sites
            'pays' => $query->when($paysId, fn($q) =>
                $q->whereHas('ville.region', fn($q2) =>
                    $q2->where('pays_id', $paysId)
                )
            ),

            // Filtrer par région → villes → sites
            'region' => $query->when($regionId, fn($q) =>
                $q->whereHas('ville', fn($q2) =>
                    $q2->where('region_id', $regionId)
                )
            ),
            // Filtrer par ville → sites
            'ville' => $query->when($villeId, fn($q) =>
                $q->where('ville_id', $villeId)
            ),
            // Filtrer par site spécifique
            'site' => $query->when($siteId, fn($q) =>
                $q->where('id', $siteId)
            ),
            // Tous les sites accessibles — pas de filtre supplémentaire
            default => null,
        };

        return $query->pluck('id')->toArray();
    }

    // -----------------------------------------------
    // Calculer les dates selon la période sélectionnée
    // -----------------------------------------------
    private function getDates(): array
    {
        return match ($this->exportPeriode) {
            'day'    => [now()->startOfDay(),   now()->endOfDay()],
            'week'   => [now()->startOfWeek(),  now()->endOfWeek()],
            'month'  => [now()->startOfMonth(), now()->endOfMonth()],
            'year'   => [now()->startOfYear(),  now()->endOfYear()],
            'custom' => [
                \Carbon\Carbon::parse($this->exportDateDebut)->startOfDay(),
                \Carbon\Carbon::parse($this->exportDateFin)->endOfDay(),
            ],
            default  => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }

    // -----------------------------------------------
    // Collecter les données pour le rapport
    // -----------------------------------------------
    private function collecterDonnees(): array
    {
        [$debut, $fin] = $this->getDates();
        
        // Si siteIds non fournis — utiliser les filtres courants
        $siteIds = $siteIds ?? $this->getSiteIdsFiltres(
            $this->filtreNiveau,
            $this->filtrePaysId,
            $this->filtreRegionId,
            $this->filtreVilleId,
            $this->filtreSiteId,
        );

        $donnees = [];

        foreach ($siteIds as $siteId) {
            $site = Site::with(['ville.region.pays'])->find($siteId);
            if (!$site) continue;

            // Votes sur la période
            $query = Vote::where('site_id', $siteId)->whereBetween('created_at', [$debut, $fin]);
            $total = (clone $query)->count();
            $satisfaits = (clone $query)->where('niveau', 'satisfait')->count();
            $moyens = (clone $query)->where('niveau', 'moyen')->count();
            $insatisfaits = (clone $query)->where('niveau', 'insatisfait')->count();

            $donnees[] = [
                'site'             => $site->nom,
                'ville'            => $site->ville?->nom ?? 'N/A',
                'region'           => $site->ville?->region?->nom ?? 'N/A',
                'pays'             => $site->ville?->region?->pays?->nom ?? 'N/A',
                'total'            => $total,
                'satisfaits'       => $satisfaits,
                'moyens'           => $moyens,
                'insatisfaits'     => $insatisfaits,
                'taux_satisfaction' => $total > 0 ? round(($satisfaits / $total) * 100, 1) : 0,
                'taux_moyen' => $total > 0 ? round(($moyens / $total) * 100, 1) : 0,
                'taux_insatisfait'  => $total > 0 ? round(($insatisfaits / $total) * 100, 1) : 0,
            ];
        }

        // Trier par taux de satisfaction décroissant
        usort($donnees, fn($a, $b) => $b['taux_satisfaction'] <=> $a['taux_satisfaction']);

        return $donnees;
    }

    // -----------------------------------------------
    // Changer le niveau de filtre
    // -----------------------------------------------
    public function changerFiltreNiveau(string $niveau): void
    {
        $this->filtreNiveau = $niveau;
        $this->filtrePaysId = null;
        $this->filtreRegionId = null;
        $this->filtreVilleId = null;
        $this->filtreSiteId = null;
    }

    public function changerRapportFiltreNiveau(string $niveau): void
    {
        $this->rapportFiltreNiveau   = $niveau;
        $this->rapportFiltrePaysId   = null;
        $this->rapportFiltreRegionId = null;
        $this->rapportFiltreVilleId  = null;
        $this->rapportFiltreSiteId   = null;
    }

    // -----------------------------------------------
    // Export PDF
    // -----------------------------------------------
    public function exporterPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$debut, $fin] = $this->getDates();
        $donnees = $this->collecterDonnees();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rapports.pdf', [
            'donnees' => $donnees,
            'debut'   => $debut->format('d/m/Y'),
            'fin'     => $fin->format('d/m/Y'),
            'titre'   => 'Rapport de Satisfaction Client',
            'genere'  => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'rapport-satisfaction-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    // -----------------------------------------------
    // Export Excel
    // -----------------------------------------------
    public function exporterExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        [$debut, $fin] = $this->getDates();
        $donnees       = $this->collecterDonnees();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SatisfactionExport($donnees, $debut, $fin),
            'rapport-satisfaction-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // -----------------------------------------------
    // Export CSV
    // -----------------------------------------------
    public function exporterCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$debut, $fin] = $this->getDates();
        $donnees       = $this->collecterDonnees();

        $filename = 'rapport-satisfaction-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($donnees) {
            $handle = fopen('php://output', 'w');

            // En-tête BOM pour Excel (caractères spéciaux)
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Colonnes
            fputcsv($handle, [
                'Site', 'Ville', 'Région', 'Pays',
                'Total votes', 'Satisfaits', 'Moyens', 'Insatisfaits',
                'Taux satisfaction (%)', 'Taux insatisfaction (%)',
            ], ';');

            // Données
            foreach ($donnees as $ligne) {
                fputcsv($handle, [
                    $ligne['site'],
                    $ligne['ville'],
                    $ligne['region'],
                    $ligne['pays'],
                    $ligne['total'],
                    $ligne['satisfaits'],
                    $ligne['moyens'],
                    $ligne['insatisfaits'],
                    $ligne['taux_satisfaction'],
                    $ligne['taux_moyen'],
                    $ligne['taux_insatisfait'],
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // -----------------------------------------------
    // Changer la période
    // -----------------------------------------------
    public function changerPeriode(string $periode): void
    {
        $this->exportPeriode = $periode;
    }

    // -----------------------------------------------
    // Aperçu des données avant export
    // -----------------------------------------------
    public function getApercu(): array
    {
        return $this->collecterDonnees();
    }

    // -----------------------------------------------
    // Charger les rapports automatiques
    // -----------------------------------------------
    public function loadRapportsAuto(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        $this->rapportsAuto = \App\Models\RapportAuto::where('created_by', $user?->id)
            ->latest()
            ->get()
            ->toArray();
    }

    // -----------------------------------------------
    // Sauvegarder un rapport automatique
    // -----------------------------------------------
    public function sauvegarderRapportAuto(): void
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();

        $siteIds = $this->getSiteIdsFiltres(
            $this->rapportFiltreNiveau,
            $this->rapportFiltrePaysId,
            $this->rapportFiltreRegionId,
            $this->rapportFiltreVilleId,
            $this->rapportFiltreSiteId,
        );

        \App\Models\RapportAuto::create([
            'frequence'         => $this->rapportFrequence,
            'site_ids'          => !empty($siteIds) ? $siteIds : null,
            'email_destination' => $this->rapportEmail,
            'created_by'        => $user?->id,
            'actif'             => true,
        ]);

        $this->loadRapportsAuto();
    }

    // -----------------------------------------------
    // Supprimer un rapport automatique
    // -----------------------------------------------
    public function supprimerRapportAuto(int $id): void
    {
        \App\Models\RapportAuto::find($id)?->delete();
        $this->loadRapportsAuto();
    }

    // -----------------------------------------------
    // Tester un rapport immédiatement
    // -----------------------------------------------
    public function testerRapport(int $id): void
    {
        $rapport = \App\Models\RapportAuto::find($id);
        if (!$rapport) return;

        \App\Jobs\EnvoyerRapportsAutoJob::dispatchSync($rapport->frequence);
    }

}
