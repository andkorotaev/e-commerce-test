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

## Localization

- Primary locale: Ukrainian (`uk`), fallback: English (`en`) — set via `APP_LOCALE`/`APP_FALLBACK_LOCALE` in `.env`
- Translation files: `lang/uk/*.php`, `lang/en/*.php` (plain PHP arrays, Laravel's `__('file.key')` convention)
- No locale switcher UI yet, no locale-prefixed routes — only the default locale is wired up so far
