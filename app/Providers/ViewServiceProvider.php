<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Check if settings table exists before querying
        if (!Schema::hasTable('settings')) {
            return;
        }

        View::composer('*', function ($view) {
            // Use shorter cache (5 minutes) instead of forever
            $siteSettings = Cache::remember('site_settings_frontend', 300, function () {
                return [
                    'site_name' => Setting::get('site_name', 'MovieStream'),
                    'site_tagline' => Setting::get('site_tagline', 'Watch & Download Movies'),
                    'site_logo' => Setting::get('site_logo'),
                    'site_favicon' => Setting::get('site_favicon'),
                    'footer_text' => Setting::get('footer_text', '© ' . date('Y') . ' MovieStream. All rights reserved.'),
                    'social_facebook' => Setting::get('social_facebook'),
                    'social_twitter' => Setting::get('social_twitter'),
                    'social_instagram' => Setting::get('social_instagram'),
                    'social_youtube' => Setting::get('social_youtube'),
                    'analytics_code' => Setting::get('analytics_code'),
                    'header_scripts' => Setting::get('header_scripts'),
                    'footer_scripts' => Setting::get('footer_scripts'),
                    'donation_link' => Setting::get('donation_link'),
                    'donation_text' => Setting::get('donation_text', 'Dukung Kami'),
                ];
            });

            $view->with('siteSettings', $siteSettings);
        });
    }
}
