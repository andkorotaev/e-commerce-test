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
- Admin CRUD for categories is built: `CategoryController` (resource, `show` excluded — `edit` covers it), `StoreCategoryRequest`/`UpdateCategoryRequest` (in `app/Http/Requests/Admin/Category/`, sharing a common abstract `CategoryRequest`), a recursive tree list (`components/admin/categories/tree.blade.php`) and a shared create/edit form component.
- `config('localization.locales')` (`config/localization.php`, `[locale => display label]`) is the single source of truth for which languages translatable content is authored in — `CategoryRequest::rules()` and the category form both loop over it instead of hardcoding `uk`/`en`. Adding a 3rd content language is a one-line config change, not a hunt through every file that mentions a locale. (Distinct from `APP_LOCALE`/`APP_FALLBACK_LOCALE` in `.env`, which is the UI language shown to a visitor, not the set of languages content gets translated into.)

## Architecture: how backend code gets written in this project (established with Categories — the reference implementation, copy this shape for every future resource)

Looked at a much heavier DTO/TypedCollection/reflection-based pattern from another project (`api-remastered-v2/Modules/Reading`) and deliberately did NOT copy it — that pattern earns its ceremony for genuinely complex nested data (FFT/chart data); it would be pure overhead for CRUD-shaped resources like ours. What we run instead: **Controller → Service → Repository**, with DTOs as the only thing that crosses a layer boundary. `app/Http/Controllers/Admin/CategoryController.php` + `app/Services/CategoryService.php` + `app/Repositories/{CategoryRepository,CategoryTranslationRepository}.php` + `app/Dto/Category/{CategoryDto,CategoryTranslationDto,CategoryInputDto}.php` is the template to imitate for the next admin resource.

**Repository — no business logic, ever.** Only DB queries in, DTOs out. One repository per Eloquent model/table, and it never reaches into another model's table — `CategoryRepository` (`all()`, `find()`, `create()`, `update()`, `delete()`) only touches `Category`; `CategoryTranslationRepository` is a fully separate class that owns every `CategoryTranslation` write/delete (`upsert()`, `deleteForCategory()`). No repository interfaces — a concrete class only pays for itself once there are multiple real implementations, which there aren't.

**Service — all business logic and orchestration lives here, nowhere else.** `CategoryService` is what the controller actually talks to (never the repository directly). It:
- calls `$dto->image?->store('categories', 'public')` — the actual file-saving side effect (the request/DTO layer never touches the filesystem, only the service does)
- coordinates the two repositories together (`create()` makes the category row via `CategoryRepository`, then loops translations through `CategoryTranslationRepository`)
- wraps every multi-step write (`create`/`update`/`delete`) in `DB::transaction()` — without this, a failure partway through (e.g. one translation insert failing) leaves an orphaned category row with no/partial translations; the transaction makes the whole write atomic
- wraps those same methods in try/catch: `report($e)` to log (Laravel already logs *uncaught* exceptions on its own, but these ARE going to be caught here, so logging has to happen explicitly), then rethrow as a `RuntimeException` with the original as `previous` — read-only methods (`tree()`/`options()`/`find()`) do NOT need this, there's no partial-write state to protect and no cleanup to do
- file cleanup ordering matters: on create, if the transaction fails, the already-uploaded file has to be deleted by hand (it's not part of the DB transaction, storing to disk doesn't roll back). On update, the **new** file is deleted if the transaction fails; the **old** file is only deleted *after* the transaction commits successfully — deleting the old image before you're sure the new state is saved would leave a category with a broken image reference if the write then failed.
- builds the recursive tree and the flattened parent-select options list (`buildTree()`/`flattenForOptions()`) — that recursion is logic, not a query, so it does not belong in the repository. `CategoryDto::withChildren()` returns a new instance with `children` replaced (needed because the DTO is `readonly`) — this is how the tree gets assembled from a flat list without mutating anything.
- exposes a thin `find(int $id): ?CategoryDto` passthrough too — even a "just fetch one" read still goes through the service, controllers never call a repository directly, no exceptions to that rule.

