Lf# CLAUDE.md

> Check `ClaudeCustomRequirements.md` in the project root if it exists — it contains additional custom requirements for how work should be done in this project.

## Stack

Laravel 12 (Blade + vanilla JS, no SPA framework), e-commerce project.

## Docker (Laravel Sail)

- Services: `laravel.test` (PHP 8.5, app), `mysql` (8.4), `redis` (alpine)
- Host port overrides (to avoid conflicts with local Apache/MariaDB running outside Docker): `APP_PORT=8000`, `FORWARD_DB_PORT=3307`
- App URL: http://localhost:8000
- Start: `./vendor/bin/sail up -d`
- DB/cache/queue/session driver: `database` (not Redis, by choice)
