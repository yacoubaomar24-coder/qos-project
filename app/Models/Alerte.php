<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    protected $fillable = [
        'site_id', 'seuil_id', 'taux_insatisfaction',
        'seuil_configure', 'total_votes', 'statut',
        'email_envoye', 'sms_envoye', 'message',
    ];

    protected function casts(): array
    {
        return [
            'email_envoye' => 'boolean',
            'sms_envoye'   => 'boolean',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function seuil()
    {
        return $this->belongsTo(Seuil::class);
    }
}
