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

### Public Site and Music

- `.claude/rules/site.md`: routing, layout, UI text, `wire:navigate`, theme
  and motion.
- `.claude/rules/portfolio.md`: projects, the snapshot of the Next site,
  the page blocks, seeding, the player-bar rule and the hero/case motions.
- `.claude/rules/music.md`: releases and tracks, the disks, the music routes
  and the player's events, which the shop listens to.
- `.claude/rules/shop.md`: cart, checkout, Stripe webhook, login links,
  downloads, settings and local Stripe testing.

### Checking in the Browser

Run behaviour checks with `agent-browser` against Valet:
`http://sjerencms.test` (this app) and `http://sjeren-2026.test` (the old
Next site, the visual reference). Local pages and projects come from
`php artisan db:seed --class=SitePagesSeeder`. It runs `ProjectSeeder`,
adds missing pages and fills pages that are empty or still hold the
phase-2 colophon. It never overwrites admin edits. Pair
every screenshot with an `eval` value, give `screenshot` an absolute path
outside the repo, and run `git status` afterwards.
Music: `php artisan db:seed --class=MusicSeeder` gives releases without
audio, so nothing plays until a preview is attached. Media URLs come from
`APP_URL`, which must be `http://sjerencms.test` locally.

### Directory Structure Highlights

```
app/
  Filament/
    Forms/Components/Blocks/   # Page builder blocks
    Resources/Pages/           # PageResource (Filament v4+ structure)
    Pages/ManageSettings.php   # Site settings
  Models/                      # Page, Project, Release, Track, Order, OrderItem, User
  Settings/GeneralSettings.php
docs/
  superpowers/                 # spec and plans
  design/musik/                # music shop artboards (*.dc.html)
database/seeders/data/         # music.json and covers; portfolio/ (Next snapshot)
```

### Adding a New Block

1. Create `app/Filament/Forms/Components/Blocks/YourBlock.php`
2. Register it in `ContentBlocksSection.php`
3. Add `resources/views/blocks/your_block.blade.php`; it receives `$data`
4. Add it with its fields to `everyBlock()` in
   `tests/Feature/Admin/PageBlocksTest.php`
   (its registration test fails if you skip step 2)

## graphify

This project has a graphify knowledge graph at graphify-out/.

Rules:
- Before answering architecture or codebase questions, read graphify-out/GRAPH_REPORT.md for god nodes and community structure
- If graphify-out/wiki/index.md exists, navigate it instead of reading raw files
- After modifying code files in this session, run `graphify update .` to keep the graph current (AST-only, no API cost)
