# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

sjerencms is being rebuilt into the whole of sjeren.se (portfolio plus a
music shop). Read the spec first:
`docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`. Plans live
in `docs/superpowers/plans/`.

## Build & Development Commands

```bash
# Full development environment (Laravel + Vite + Queue + Logs)
composer dev

# Individual services
php artisan serve          # Laravel only (port 8000)
npm run dev                # Vite dev server

# Build for production (static assets only, no Node process in production)
npm run build

# Testing (sqlite :memory:, see below)
php artisan config:clear   # run on its own first if config was ever cached
./vendor/bin/pest                                   # all tests
./vendor/bin/pest tests/Feature/Admin               # admin smoke tests
./vendor/bin/pest --filter="test name"              # one test

# Code style
./vendor/bin/pint
```

### Tests never touch a real database

`phpunit.xml` runs the tests on `sqlite` `:memory:` with `RefreshDatabase`.
A cached config (`bootstrap/cache/config.php`) overrides `phpunit.xml` and
would point `RefreshDatabase` at the local MySQL database, so
`tests/TestCase.php` refuses to start on anything but sqlite `:memory:`.
Never run `migrate:fresh`, `migrate:refresh`, `migrate:reset` or `db:wipe`.

## Architecture Overview

Laravel 13, Filament 5, Livewire 4 and Tailwind CSS 4 on PHP 8.5. Hosted on
a shared host (hostup): no Node process, no long-running queue worker.

- **Admin:** Filament panel at `/admin`, theme in
  `resources/css/filament/admin/theme.css` (Tailwind 4, `@source`, built by
  `@tailwindcss/vite`).
- **Public site:** not built yet. `routes/web.php` is empty and every public
  URL answers 404 until phase 2, which renders pages with Blade and
  Livewire.
- **Spatie packages:** laravel-medialibrary, laravel-settings
  (`app/Settings/GeneralSettings.php`, migrations in `database/settings/`),
  laravel-sitemap.

### Block-Based Page Builder

- Filament blocks in `app/Filament/Forms/Components/Blocks/`, registered in
  `ContentBlocksSection.php`. Block `make()` takes `?string $name`.
- Blocks are stored as JSON in `pages.content`. Phase 2 adds a Blade view per
  block; until then they only exist in the admin.

### Multi-Language Content

- Pages have a `language` field (`sv` or `en`); `slug` is unique per
  language.
- Model scopes: `->swedish()`, `->english()`, `->language($lang)`.
- Public URLs use `/se/` and `/en/` prefixes (see the spec).

### Directory Structure Highlights

```
app/
  Filament/
    Forms/Components/Blocks/   # Page builder blocks
    Resources/Pages/           # PageResource (Filament v4+ structure)
    Pages/ManageSettings.php   # Site settings
  Models/                      # Page, User
  Settings/GeneralSettings.php
docs/
  superpowers/                 # spec and plans
  design/musik/                # music shop artboards (*.dc.html)
database/seeders/data/         # music.json and covers for the shop seed
```

### Adding a New Block

1. Create `app/Filament/Forms/Components/Blocks/YourBlock.php`
2. Register it in `ContentBlocksSection.php`
3. From phase 2: add its Blade view

## graphify

This project has a graphify knowledge graph at graphify-out/.

Rules:
- Before answering architecture or codebase questions, read graphify-out/GRAPH_REPORT.md for god nodes and community structure
- If graphify-out/wiki/index.md exists, navigate it instead of reading raw files
- After modifying code files in this session, run `graphify update .` to keep the graph current (AST-only, no API cost)
