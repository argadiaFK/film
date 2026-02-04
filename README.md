# Film Website Boilerplate

A modern film streaming website boilerplate built with Laravel 12, Filament 4, PostgreSQL, and Docker (FrankenPHP/Octane).

## Features

- **Multi-Stream Support**: Each film can have multiple streaming sources (Server 1, Server 2, etc.)
- **Multi-Download with Click Tracking**: Download links with automatic click count tracking
- **Filament Admin Panel**: Complete admin panel for managing:
  - Films (with nested streaming sources & download links)
  - Genres & Countries (taxonomies)
  - SEO Metadata (polymorphic)
  - Users & Roles (Spatie Permission)
- **Docker Ready**: FrankenPHP with Laravel Octane for high performance
- **PostgreSQL Database**: Production-ready database

## Tech Stack

| Technology | Version |
|------------|---------|
| Laravel | 12.x |
| Filament | 4.x |
| PHP | 8.3+ |
| PostgreSQL | 16 |
| Redis | 7 |
| FrankenPHP | Latest |
| Laravel Octane | Latest |

## Quick Start

### 1. Clone & Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key (run after composer install)
php artisan key:generate
```

### 2. Start with Docker

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Seed demo data
docker-compose exec app php artisan db:seed

# Create admin user (if not using seeder)
docker-compose exec app php artisan make:filament-user
```

### 3. Access Application

- **Website**: http://localhost
- **Admin Panel**: http://localhost/admin
- **Default Admin Login** (from seeder):
  - Email: `admin@example.com`
  - Password: `password`

## Local Development (Without Docker)

```bash
# Install dependencies
composer install
npm install

# Setup database (use your local PostgreSQL)
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

## Database Schema

```
films
├── streaming_sources (Multi-stream)
├── download_links (Multi-download with click_count)
├── genres (Many-to-Many)
├── countries (Many-to-Many)
└── seo_metas (Polymorphic)
```

## Admin Panel Resources

| Resource | Features |
|----------|----------|
| FilmResource | Full CRUD with nested streaming sources & download links |
| GenreResource | Simple CRUD with film count |
| CountryResource | Simple CRUD with film count |
| UserResource | User management with role assignment |
| RoleResource | Role & permission management |

## API Endpoints

### Download Tracking

```
GET /download/{downloadLink}
```

Increments click count and redirects to the actual download URL.

**Usage in Blade:**
```blade
<a href="{{ route('download.track', $downloadLink) }}">Download</a>
```

## Project Structure

```
app/
├── Filament/Resources/     # Admin panel resources
├── Http/Controllers/       # Web controllers
└── Models/                 # Eloquent models

database/
├── migrations/             # Database migrations
└── seeders/               # Demo data seeders

.docker/
└── Caddyfile              # FrankenPHP configuration
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Database host | `postgres` |
| `DB_DATABASE` | Database name | `film_db` |
| `DB_USERNAME` | Database user | `film_user` |
| `DB_PASSWORD` | Database password | `secret` |
| `OCTANE_SERVER` | Octane server | `frankenphp` |
| `REDIS_HOST` | Redis host | `redis` |

## Docker Services

| Service | Port | Description |
|---------|------|-------------|
| app | 80, 443 | FrankenPHP + Laravel Octane |
| postgres | 5432 | PostgreSQL 16 |
| redis | 6379 | Redis 7 |

## License

MIT License