**Controller — thin, HTTP-only, never touches Eloquent.** `CategoryController` injects `CategoryService` (never a repository). Route parameters are plain typed IDs (`int $categoryId`), not Eloquent implicit model binding (`Category $category`) — the controller only ever needs the ID to hand to the service, so binding a full Eloquent model via the route would be a wasted query. This means:
- the resource route has to explicitly rename its parameter to match: `Route::resource(...)->parameters(['categories' => 'categoryId'])`, otherwise the URI placeholder (`{category}`) and the controller's parameter name disagree and Laravel can't inject it.
- 404-for-missing-record is no longer automatic (implicit binding used to throw `ModelNotFoundException` for you) — `edit()` has to check explicitly: `abort_if($dto === null, 404)`.
- when a FormRequest genuinely needs the Eloquent model (not just its ID) for a validation rule — `UpdateCategoryRequest` needs `$category->translations`/`$category->children` to validate "slug unique excluding myself" and "not my own parent/descendant" — the FormRequest fetches and memoizes it itself (`protected function category(): Category { return $this->categoryModel ??= Category::with([...])->findOrFail($this->route('categoryId')); }`), it does not rely on route-model-binding magic to hand it a model.
- `store()`/`update()` call `$request->getDto()` (see below) and pass straight to the service; no `$request->validated()` array wrangling, no file-store calls, no translation loops in the controller itself.

**`FormRequest::getDto()`** — every write-capable FormRequest gets a `getDto()` method (defined once on the shared abstract base, e.g. `CategoryRequest`, inherited by `Store...`/`Update...`) that builds and returns the input DTO from validated data: `CategoryInputDto::fromArray($this->validated())`. The controller calls `$request->getDto()`, never builds a DTO from `$request->validated()` itself. Since `image` has an `'image'` validation rule, `validated()` includes the raw `UploadedFile` (not a stored path yet) — the DTO carries the file object; turning it into a stored path is explicitly the service's job, never the request's or the DTO's own.

(The login flow predates this convention and does it slightly differently — `LoginRequest::authenticate()` does field validation + rate limiting + the actual `Auth::attempt()` all in one method, because a login attempt has no separate "input DTO" worth building; that's a deliberate, narrow exception, not evidence the `getDto()` rule is optional.)

**DTOs — plain, no reflection magic.** `readonly` classes, real typed constructors via constructor property promotion. No `BaseDto`/attribute-caster/reflection hydration. Two rules on when to split a DTO in two vs share one:
- **Split into separate input/output classes only when the shape actually differs.** `CategoryDto` (output: has `id`, `children` for the tree) and `CategoryInputDto` (input: no `id`, holds the raw `UploadedFile`) are genuinely different shapes — kept separate, and the input one is named `...InputDto` (not `...Data`) to read naturally next to `getDto()`.
- **Don't split when the shape is identical.** `CategoryTranslationDto` is used for BOTH directions (`fromModel()` for reads, `fromArray()` for writes) because a translation's fields are the same either way — a mirror `CategoryTranslationData` class was written once during this build and then deleted for being pure duplication. Default to one shared DTO; only fork it once the fields genuinely diverge.

**No custom `TypedCollection` class.** Plain `Illuminate\Support\Collection` + a `@return Collection<int, CategoryDto>` PHPDoc is enough for IDE/PHPStan support. Build a list of DTOs with one `->map()` call, never a manual `foreach`. Only introduce a real typed collection class if the same list-filtering/query logic over the same entity is found duplicated in more than one place — that's the actual signal it would pay for itself, not a rule applied up front.

**Views get DTOs, not raw Eloquent models — everywhere, no exceptions currently.** `tree()` passes `Collection<CategoryDto>` to the index view; `edit()` passes a `CategoryDto` (via `CategoryService::find()`) to the form, not the Eloquent model. DTO properties are camelCase (`isActive`, `sortOrder`, `parentId`, `metaTitle`, `metaDescription`) — Blade templates working with a DTO use those, not the Eloquent snake_case (`is_active`, `sort_order`, etc.) that would apply to a raw model. A DTO doesn't implement `UrlRoutable` the way an Eloquent model does, so `route()` calls in Blade need the id passed explicitly — `route('admin.categories.update', $category->id)`, not `route(..., $category)`.

**Config over hardcoding anything that could grow.** `config('localization.locales')` (`config/localization.php`, `[locale => label]`) is the one place the set of content languages is listed — `CategoryRequest::rules()` and the category form both loop over it. Adding a 3rd language is a one-line config change, never a hunt through files for hardcoded `'uk'`/`'en'` pairs. Same instinct applies to any future "closed list that might grow" — put it in config, loop over it, don't hardcode the list at each call site.

**Other things considered and explicitly rejected, so as not to re-litigate them:**
- Building DTOs from `$model->toArray()` instead of straight off the hydrated model's properties — rejected, `toArray()` doesn't skip building the model, it adds a whole extra serialization pass on top of an already-built model. `fromModel()` reading properties directly is strictly less work.
- Bypassing Eloquent entirely (`Category::query()->toBase()->get()` / `DB::table(...)`) for a "no wasted model construction" micro-optimization — rejected for this project's scale (the win is unmeasurable on a categories-sized table, and the cost — losing `with()` eager loading and automatic casts, needing manual joins/grouping — is real). Worth reconsidering only if a specific table genuinely gets large/hot.
