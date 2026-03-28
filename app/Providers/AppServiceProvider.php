<?php

namespace App\Providers;

use App\Listeners\RecordLoginHistory;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Series;
use App\Models\Setting;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (to avoid mixed content loading issues when placed behind SSL proxies)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register activity log observer for all trackable models
        $observer = ActivityLogObserver::class;

        Film::observe($observer);
        Series::observe($observer);
        Episode::observe($observer);
        Genre::observe($observer);
        Country::observe($observer);
        User::observe($observer);
        Comment::observe($observer);
        Setting::observe($observer);

        // Register login/logout event listeners
        $loginListener = new RecordLoginHistory();
        Event::listen(Login::class, [$loginListener, 'handleLogin']);
        Event::listen(Logout::class, [$loginListener, 'handleLogout']);
    }
}

