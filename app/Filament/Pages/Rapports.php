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
    public string  $exportFormat = 'pdf';    // pdf, excel, csv
    public string  $exportPeriode = 'week';   // day, week, month, year, custom
    public string  $exportDateDebut = '';
    public string  $exportDateFin = '';
    public array $exportSiteIds = [];       // sites sélectionnés
    public array $sitesOptions = [];       // liste des sites disponibles

    // -----------------------------------------------
    // Propriétés pour les rapports automatiques
    // -----------------------------------------------
    public string $rapportFrequence = 'hebdomadaire'; // quotidien, hebdomadaire, mensuel
    public string $rapportEmail = '';
    public array $rapportSiteIds = [];
    public bool $rapportActif = false;
    public array $rapportsAuto = [];  // liste des rapports configurés

    public function mount(): void
    {
        // Charger les sites accessibles selon le rôle
        $this->sitesOptions = $this->getSitesOptions();

        // Sélectionner tous les sites par défaut
        $this->exportSiteIds = array_keys($this->sitesOptions);

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
    private function getSitesOptions(): array
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return [];

        $query = Site::query()->where('statut', true);

        if ($user->hasRole('Super admin')) {
            $adminIds = Utilisateur::where('created_by', $user->id)
                ->where('role', 'Admin national')->pluck('id')->toArray();
            $query->whereIn('created_by', array_merge([$user->id], $adminIds));
        } elseif ($user->hasRole('Admin national')) {
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');
            $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)->pluck('id');
            $query->whereIn('ville_id', $villeIds);
        } elseif ($user->hasRole('Admin de site')) {
            $query->where('id', $user->site_id);
        }

        return $query->pluck('nom', 'id')->toArray();
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
        $siteIds = !empty($this->exportSiteIds)
            ? $this->exportSiteIds
            : array_keys($this->sitesOptions);

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

        \App\Models\RapportAuto::create([
            'frequence'         => $this->rapportFrequence,
            'site_ids'          => !empty($this->rapportSiteIds) ? $this->rapportSiteIds : null,
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
