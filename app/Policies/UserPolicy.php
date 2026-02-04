<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_users') || $user->hasRole('super_admin');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_users') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_users') || $user->hasRole('super_admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('edit_users') || $user->hasRole('super_admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('delete_users') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_users') || $user->hasRole('super_admin');
    }
}
