<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dispositif;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class DispositifPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_DispositifResource');
    }

    public function view(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('view_DispositifResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_DispositifResource');
    }

    public function update(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('update_DispositifResource');
    }

    public function delete(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('delete_DispositifResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_DispositifResource');
    }

    public function restore(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('restore_DispositifResource');
    }

    public function forceDelete(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('force_delete_DispositifResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_DispositifResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_DispositifResource');
    }

    public function replicate(Utilisateur $user, Dispositif $dispositif): bool
    {
        return $user->can('replicate_DispositifResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_DispositifResource');
    }

}