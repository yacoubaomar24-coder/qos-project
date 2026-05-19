<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'site_id',
        'dispositif_id',
        'niveau',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function dispositif()
    {
        return $this->belongsTo(Dispositif::class);
    }
}
