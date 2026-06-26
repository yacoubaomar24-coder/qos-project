<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'libelle_satisfait',
        'libelle_moyen',
        'libelle_insatisfait',
        'couleur_satisfait',
        'couleur_moyen',
        'couleur_insatisfait',
        'heure_debut',
        'heure_fin',
        'jours_actifs',
        'organisation_nom',
        'organisation_logo',
        'couleur_primaire',
        'couleur_secondaire',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jours_actifs' => 'array',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
