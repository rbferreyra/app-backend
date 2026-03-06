# app-backend

> Laravel 12 REST API — Real Estate Platform  
> Stack: PHP 8.4 · MySQL 8 · Redis 7 · Nginx · Supervisor · Cron

---

## Stack overview

| Service | Image | Purpose |
|---------|-------|---------|
| `realestate-app` | PHP 8.4-FPM | Application (Laravel) |
| `realestate-nginx` | Nginx 1.26 Alpine | Web server / reverse proxy |
| `realestate-db` | MySQL 8.0 | Database |
| `realestate-redis` | Redis 7.2 Alpine | Cache, queues, sessions |
| `realestate-supervisor` | PHP 8.4-FPM | Queue workers / Horizon |
| `realestate-cron` | PHP 8.4-FPM | Laravel Scheduler |

> This stack mirrors [Laravel Forge's App Server](https://forge.laravel.com/docs/servers/types) provisioning (Nginx + PHP + MySQL + Redis + Memcached + Node.js + Supervisor).

---

## Requirements

- Docker Desktop 4.x+ or Docker Engine 24+
- Docker Compose v2
- Make (usually pre-installed on macOS/Linux)
- Git

---

## First-time setup

### 1. Clone and enter the project

```bash
git clone <repo-url> app-backend
cd app-backend
```

### 2. Build and start containers

```bash
make up
```

This builds all images and starts the containers in detached mode.  
First build takes ~3–5 minutes (downloading PHP + extensions).

### 3. Install Laravel

```bash
make install
```

This will:
- Run `composer create-project laravel/laravel:^12.0 .` inside the container
- Copy `.env.example` → `.env`
- Generate `APP_KEY`
- Run migrations

> ⚠️ Only run `make install` once — it will overwrite existing files.

### 4. Access the application

| URL | Service |
|-----|---------|
| http://localhost | Laravel app |
| http://localhost:5173 | Vite dev server |
| localhost:3306 | MySQL (via TablePlus / DBeaver) |
| localhost:6379 | Redis |

---

## Daily workflow

```bash
make up           # Start containers
make down         # Stop containers
make shell        # SSH into app container
make logs         # Tail all logs
make cache-clear  # Clear Laravel caches
```

---

## Common commands

```bash
# Artisan
make artisan cmd="route:list"
make artisan cmd="make:model Property -m"
make migrate
make migrate-fresh           # drops everything and re-seeds

# Composer
make require pkg="spatie/laravel-permission"
make composer-install

# Tests
make test
make test-filter f=AuthTest

# Code style (Laravel Pint)
make pint
```

---

## Project structure

```
app-backend/
├── app/
│   ├── Modules/             # Feature modules (Auth, Properties, ...)
│   │   └── Auth/
│   │       ├── Actions/
│   │       ├── Controllers/
│   │       ├── DTOs/
│   │       ├── Events/
│   │       ├── Listeners/
│   │       ├── Mail/
│   │       ├── Models/
│   │       ├── Repositories/
│   │       ├── Requests/
│   │       ├── Resources/
│   │       └── Routes/
│   └── Shared/              # Cross-module utilities
│       ├── Contracts/
│       ├── Exceptions/
│       ├── Helpers/
│       ├── Middlewares/
│       └── Traits/
├── .docker/
│   ├── app/                 # PHP-FPM Dockerfile + php.ini
│   ├── nginx/               # Nginx config
│   ├── mysql/               # MySQL config + data volume
│   ├── redis/               # Redis data volume
│   ├── supervisor/          # Supervisor Dockerfile + config
│   └── cron/                # Cron Dockerfile
├── docker-compose.yml
├── Makefile
└── .env.example
```

---

## Environment variables

Copy `.env.example` to `.env` and adjust as needed:

| Variable | Default | Description |
|----------|---------|-------------|
| `FORWARD_APP_PORT` | `80` | Nginx port on host |
| `FORWARD_VITE_PORT` | `5173` | Vite HMR port on host |
| `DB_PORT` | `3306` | MySQL port on host |
| `FORWARD_REDIS_PORT` | `6379` | Redis port on host |
| `DB_DATABASE` | `realestate` | Database name |
| `DB_USERNAME` | `forge` | Database user |
| `DB_PASSWORD` | `secret` | Database password |
| `REDIS_PASSWORD` | `secret` | Redis password |

---

## Supervisor (Queue Workers)

Workers are managed by the `realestate-supervisor` container.  
Config: `.docker/supervisor/supervisord.conf`

By default, 2 `queue:work` workers are running.  
After installing **Laravel Horizon**, uncomment the `[program:horizon]` block and comment out `[program:queue-worker]`.

---

## Cron (Scheduler)

The `realestate-cron` container runs `php artisan schedule:run` every minute,  
mirroring Forge's built-in Scheduler feature.

Logs: `storage/logs/cron.log`

---

## Forge parity

| Forge Feature | Docker equivalent |
|--------------|-------------------|
| App Server (PHP + Nginx) | `realestate-app` + `realestate-nginx` |
| Database Server | `realestate-db` |
| Cache Server | `realestate-redis` |
| Worker Server | `realestate-supervisor` |
| Scheduler | `realestate-cron` |
| forge user (UID 1000) | `USER=forge` ARG in Dockerfiles |

---

## Troubleshooting

**Containers not starting?**
```bash
make logs   # Check for errors
```

**Permission errors on storage/?**
```bash
make shell-root
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R forge:forge /var/www/storage
```

**MySQL connection refused?**  
Wait ~15s after `make up` — MySQL takes time to initialize on first run.

**Port already in use?**  
Edit `FORWARD_APP_PORT` or other port variables in your `.env`.
