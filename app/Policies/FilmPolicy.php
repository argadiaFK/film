<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Film;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilmPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_films') || $user->hasRole('super_admin');
    }

    public function view(User $user, Film $film): bool
    {
        return $user->can('view_films') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_films') || $user->hasRole('super_admin');
    }

    public function update(User $user, Film $film): bool
    {
        return $user->can('edit_films') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Film $film): bool
    {
        return $user->can('delete_films') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_films') || $user->hasRole('super_admin');
    }
}
