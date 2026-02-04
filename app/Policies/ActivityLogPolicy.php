<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, ActivityLog $log): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return false; // System only
    }

    public function update(User $user, ActivityLog $log): bool
    {
        return false; // Read-only
    }

    public function delete(User $user, ActivityLog $log): bool
    {
        return false; // Read-only
    }
}
