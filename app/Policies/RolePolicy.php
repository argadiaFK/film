<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_roles') || $user->hasRole('super_admin');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('view_roles') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_roles') || $user->hasRole('super_admin');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('edit_roles') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('delete_roles') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_roles') || $user->hasRole('super_admin');
    }
}
