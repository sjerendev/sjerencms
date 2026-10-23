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
- **Public site:** Blade pages under `/se/` and `/en/`, see below.
- **Spatie packages:** laravel-medialibrary, laravel-settings
  (`app/Settings/GeneralSettings.php`, migrations in `database/settings/`),
  laravel-sitemap.

### Block-Based Page Builder

- Filament blocks in `app/Filament/Forms/Components/Blocks/`, registered in
  `ContentBlocksSection.php`. Block `make()` takes `?string $name`.
- Blocks are stored as JSON in `pages.content`. A block gets a view in
  `resources/views/blocks/{type}.blade.php` when it gets the new design;
  `pages/show.blade.php` skips types without a view. The colophon was first.

### Multi-Language Content

- Pages have a `language` field (`sv` or `en`); `slug` is unique per
  language.
- Model scopes: `->swedish()`, `->english()`, `->language($lang)`.
- Public URLs use `/se/` and `/en/` prefixes (see the spec).

### Public Site

- `routes/web.php`: the old-site redirects (`/` → `/se`, `/en/about-me` and
  friends), then `{locale}` (`se|en`) with `page.home` and `page.show`.
  `SetLocaleFromPrefix` maps `se` → `sv` for the app locale and
  `Page::$language`; admin stays English. URLs have no trailing slash, and
  the old slash form still resolves.
- `ShowPage` finds the published page, its title and description (falling
  back to `lang/*/meta.php`) and hreflang alternates for the languages where
  it exists.
- UI text lives in `lang/{sv,en}/*.php`, copied 1:1 from sjeren-2026
  `messages/*.json`. Never invent copy; missing text goes in
  `TODO-INNEHALL.md`.
- Layout: `components/layouts/site.blade.php` with `site-header`,
  `site-footer`, `section-head`, `wordmark`, `wave-underline`,
  `theme-toggle` and `locale-switch`. Classes and SVG data are ported from
  sjeren-2026's components. Footer and header links come from
  `GeneralSettings`.
- Internal links use `wire:navigate`; the layout loads `@livewireScripts` and
  Vite tags carry `data-navigate-track`.
- JS has one entry, `resources/js/app.js`: `theme.js` (light/dark, set in
  `<head>` before first paint and again on swap) and `motion.js`.
  `defineMotion(name, init)` starts on every `livewire:navigated` for each
  `[data-motion=name]`; the cleanup `init` returns runs on
  `livewire:navigating`. `<html data-motion-active>` counts what runs.

### Checking in the Browser

Run behaviour checks with `agent-browser` against Valet:
`http://sjerencms.test` (this app) and `http://sjeren-2026.test` (the old
Next site, the visual reference). Local pages come from
`php artisan db:seed --class=SitePagesSeeder` (adds or updates only). Pair
every screenshot with an `eval` value, give `screenshot` an absolute path
outside the repo, and run `git status` afterwards.

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
3. Add `resources/views/blocks/your_block.blade.php`; it receives `$data`

## graphify

This project has a graphify knowledge graph at graphify-out/.

Rules:
- Before answering architecture or codebase questions, read graphify-out/GRAPH_REPORT.md for god nodes and community structure
- If graphify-out/wiki/index.md exists, navigate it instead of reading raw files
- After modifying code files in this session, run `graphify update .` to keep the graph current (AST-only, no API cost)
