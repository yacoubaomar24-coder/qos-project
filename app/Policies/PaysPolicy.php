<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Pays;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaysPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_PaysResource');
    }

    public function view(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('view_PaysResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_PaysResource');
    }

    public function update(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('update_PaysResource');
    }

    public function delete(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('delete_PaysResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_PaysResource');
    }

    public function restore(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('restore_PaysResource');
    }

    public function forceDelete(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('force_delete_PaysResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_PaysResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_PaysResource');
    }

    public function replicate(Utilisateur $user, Pays $pays): bool
    {
        return $user->can('replicate_PaysResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_PaysResource');
    }

}