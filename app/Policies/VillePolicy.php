<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Ville;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_VilleResource');
    }

    public function view(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('view_VilleResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_VilleResource');
    }

    public function update(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('update_VilleResource');
    }

    public function delete(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('delete_VilleResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_VilleResource');
    }

    public function restore(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('restore_VilleResource');
    }

    public function forceDelete(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('force_delete_VilleResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_VilleResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_VilleResource');
    }

    public function replicate(Utilisateur $user, Ville $ville): bool
    {
        return $user->can('replicate_VilleResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_VilleResource');
    }
}