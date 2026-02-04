<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage_settings') || $user->hasRole('super_admin');
    }
}
