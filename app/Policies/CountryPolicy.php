<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Country;
use Illuminate\Auth\Access\HandlesAuthorization;

class CountryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_countries') || $user->hasRole('super_admin');
    }

    public function view(User $user, Country $country): bool
    {
        return $user->can('view_countries') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_countries') || $user->hasRole('super_admin');
    }

    public function update(User $user, Country $country): bool
    {
        return $user->can('edit_countries') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Country $country): bool
    {
        return $user->can('delete_countries') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_countries') || $user->hasRole('super_admin');
    }
}
