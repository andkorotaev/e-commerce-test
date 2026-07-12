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

### Component architecture

**Hard constraint (verified by breaking it): `<x-a.b.c />` only resolves under `resources/views/components/`, and only in one of two leaf shapes: `c.blade.php` (flat) or `c/index.blade.php` (folder+index). Files outside `components/`, or extra unrecognized path segments (e.g. a `components/` sub-folder that isn't the actual Blade root), do NOT resolve — confirmed via a real `InvalidArgumentException: Unable to locate a class or view for component`. Don't reinvent this; every nesting decision below has to reduce to that rule.**

- A component with no children: a single flat file named for what it does, e.g. `components/front/layouts/header/logo.blade.php` — no subfolder, no generic `index.blade.php`.
- A component WITH children: a flat file for itself + a same-named sibling folder holding only its children, e.g. `components/front/layouts/header.blade.php` (the component) next to `components/front/layouts/header/` (folder with `logo.blade.php`, `nav.blade.php`, `search.blade.php`, `cart.blade.php`, `announcement-bar.blade.php`). Tag: `<x-front.layouts.header />` for itself, `<x-front.layouts.header.logo />` for a child. Both the file and the folder can coexist under the same name since one's a file and one's a directory.
- Arbitrary nesting depth is fine as long as every level follows the rule above — this is how `front/layouts/header/...` (3 levels deep) still resolves cleanly.
- JS mirrors the same relative path 1:1 when a component needs behavior: `resources/js/components/<same path>.js` — not yet needed, no component has JS behavior yet.
- A component's root element carries `data-component="<name>"`; an autoloader in `app.js` uses `import.meta.glob('./components/**/*.js')` to lazy-load and `init(el)` only the JS for components actually present in the DOM on that page — planned, not yet wired up.
- CSS is NOT split per page/component — Tailwind already emits one `app.css` containing only utility classes actually used across the whole project (content-scanned), which is small and cached site-wide; per-page CSS splitting was considered and rejected as unnecessary complexity.
- Views split into `resources/views/front/` (storefront, actual page views only) and `resources/views/admin/` (not started yet); reusable components always live under `resources/views/components/front/...`, never inside `resources/views/front/` itself — that split is what the hard constraint above requires.
- Current layout/header tree: `components/front/layouts/layout.blade.php` (page shell: head/fonts/@vite + header + `$slot`, used via `<x-front.layouts.layout>`) and `components/front/layouts/header.blade.php` + `components/front/layouts/header/{logo,nav,search,cart,announcement-bar}.blade.php`.
- Home page: `resources/views/front/home/index.blade.php` (route `/`) — currently a placeholder body, header is final.

## Design direction

- Brand concept: "OCRE" — small-batch, natural-dye clothing. Palette and copy are grounded in real dye pigments (indigo, walnut, cochineal, weld), not decorative.
- Chosen header layout: "Split ledger" — thin dark utility bar (shipping notice + locale/currency) above a light main header (logo left, nav, search + cart right)
- Chosen colorway: **Indigo** — `--color-indigo-vat: #202b3b` (dark ground), `--color-bone: #ede6d8` (light ground), `--color-ink: #211c16` (text on bone), `--color-madder: #a63b2c` (single accent), `--color-stone: #b7afa0` (muted text) — defined in `resources/css/app.css` `@theme`
- Type: **Fraunces** (display/serif, used sparingly), **Schibsted Grotesk** (body/UI sans), **IBM Plex Mono** (prices, tags, utility bar) — loaded via Bunny Fonts
- Alternative colorways considered and rejected: Walnut (warm brown-black), Cochineal (wine/aubergine), Weld (bottle green) — see git history if revisiting
- Other header layouts considered and rejected: "Vat lid" (full dark header), "Minimal mark" (nav hidden behind menu trigger), "Two-tier dark" (fully dark two-row header)

## Backend conventions

- Validation lives in `App\Http\Requests\...` FormRequest classes, never inline `$request->validate()` in a controller. The Requests tree mirrors the Controllers tree 1:1: `app/Http/Controllers/Admin/AuthController.php` → `app/Http/Requests/Admin/LoginRequest.php`. Controller methods type-hint the FormRequest and read `$request->validated()`.
- Admin auth: separate `admin` guard + `admins` table/`Admin` model (not the customer `users` table) — a compromised customer account can never reach admin routes even via a role-check bug. No public registration; admins are provisioned out-of-band (console command / tinker), never via a form.
- Login rate limiting is hand-rolled on `Illuminate\Support\Facades\RateLimiter` (5 attempts / 60s decay, keyed on `email|ip`) — Laravel 12 dropped the old `ThrottlesLogins` trait, it no longer exists in the framework. Lives inside the FormRequest's own `authenticate()` method (Breeze-style), not the controller — the request class owns the full "is this login valid" question, field format AND rate limit AND credential check; the controller just calls `$request->authenticate()`.
- Session fixation: `session()->regenerate()` right after a successful login, `invalidate()` + `regenerateToken()` on logout.
- Routes are split by area into their own files, not one shared `web.php`: `routes/front.php` (storefront), `routes/admin.php` (admin, prefix `/admin`, name prefix `admin.`). `routes/web.php` just `require`s both so they stay in the default `web` middleware group.
- Admin routes: `admin.login` (GET/POST), `admin.logout` (POST, `auth:admin`), `admin.dashboard` (GET `/admin`, `auth:admin`, currently a placeholder). Unauthenticated guests hitting any `admin*` path are redirected to `admin.login` via `redirectGuestsTo` in `bootstrap/app.php` (guard-aware — front guests would redirect to `front.home` instead).
- Admin layout is deliberately plain/utilitarian (`components/admin/layouts/layout.blade.php`) — no OCRE storefront branding/fonts, this is an internal tool not customer-facing.
- New admins: `php artisan admin:create` (interactive Laravel Prompts, validates + hashes password) — no self-registration route exists or should exist.
- Also fixed a related bug while adding tests: `redirectUsersTo` wasn't configured in `bootstrap/app.php`, so an already-authenticated admin hitting `/admin/login` silently fell through Laravel's default `guest` middleware fallback (checks for named routes `dashboard`/`home`, neither exists here) to `/` — the storefront homepage, not the admin dashboard. Fixed by adding a guard-aware `redirectUsersTo` alongside the existing `redirectGuestsTo`.

## Testing

- Plain PHPUnit (no Pest). `phpunit.xml` sets `APP_ENV=testing`, which auto-disables CSRF verification for the test run (`VerifyCsrfToken::runningUnitTests()`) — feature tests can `$this->post()` without manually handling a token.
- `DB_DATABASE=testing` still uses the real `mysql` connection (not sqlite) — Sail's MySQL container already creates a `testing` database on boot (see `vendor/laravel/sail/database/mysql/create-testing-database.sh`), so no extra setup is needed; feature tests use `RefreshDatabase`.
- `SESSION_DRIVER=array` / `CACHE_STORE=array` in tests — session and rate-limiter state never touch the real `sessions`/`cache` tables, and reset cleanly between tests since Laravel rebuilds the app container per test.
- `tests/Feature/Admin/LoginTest.php` is the reference example for auth-flow tests: renders, redirects for both directions of the `guest`/`auth` middleware, valid/invalid credentials, the rate-limit lockout (assert the *generic* error message, not just "it failed" — enumeration-safety is a first-class thing to test here, not an afterthought), logout.
- `database/factories/AdminFactory.php` mirrors `UserFactory` (same static-cached-password trick to avoid re-hashing per row) — needed `Admin::class` to `use HasFactory`, which the model didn't have initially.
- Run: `./vendor/bin/sail artisan test` (or `--filter=LoginTest` for just this suite).

## Localization

- Primary locale: Ukrainian (`uk`), fallback: English (`en`) — set via `APP_LOCALE`/`APP_FALLBACK_LOCALE` in `.env`
- Translation files: `lang/uk/*.php`, `lang/en/*.php` (plain PHP arrays, Laravel's `__('file.key')` convention)
- No locale switcher UI yet, no locale-prefixed routes — only the default locale is wired up so far

## Data model: Categories

- Two tables, not one: `categories` (structural/non-translatable: `parent_id` self-reference for nesting, `image`, `is_active`, `sort_order`) and `category_translations` (`category_id`, `locale`, `name`, `slug`, `h1`, `meta_title`, `meta_description`, `description`) — one row per locale per category. Chosen over `name_uk`/`name_en` columns specifically so adding a 3rd language later is a data migration, not a schema migration.
- `slug` is unique per-locale (`unique(['locale', 'slug'])`), not globally — uk and en slugs for the same category are independent and can differ.
- SEO fields on a category: `slug` (clean URL), `meta_title` (often not the same text as the nav label), `meta_description` (SERP snippet, doesn't affect ranking but drives CTR), `description` (on-page body content — Google wants real text on category pages, not just a product grid), `h1` (only when it needs to differ from the nav label). Deliberately skipped `meta_keywords` — Google has ignored it since the mid-2000s.
- `Category::translation($locale = null)` is a `hasOne` (not `hasMany`) scoped to one locale, defaulting to `app()->getLocale()` — the ergonomic accessor for "give me this category's text in the current language."
- Both `CategoryFactory` and `CategoryTranslationFactory` exist; `CategoryFactory::configure()` auto-creates uk+en translations via `afterCreating` — a bare `Category::factory()->create()` is realistic/usable immediately, never an empty shell missing translations.
- Admin CRUD for categories (controller/requests/views) not built yet — only the migrations/models/factories exist so far.
