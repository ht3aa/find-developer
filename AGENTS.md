# AI Agent Instructions

Instructions for AI agents (Cursor, Copilot, Claude Code, etc.) working in this repository.

## Project

Laravel 12 + Vue 3 application for developer portfolio management and hackathon event coordination. Roles: Super Admin, Admin, HR, Developer.

**Full details:** See [CLAUDE.md](./CLAUDE.md) for architecture, structure, and conventions.

## Before Making Changes

1. **Search docs first** — Use Laravel Boost `search-docs` for Laravel, Inertia, Pest, etc. before implementing.
2. **Check existing code** — Inspect sibling files for structure, naming, and reusable components.
3. **Use Artisan** — `php artisan make:*` with `--no-interaction` for new files.

## Key Conventions

- **PHP:** Constructor promotion, explicit return types, Form Requests (never inline validation), Eloquent over `DB::`, `config()` not `env()`.
- **Frontend:** Vue 3 Composition API + TypeScript, Inertia v2 (deferred props, polling, prefetching).
- **Testing:** Pest syntax, model factories, datasets for validation tests.
- **Enums:** TitleCase keys (e.g. `FavoritePerson`, `Monthly`).

## Commands

```bash
composer run dev          # Full dev stack
php artisan test         # Run tests (use --filter for targeted runs)
vendor/bin/pint --dirty  # Format changed PHP files (run before finalizing)
npm run lint && npm run format  # Lint/format frontend
```

## Before Finalizing

- [ ] Run `vendor/bin/pint --dirty`
- [ ] Run affected tests (`php artisan test --filter=...`)
- [ ] No new docs unless explicitly requested

## Tools

- **Laravel Boost MCP** — Use `search-docs`, `tinker`, `database-query`, `list-artisan-commands`, `get-absolute-url`, `browser-logs` when available.
- **Frontend not updating?** — Suggest `npm run build`, `npm run dev`, or `composer run dev`.
