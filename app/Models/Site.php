<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'ville_id',
        'nom',
        'latitude',
        'longitude',
        'statut',
    ];

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function dispositifs()
    {
        return $this->hasMany(Dispositif::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
