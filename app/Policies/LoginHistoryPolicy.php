<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoginHistoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, LoginHistory $log): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return false; // System only
    }

    public function update(User $user, LoginHistory $log): bool
    {
        return false; // Read-only
    }

    public function delete(User $user, LoginHistory $log): bool
    {
        return false; // Read-only
    }
}
