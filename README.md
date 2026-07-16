# OCRE — test e-commerce project

This is a learning/practice project — a fictional clothing store (**OCRE**) built with plain **Laravel** (Blade + vanilla JS, no SPA framework like Vue/React). The goal of this project is to practice and refresh e-commerce development skills: product catalog, cart, checkout, reviews, wishlist, admin panel, CI/CD, Docker.

This is **not a real store** — the delivery carriers, payment banks, etc. are fictional; nothing is actually charged or shipped.

## Stack

- **Backend:** Laravel 12, MySQL, PHPUnit
- **Frontend:** Blade components + vanilla JS (no Vue/React/Alpine), Tailwind CSS v4 + Vite
- **Infrastructure:** Docker (Laravel Sail for local development), GitHub Actions (CI/CD), production deployment via Docker Compose (nginx + php-fpm + MySQL in containers)

## Running locally

The easiest way — one script that sets everything up from scratch (composer install, `.env`, Sail, key generation, `npm install`/`build`, migrations):

```bash
./setup.sh
```

The script is idempotent, safe to re-run.

The site will be available at **http://localhost:8000**.

### Manual, step by step

```bash
composer install
cp .env.example .env
php artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail npm install
./vendor/bin/sail npm run build   # or `npm run dev` for HMR during development
./vendor/bin/sail artisan migrate
```

### Useful commands (via Sail)

```bash
./vendor/bin/sail up -d              # start containers (app, mysql, redis)
./vendor/bin/sail artisan migrate    # run migrations
./vendor/bin/sail artisan test       # run tests
./vendor/bin/sail npm run dev        # Vite dev server with hot reload
./vendor/bin/sail down               # stop containers
```

Create an admin account for `/admin` (there's deliberately no public admin registration form):

```bash
./vendor/bin/sail artisan admin:create
```

## Tests

```bash
./vendor/bin/sail artisan test
```

Plus code style check (Laravel Pint):

```bash
./vendor/bin/sail exec laravel.test vendor/bin/pint --test
```

## CI/CD

On every push/PR to `main`, GitHub Actions runs the tests and style check; on push to `main`, it builds a Docker image and (once server access is configured) deploys it to production. See `.github/workflows/ci-cd.yml` for details.

## Project documentation

A detailed description of the architecture (Controller → Service → Repository → DTO), conventions, and decisions made during development lives in `CLAUDE.md`.
