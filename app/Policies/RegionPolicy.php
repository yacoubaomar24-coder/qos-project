<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Region;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_RegionResource');
    }

    public function view(Utilisateur $user, Region $region): bool
    {
        return $user->can('view_RegionResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_RegionResource');
    }

    public function update(Utilisateur $user, Region $region): bool
    {
        return $user->can('update_RegionResource');
    }

    public function delete(Utilisateur $user, Region $region): bool
    {
        return $user->can('delete_RegionResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_RegionResource');
    }

    public function restore(Utilisateur $user, Region $region): bool
    {
        return $user->can('restore_RegionResource');
    }

    public function forceDelete(Utilisateur $user, Region $region): bool
    {
        return $user->can('force_delete_RegionResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_RegionResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_RegionResource');
    }

    public function replicate(Utilisateur $user, Region $region): bool
    {
        return $user->can('replicate_RegionResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_RegionResource');
    }
}