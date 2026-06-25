<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapportAuto extends Model
{
    protected $table = 'rapports_auto';
    protected $fillable = [
        'frequence', 'site_ids', 'email_destination',
        'actif', 'created_by', 'derniere_execution',
    ];

    protected function casts(): array
    {
        return [
            'site_ids' => 'array',
            'actif' => 'boolean',
            'derniere_execution' => 'datetime',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}
