<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'pays_id',
        'nom',
        'statut',
    ];

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function villes()
    {
        return $this->hasMany(Ville::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
