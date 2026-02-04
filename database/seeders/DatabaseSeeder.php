<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\DownloadLink;
use App\Models\Film;
use App\Models\Genre;
use App\Models\StreamingSource;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Film permissions
            'view_films',
            'create_films',
            'edit_films',
            'delete_films',
            // Series permissions
            'view_series',
            'create_series',
            'edit_series',
            'delete_series',
            // Genre permissions
            'view_genres',
            'create_genres',
            'edit_genres',
            'delete_genres',
            // Country permissions
            'view_countries',
            'create_countries',
            'edit_countries',
            'delete_countries',
            // Comment permissions
            'view_comments',
            'edit_comments',
            'delete_comments',
            'moderate_comments',
            // User permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            // Role permissions
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            // SEO permissions
            'view_seo',
            'edit_seo',
            // Settings permissions
            'manage_settings',
            // Ads permissions
            'view_ads',
            'create_ads',
            'edit_ads',
            'delete_ads',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles (use firstOrCreate to avoid duplicates)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        // Assign permissions to roles
        // Super Admin - all permissions
        $superAdminRole->syncPermissions($permissions);

        // Admin - content & settings (no user/role management)
        $adminRole->syncPermissions([
            'view_films',
            'create_films',
            'edit_films',
            'delete_films',
            'view_genres',
            'create_genres',
            'edit_genres',
            'delete_genres',
            'view_countries',
            'create_countries',
            'edit_countries',
            'delete_countries',
            'view_comments',
            'edit_comments',
            'delete_comments',
            'moderate_comments',
            'view_seo',
            'edit_seo',
            'manage_settings',
        ]);

        // Editor - only films and comments
        $editorRole->syncPermissions([
            'view_films',
            'create_films',
            'edit_films',
            'view_genres',
            'view_countries',
            'view_comments',
            'moderate_comments',
        ]);

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create default settings
        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'Film Boilerplate', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Nonton dan download film gratis', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'admin@example.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'enable_comments', 'value' => '1', 'type' => 'boolean', 'group' => 'comments'],
            ['key' => 'comments_require_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'comments'],
            ['key' => 'enable_guest_comments', 'value' => '0', 'type' => 'boolean', 'group' => 'comments'],
            ['key' => 'footer_text', 'value' => '© ' . date('Y') . ' Film Boilerplate. All rights reserved.', 'type' => 'string', 'group' => 'footer'],
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'string', 'group' => 'analytics'],
        ];

        foreach ($defaultSettings as $setting) {
            \App\Models\Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Create genres
        $genres = [
            ['name' => 'Action', 'slug' => 'action'],
            ['name' => 'Comedy', 'slug' => 'comedy'],
            ['name' => 'Drama', 'slug' => 'drama'],
            ['name' => 'Horror', 'slug' => 'horror'],
            ['name' => 'Sci-Fi', 'slug' => 'sci-fi'],
            ['name' => 'Romance', 'slug' => 'romance'],
            ['name' => 'Thriller', 'slug' => 'thriller'],
            ['name' => 'Animation', 'slug' => 'animation'],
        ];

        foreach ($genres as $genre) {
            Genre::create($genre);
        }

        // Create countries
        $countries = [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United Kingdom', 'code' => 'UK'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'South Korea', 'code' => 'KR'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Indonesia', 'code' => 'ID'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }

        // Create sample films
        $films = [
            [
                'title' => 'Sample Action Movie',
                'slug' => 'sample-action-movie',
                'synopsis' => 'An exciting action movie with thrilling sequences and amazing stunts.',
                'year' => 2024,
                'duration_minutes' => 120,
                'status' => 'published',
                'genres' => ['action', 'thriller'],
                'countries' => ['US'],
            ],
            [
                'title' => 'Romantic Comedy',
                'slug' => 'romantic-comedy',
                'synopsis' => 'A heartwarming romantic comedy that will make you laugh and cry.',
                'year' => 2024,
                'duration_minutes' => 95,
                'status' => 'published',
                'genres' => ['comedy', 'romance'],
                'countries' => ['US', 'UK'],
            ],
            [
                'title' => 'Sci-Fi Adventure',
                'slug' => 'sci-fi-adventure',
                'synopsis' => 'A futuristic adventure through space and time.',
                'year' => 2023,
                'duration_minutes' => 150,
                'status' => 'published',
                'genres' => ['sci-fi', 'action'],
                'countries' => ['JP'],
            ],
        ];

        foreach ($films as $filmData) {
            $genreSlugs = $filmData['genres'];
            $countryCodes = $filmData['countries'];
            unset($filmData['genres'], $filmData['countries']);

            $film = Film::create($filmData);

            // Attach genres
            $genreIds = Genre::whereIn('slug', $genreSlugs)->pluck('id');
            $film->genres()->attach($genreIds);

            // Attach countries
            $countryIds = Country::whereIn('code', $countryCodes)->pluck('id');
            $film->countries()->attach($countryIds);

            // Add streaming sources
            StreamingSource::create([
                'film_id' => $film->id,
                'name' => 'Server 1 - Main',
                'url' => 'https://example.com/embed/server1/' . $film->slug,
                'type' => 'embed',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            StreamingSource::create([
                'film_id' => $film->id,
                'name' => 'Server 2 - Backup',
                'url' => 'https://example.com/embed/server2/' . $film->slug,
                'type' => 'embed',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            // Add download links
            DownloadLink::create([
                'film_id' => $film->id,
                'name' => 'Google Drive - 720p',
                'url' => 'https://drive.google.com/example/' . $film->slug . '-720p',
                'quality' => '720p',
                'size' => '1.2 GB',
                'click_count' => rand(50, 500),
                'is_active' => true,
            ]);

            DownloadLink::create([
                'film_id' => $film->id,
                'name' => 'Google Drive - 1080p',
                'url' => 'https://drive.google.com/example/' . $film->slug . '-1080p',
                'quality' => '1080p',
                'size' => '2.5 GB',
                'click_count' => rand(100, 1000),
                'is_active' => true,
            ]);
        }
    }
}
