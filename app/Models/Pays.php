<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pays extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'nom',
        'code',
        'statut',
    ];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
