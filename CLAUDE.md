# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A full-stack Laravel 12 + Vue 3 application for developer portfolio management and hackathon event coordination. Features role-based access control (Super Admin, Admin, HR, Developer), developer profiles with skills/experience/projects/blogs, hackathon management with teams and voting, badge achievements, and a newsletter system.

## Tech Stack

- **Backend:** PHP 8.4, Laravel 12, Inertia.js v2
- **Frontend:** Vue 3, TypeScript 5.2, Vite 7, Tailwind CSS 4
- **Auth:** Laravel Fortify + Spatie Permission (role-based)
- **Testing:** Pest 4 (with browser testing support)
- **Database:** SQLite (default), MySQL supported

## Common Commands

```bash
# Development
composer run dev              # Start all services (artisan serve + queue + pail + npm dev)
npm run dev                   # Vite dev server only

# Building
npm run build                 # Production build
npm run build:ssr             # Production build with SSR

# Testing
php artisan test                          # Run all tests
php artisan test tests/Feature/ExampleTest.php  # Run a specific test file
php artisan test --filter=testName        # Run tests matching a name

# Linting & Formatting
vendor/bin/pint --dirty       # Format only changed PHP files (run before finalizing changes)
vendor/bin/pint               # Format all PHP files
npm run lint                  # ESLint with auto-fix
npm run format                # Prettier format JS/Vue/TS

# Setup
composer run setup            # Full initial setup (deps, migrate, build)
```

## Architecture

### Backend Structure

- **Controllers:** `app/Http/Controllers/` — split into `Api/` (4), `Dashboard/` (13), `Settings/` (5), and public-facing controllers
- **Models:** `app/Models/` — 26 Eloquent models. Key: `User`, `Developer`, `Hackathon`, `Badge`, `Skill`, `JobTitle`
- **Policies:** `app/Policies/` — one per model, using Spatie permissions
- **Enums:** `app/Enums/` — 19 enums for statuses and types (TitleCase keys convention)
- **Observers:** `app/Observers/` — 8 observers for activity logging
- **Form Requests:** `app/Http/Requests/` — 37 validation classes (always use these, never inline validation)
- **Services:** `app/Services/` — `MailtrapService`, `PolicyPermissionService`
- **Middleware:** `app/Http/Middleware/` — `HandleInertiaRequests`, `HandleAppearance`, `IsSuperAdmin`

### Frontend Structure

- **Pages:** `resources/js/pages/` — Inertia page components organized by feature
- **Components:** `resources/js/components/` — 46+ reusable Vue components (Reka UI primitives, TipTap editor)
- **Layouts:** `resources/js/layouts/` — AppLayout, DashboardLayout, AuthLayout
- **Composables:** `resources/js/composables/` — Vue composition functions
- **Actions:** `resources/js/actions/` — Wayfinder server actions
- **Types:** `resources/js/types/` — TypeScript type definitions

### Routing

Routes defined in `routes/web.php` (109 routes), `routes/api.php` (4 endpoints), `routes/settings.php`. Key groups:
- Public: `/`, `/developers/{slug}`, `/blogs`, `/hackathons`, `/badges`
- Dashboard (auth + verified): role-gated admin, developer, and super admin sections under `/dashboard`
- API: `/api/developers`, `/api/job-titles`, `/api/skills`, `/api/badges`

### Key Patterns

- **Registration:** `bootstrap/app.php` for middleware, exceptions, routing. `bootstrap/providers.php` for service providers. Commands auto-register from `app/Console/Commands/`.
- **Artisan generation:** Always use `php artisan make:*` with `--no-interaction` to create new files (controllers, models, migrations, tests, etc.)
- **Queries:** Use Eloquent with eager loading. Avoid `DB::` facade — prefer `Model::query()`. Spatie Query Builder for filtering.
- **Storage:** AWS S3 via Flysystem, with `StorageHelper` for URL generation.

## Code Conventions

### PHP
- PHP 8 constructor property promotion
- Explicit return types and type hints on all methods
- PHPDoc blocks over inline comments
- Curly braces required for all control structures
- Enum keys in TitleCase
- Pint with `laravel` preset for formatting
- Never use `env()` outside config files — use `config()` instead

### Frontend
- Vue 3 Composition API with TypeScript
- ESLint + Prettier for formatting
- Inertia v2 features: deferred props, polling, prefetching, infinite scrolling

### Testing
- All tests in Pest syntax (`it('...', function () { ... })`)
- Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`, browser tests in `tests/Browser/`
- Use model factories (check for custom states before manual setup)
- Use datasets for validation rule tests
- Run `vendor/bin/pint --dirty` before finalizing changes
