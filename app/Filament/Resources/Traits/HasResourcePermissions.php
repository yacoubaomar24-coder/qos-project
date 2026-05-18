<?php
// app/Filament/Traits/HasResourcePermissions.php

namespace App\Filament\Resources\Traits;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

// Ce trait peut être utilisé dans tous les Resource en une seule ligne pour gérer les permissions 
// de l'utilisateur connecté, ça permet d'éviter de dupliquer le même code dans chaque Resource.
trait HasResourcePermissions
{
    protected static function getPermissionPrefix(): string
    {
        // Extrait automatiquement le nom : SiteResource → Site
        return class_basename(static::class);
    }

    protected static function getAuthUser(): ?Utilisateur
    {
        // Essaie les deux méthodes
        $user = filament()->auth()->user() ?? Auth::guard('web')->user();

        return $user instanceof Utilisateur ? $user : null;
    }

    public static function canViewAny(): bool
    {
        $user = static::getAuthUser();
        if (!$user) return false;
        return $user->can('view_any_' . static::getPermissionPrefix());
    }

    public static function canCreate(): bool
    {
        $user = static::getAuthUser();
        if (!$user) return false;
        return $user->can('create_' . static::getPermissionPrefix());
    }

    public static function canEdit(Model $record): bool
    {
        $user = static::getAuthUser();
        if (!$user) return false;
        return $user->can('update_' . static::getPermissionPrefix());
    }

    public static function canDelete(Model $record): bool
    {
        $user = static::getAuthUser();
        if (!$user) return false;
        return $user->can('delete_' . static::getPermissionPrefix());
    }
}