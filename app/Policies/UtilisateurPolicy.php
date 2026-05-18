<?php

namespace App\Policies;

use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilisateurPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('view_any_UtilisateurResource');
    }

    public function view(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('view_UtilisateurResource');
    }

    public function create(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('create_UtilisateurResource');
    }

    public function update(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('update_UtilisateurResource');
    }

    public function delete(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('delete_UtilisateurResource');
    }

    public function deleteAny(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('delete_any_UtilisateurResource');
    }

    public function restore(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('restore_UtilisateurResource');
    }

    public function forceDelete(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('force_delete_UtilisateurResource');
    }

    public function forceDeleteAny(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('force_delete_any_UtilisateurResource');
    }

    public function restoreAny(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('restore_any_UtilisateurResource');
    }

    public function replicate(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('replicate_UtilisateurResource');
    }

    public function reorder(Utilisateur $utilisateur): bool
    {
        return $utilisateur->can('reorder_UtilisateurResource');
    }
}