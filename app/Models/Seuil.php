<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seuil extends Model
{
    protected $fillable = [
        'site_id', 'seuil_insatisfaction', 'periode_heures',
        'notif_email', 'notif_sms', 'email_destination',
        'telephone_destination', 'created_by', 'actif',
    ];

    protected function casts(): array
    {
        return [
            'notif_email' => 'boolean',
            'notif_sms'   => 'boolean',
            'actif'       => 'boolean',
        ];
    }

    // Relation vers le site (null = seuil global)
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }
}
