Lf# CLAUDE.md

> Check `ClaudeCustomRequirements.md` in the project root if it exists — it contains additional custom requirements for how work should be done in this project.

## Stack

Laravel 12 (Blade + vanilla JS, no SPA framework), e-commerce project.

## Setup

`./setup.sh` — one command to deploy and run the whole project from scratch (composer install, .env, Sail up, key generation, npm install/build, migrate). Idempotent, safe to re-run.

## Docker (Laravel Sail)

- Services: `laravel.test` (PHP 8.5, app), `mysql` (8.4), `redis` (alpine)
- Host port overrides (to avoid conflicts with local Apache/MariaDB running outside Docker): `APP_PORT=8000`, `FORWARD_DB_PORT=3307`
- App URL: http://localhost:8000
- Start: `./vendor/bin/sail up -d`
- DB/cache/queue/session driver: `database` (not Redis, by choice)

## Frontend

- Tailwind CSS v4 + Vite (`@tailwindcss/vite` plugin), came pre-wired with the Laravel 12 skeleton
- Run npm through the container: `./vendor/bin/sail npm install`, `sail npm run dev` (HMR), `sail npm run build`
