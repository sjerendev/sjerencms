# Portfolio

The start page, /om-mig and /projekt, ported 1:1 from sjeren-2026 (Next).
Classes, sizes and SVG come from its `src/components/` and
`src/app/[locale]/*/page.tsx`; read them instead of guessing a size.
WeUnite is mentioned in the timeline but never illustrated: no project, no
image.

## Projects

- `Project`: `slug`, `client`, `headline_sv`/`headline_en`,
  `role_sv`/`role_en` (nullable), `services` (array), `tech` (nullable),
  `url`, `rebuilt`, `position`. No published flag: a new project shows on
  /projekt at once. Admin: `ProjectResource`.
- Media on `public`, one file each: `image` (case crop and mobile thumbnail),
  `preview` (450×526, the register's hover image) and `tall` (full-page
  screenshot that drives the pinned case).
- Fallbacks:
  - `role($lang)` is the set role, else derived from `services`: any
    service matching `/design/i` gives `projects.roleDesignDev`, otherwise
    `projects.roleDev`.
  - `tech()` is `tech`, else the services joined with ` · `.
  - `imageUrl()`/`previewUrl()` return null, never `''`, so no
    `<img src="">`.
  - `tall()` returns `{src, width, height}` read with `getimagesize()` on
    every call, or null. ScrollTrigger needs the size. It only works on a
    local disk; on R2 the size must go into custom properties on upload
    (`ponytail:` comment).
- `Project::chosen($ids)` keeps the given order and skips deleted ids;
  `[]` means every project by `position`, then `id`.
- `Project::withTotal($text)` replaces `{total}` with the project count. It
  is used in the `aside` of `featured_cases` and `register`, the register's
  `all_label` and the `page_hero` text.
- Labels in `lang/{sv,en}/projects.php`: `role`, `tech`, `site`, `rebuilt`,
  `roleDesignDev`, `roleDev`. A `rebuilt` project is a `div` without a link
  in the register and shows `rebuilt` instead of the link in the meta row
  (`components/project-meta`).

## The snapshot

`database/seeders/data/portfolio/` holds `projects.json`, `about.json`,
`messages-se.json`, `messages-en.json` and `images/`. They were exported
from sjeren-2026's working tree on 2026-10-24, uncommitted edits included.
Nothing reads sjeren-2026 at runtime, and nothing ever writes there. To redo
the export, use the node command in the `ProjectSeeder` docblock (run from
`~/Code/sjeren-2026`), then copy `messages/{se,en}.json` over.

## Seeding

- `php artisan db:seed --class=ProjectSeeder`: `firstOrCreate` on slug, so
  an existing project (headlines rewritten in the admin) is left alone. An
  image is added only when its collection is empty. Run directly, it
  therefore re-creates a snapshot project deleted in the admin and re-adds
  an image removed there.
- `php artisan db:seed --class=SitePagesSeeder` calls `ProjectSeeder` only
  when the projects table is empty, then builds start, /om-mig and /projekt
  in `sv` and `en`. A page gets the blocks when it is created, or when its
  `content` is `[]` (also a page emptied on purpose in the admin) or equal
  (`==`, key order ignored) to the phase-2 colophon. Any other content
  counts as an admin edit and is never touched. Content is built only for a
  page that gets filled, so a re-run after a project is deleted is a no-op;
  filling a page that needs the deleted project throws.
- Copy comes from the snapshot through `t()`, which throws on a missing
  message. `picks()` turns slugs into picker items and throws on an unknown
  slug. `|` in a message becomes a line break (one item per line).
- `featured_cases` gets the three `featured` slugs. The start register gets
  the first four that are not featured, as `registerPreview` in
  `projects.ts`. The /projekt register gets an empty list, so all projects.

## Blocks

Each block has a view in `resources/views/blocks/{type}.blade.php`.
Section numbers (`01`, `02` …) are a field, not counted from the order.

| Type | Page | Fields |
|---|---|---|
| `hero` | start | `lead`, `phrases` (one per line), `intro_one`, `intro_two`, `work_label` (links to /projekt in the page's language), `cv_label`, `cv_file` (required PDF upload per page, so per language: `public` disk, `cv/`, random name; the original in `cv_file_name` is the link's `download` name; the seeder copies the old PDFs from `public/files`) |
| `featured_cases` | start | `number`, `label`, `aside`, `projects` |
| `register` | start, /projekt | `number`, `label`, `aside`, `projects`, `all_label` (empty: a plain closing line) |
| `service_cards` | start | `number`, `label`, `columns[title, items]` (items one per line) |
| `headline_text` | start | `number`, `label`, `headline`, `text`, `cards[title, body]` |
| `colophon` | start | `label`, `tools` (separated by `·`) |
| `page_hero` | /om-mig, /projekt | `lines` (one row per line), `text`, `size` (`large` om mig, `medium` projekt) |
| `profile` | /om-mig | `number`, `label`, `column_one`, `column_two` (paragraphs split by a blank line; the first of column one is larger), `stats[value, label]` |
| `timeline` | /om-mig | `number`, `label`, `work_label`, `sound_label`, `entries[years, title, subtitle, body, tags, track]` (`track` is `work` or `sound`) |
| `stack` | /om-mig | `number`, `label`, `groups[label, items]` |
| `faq_accordion` | /om-mig | `number`, `label`, `items[question, answer, note]` |

