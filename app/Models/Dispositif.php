<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispositif extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'nom',
        'adresse_mac',
        'statut',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
