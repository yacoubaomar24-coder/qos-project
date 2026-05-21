<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;

#[Fillable(['created_by', 'pays_id', 'region_id', 'site_id','nom', 'prenom', 'numero', 'email', 'role', 'statut', 'password'])]
#[Hidden(['password', 'remember_token'])]

class Utilisateur extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->nom ?? '';
    }

    public function getNameAttribute()
    {
        return $this->nom;
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->satut) {
            return false;
        }
        return $this->hasAnyRole([
            'Admin',
            'Super admin',
            'Admin régional',
            'Admin de site',
        ]);
    }

    public function createdBy()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }
}