- `projects` is the `ProjectPicker` repeater, `[['project_id' => id], …]`, in
  order.
  - In `register`, an empty list means every project.
  - In `featured_cases`, an empty list renders nothing. Nineteen pinned
    cases would be wrong on any page.
  - The case counter `01 / 0N` counts the cases rendered, so a deleted
    project shortens it.
  - The picker drops a pick of a deleted project when the form loads, so
    the page still saves (the Select's `in` rule would reject it). The
    stored content keeps the stale id until that page is saved.
- `stack.items` is one string shown as typed (`Figma · Penpot`), not one
  per line. A list would mean inventing a separator.
- A new or changed block also goes into `everyBlock()` in
  `tests/Feature/Admin/PageBlocksTest.php` with its fields. That file's
  registration test fails when a type there is missing from
  `ContentBlocksSection`. Saving keeps an unknown block's data, so only the
  form can tell.

## Spacing on the start page

`pages/show.blade.php` puts `data-home` on its wrapper when the page is the
homepage. The register is the only block whose top spacing differs
between its pages:
`pt-[clamp(56px,8vw,90px)] [[data-home]_&]:pt-[clamp(72px,10vw,116px)]`
gives 116/72px on start and 90/56px on /projekt (1440/390 wide).
`page_hero` has the same spacing on /om-mig and /projekt, and its `size`
field handles the difference. Every other block lives on one page and
carries that page's size.

## The player bar and the pinned case

Next had no player bar. Here it would cover the pinned case's meta row, so
`html[data-player="on"] [data-motion="case-pin"]` is `100svh` minus the bar:
74px below `lg` (mini player) and 80px from `lg`. The rule sits in
`app.css` **outside the layers**, or the `h-svh` utility wins. `case-pin`
observes `data-player` and runs `ScrollTrigger.refresh()` when its value
changes.

## Motions

Each one ports the Next component named in its header comment. The three
`defineMotion`s stand still under `prefers-reduced-motion: reduce` and clean
up on `livewire:navigating` (see `site.md`).

- `hero-rotator` (`motions/hero-rotator.js`): the server renders phrase 0
  (`[data-out]`) and phrase 1 (`[data-in]`, below the edge). The phrase is
  read from the `h1 .sr-only` label. It rolls one GSAP timeline per lap,
  about 3 s each. It starts from the phrase on show, which covers a
  back-button snapshot. Cleanup kills the current timeline, because each lap
  creates it outside the `matchMedia` context, then `mm.revert()`. Reduced
  motion: phrase 0 stands still.
- `hero-waves` (`motions/hero-waves.js`): a canvas, with colours from
  `--accent`, `--ink` and `--wave-mid`. A `data-theme` observer re-reads
  them and redraws the still frame. Cleanup cancels the frame and removes
  the pointer listener, the `ResizeObserver` and the theme observer.
  Reduced motion: one still frame.
- `case-pin` (`motions/case-pin.js`): only on a case with a `tall` image
  (`h-svh`). It pins the whole case and scrolls the image inside its frame,
  at most `1.8 ×` the window height. There is no pin when the image fits
  the frame. Cleanup disconnects the `data-player` observer and kills the
  trigger, which removes its `.pin-spacer`. Reduced motion: no pin.
- `register.js` is not a motion. One `mousemove` listener on `document`
  sets `--mx`/`--my` on the hovered `[data-register-row]`, so there is
  nothing to clean up. The tilt itself is CSS (`group-hover`); under
  reduced motion the global rule in `app.css` cuts the transition to
  0.01ms, so the image appears without moving in.

`<html data-motion-active>` counts initialised `[data-motion]` elements.
On start that is 6: rotator, waves, three cases and the colophon. It is 0
on /om-mig and /projekt, and also 6 under reduced motion, because the
inits still return a cleanup.

## Copy

Copy moves unchanged from sjeren-2026. Drafts and invented text are listed
in `TODO-INNEHALL.md`.
