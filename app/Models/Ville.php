<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ville extends Model
{
    use HasFactory;
    protected $fillable = [
        'created_by',
        'region_id',
        'nom',
        'statut',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
