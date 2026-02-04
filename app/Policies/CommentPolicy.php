<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_comments') || $user->hasRole('super_admin');
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->can('view_comments') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return false; // Comments created by users on frontend
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->can('edit_comments') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->can('delete_comments') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_comments') || $user->hasRole('super_admin');
    }
}
