<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Vote;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\HandlesAuthorization;

class VotePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(Utilisateur $user): bool
    {
        return $user->can('view_any_VoteResource');
    }

    public function view(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('view_VoteResource');
    }
    
    public function create(Utilisateur $user): bool
    {
        return $user->can('create_VoteResource');
    }

    public function update(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('update_VoteResource');
    }

    public function delete(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('delete_VoteResource');
    }

    public function deleteAny(Utilisateur $user): bool
    {
        return $user->can('delete_any_VoteResource');
    }

    public function restore(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('restore_VoteResource');
    }

    public function forceDelete(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('force_delete_VoteResource');
    }

    public function forceDeleteAny(Utilisateur $user): bool
    {
        return $user->can('force_delete_any_VoteResource');
    }

    public function restoreAny(Utilisateur $user): bool
    {
        return $user->can('restore_any_VoteResource');
    }

    public function replicate(Utilisateur $user, Vote $vote): bool
    {
        return $user->can('replicate_VoteResource');
    }

    public function reorder(Utilisateur $user): bool
    {
        return $user->can('reorder_VoteResource');
    }
}