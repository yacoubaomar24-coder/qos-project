<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Utilisat;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilisatPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Utilisat');
    }

    public function view(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('View:Utilisat');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Utilisat');
    }

    public function update(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('Update:Utilisat');
    }

    public function delete(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('Delete:Utilisat');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Utilisat');
    }

    public function restore(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('Restore:Utilisat');
    }

    public function forceDelete(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('ForceDelete:Utilisat');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Utilisat');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Utilisat');
    }

    public function replicate(AuthUser $authUser, Utilisat $utilisat): bool
    {
        return $authUser->can('Replicate:Utilisat');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Utilisat');
    }

}