<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Site;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_SiteResource');
    }

    public function view(Utilisateur $user, Site $site): bool
    {
        return $user->can('view_SiteResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_SiteResource');
    }

    public function update(Utilisateur $user, Site $site): bool
    {
        return $user->can('update_SiteResource');
    }

    public function delete(Utilisateur $user, Site $site): bool
    {
        return $user->can('delete_SiteResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_SiteResource');
    }

    public function restore(Utilisateur $user, Site $site): bool
    {
        return $user->can('restore_SiteResource');
    }

    public function forceDelete(Utilisateur $user, Site $site): bool
    {
        return $user->can('force_delete_SiteResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_SiteResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_SiteResource');
    }

    public function replicate(Utilisateur $user, Site $site): bool
    {
        return $user->can('replicate_SiteResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_SiteResource');
    }

}