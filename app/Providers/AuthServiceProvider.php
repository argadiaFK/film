<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Ad;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Film;
use App\Models\Genre;
use App\Models\LoginHistory;
use App\Models\SeoMeta;
use App\Models\Series;
use App\Models\Setting;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\AdPolicy;
use App\Policies\CommentPolicy;
use App\Policies\CountryPolicy;
use App\Policies\EpisodePolicy;
use App\Policies\FilmPolicy;
use App\Policies\GenrePolicy;
use App\Policies\LoginHistoryPolicy;
use App\Policies\RolePolicy;
use App\Policies\SeoMetaPolicy;
use App\Policies\SeriesPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Film::class => FilmPolicy::class,
        Series::class => SeriesPolicy::class,
        Episode::class => EpisodePolicy::class,
        Genre::class => GenrePolicy::class,
        Country::class => CountryPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Comment::class => CommentPolicy::class,
        Ad::class => AdPolicy::class,
        SeoMeta::class => SeoMetaPolicy::class,
        Setting::class => SettingPolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
        LoginHistory::class => LoginHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
