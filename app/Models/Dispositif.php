<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispositif extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'site_id',
        'nom',
        'adresse_mac',
        'derniere_connexion',  // Permet de savoir si un dispositif est toujours actif ou abandonné.
        'en_ligne',   // 
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'derniere_connexion' => 'datetime',
            'en_ligne'           => 'boolean',
            'statut'             => 'boolean',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
