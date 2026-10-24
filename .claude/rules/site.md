# Public Site

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
- Motions in `resources/js/motions/`: `colophon`, `hero-rotator`,
  `hero-waves` and `case-pin` (the last three are in `portfolio.md`).
  `register.js` is not a motion: it is one `mousemove` listener on
  `document` for the register's hover image, so there is nothing to clean
  up.
