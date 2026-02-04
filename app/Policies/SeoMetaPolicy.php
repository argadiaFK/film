<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SeoMeta;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeoMetaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_seo') || $user->hasRole('super_admin');
    }

    public function view(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('view_seo') || $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->can('edit_seo') || $user->hasRole('super_admin');
    }

    public function update(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('edit_seo') || $user->hasRole('super_admin');
    }

    public function delete(User $user, SeoMeta $seoMeta): bool
    {
        return $user->can('edit_seo') || $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('edit_seo') || $user->hasRole('super_admin');
    }
}
