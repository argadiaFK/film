<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Series;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeriesPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_series') || $user->hasRole('super_admin');
    }

    public function view(User $user, Series $series): bool
    {
        return $user->can('view_series') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_series') || $user->hasRole('super_admin');
    }

    public function update(User $user, Series $series): bool
    {
        return $user->can('edit_series') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Series $series): bool
    {
        return $user->can('delete_series') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_series') || $user->hasRole('super_admin');
    }
}
