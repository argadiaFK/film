<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Auth\Access\HandlesAuthorization;

class GenrePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_genres') || $user->hasRole('super_admin');
    }

    public function view(User $user, Genre $genre): bool
    {
        return $user->can('view_genres') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('create_genres') || $user->hasRole('super_admin');
    }

    public function update(User $user, Genre $genre): bool
    {
        return $user->can('edit_genres') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Genre $genre): bool
    {
        return $user->can('delete_genres') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_genres') || $user->hasRole('super_admin');
    }
}
