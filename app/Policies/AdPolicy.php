<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ad;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_ads') || $user->hasRole('super_admin');
    }

    public function view(User $user, Ad $ad): bool
    {
        return $user->can('view_ads') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_ads') || $user->hasRole('super_admin');
    }

    public function update(User $user, Ad $ad): bool
    {
        return $user->can('edit_ads') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Ad $ad): bool
    {
        return $user->can('delete_ads') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_ads') || $user->hasRole('super_admin');
    }
}
