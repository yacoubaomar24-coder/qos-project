<?php
// app/Exports/SatisfactionExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SatisfactionExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private array  $donnees,
        private string $debut,
        private string $fin,
    ) {}

    // Titre de la feuille Excel
    public function title(): string
    {
        return 'Rapport Satisfaction';
    }

    // En-têtes des colonnes
    public function headings(): array
    {
        return [
            'Site',
            'Ville',
            'Région',
            'Pays',
            'Total votes',
            'Satisfaits',
            'Moyens',
            'Insatisfaits',
            'Taux satisfaction (%)',
            'Taux moyen (%)',
            'Taux insatisfaction (%)',
        ];
    }

    // Données
    public function array(): array
    {
        return array_map(fn($d) => [
            $d['site'],
            $d['ville'],
            $d['region'],
            $d['pays'],
            $d['total'],
            $d['satisfaits'],
            $d['moyens'],
            $d['insatisfaits'],
            $d['taux_satisfaction'],
            $d['taux_moyen'],
            $d['taux_insatisfait'],
        ], $this->donnees);
    }

    // Styles Excel
    public function styles(Worksheet $sheet): array
    {
        return [
            // En-tête en gras avec fond gris
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '374151'],
                ],
            ],
        ];
    }
}