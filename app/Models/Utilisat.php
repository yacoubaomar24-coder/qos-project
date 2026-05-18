<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisat extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'nom',
        'prenom',
        'numero',
        'email',
        'password',
        'role',
        'statut',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
