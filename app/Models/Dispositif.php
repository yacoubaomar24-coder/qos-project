<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Dispositif extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'site_id',
        'nom',
        'adresse_mac',
        'token',  
        'token_genere_le',
        'derniere_connexion',  // Permet de savoir si un dispositif est toujours actif ou abandonné.
        'en_ligne',   // 
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'derniere_connexion' => 'datetime',
            'token_genere_le'   => 'datetime',
            'en_ligne'           => 'boolean',
            'statut'             => 'boolean',
        ];
    }

    // -----------------------------------------------
    // Générer un token unique et sécurisé
    // Format : {site_id}-{api_key_aléatoire}
    // Ex : 5-a3f8b2c1d9e4f7g6h0i2j1k8l3m5n6o4
    // -----------------------------------------------
    public static function genererToken(int $siteId): string
    {
        do {
            // Préfixe lisible + 32 caractères aléatoires
            $token = 'site' . $siteId . '-' . Str::random(32);
        } while (static::where('token', $token)->exists()); // garantir l'unicité

        return $token;
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
