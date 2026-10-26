# Music

- `Release` (alias as plain text in `artist`, `min_price` in whole kronor,
  `is_published`) has many `Track` (`position`, `duration_seconds`,
  `min_price`, default 20). Scopes `published()` and `newest()`;
  `Release::aliases()` builds the filter from distinct `artist` values.
- Disks: covers (`cover`) and MP3 previews (`preview`) on `public`; WAV
  masters (`wav`) on `music`, `storage/app/music`, private, no URL. A WAV
  must never get a public URL — downloads go through the authorised
  `download` route (see `shop.md`).
- `MusicSeeder` loads the 22 Bandcamp releases from
  `database/seeders/data/music.json`, with names from `music-names.json` and
  covers from `covers/`, no audio. Idempotent:
  `php artisan db:seed --class=MusicSeeder`.
- Admin: `ReleaseResource` (slug in URLs, as on the public site: Filament
  builds its links from the model's route key, so an id-only admin 404s;
  saving a new slug redirects to the new address) with
  `TracksRelationManager` for tracks, preview and WAV.
- Uploads up to 1 GB (`config/livewire.php`, `maxSize` on the fields,
  `max_file_size` in `config/media-library.php`: medialibrary's own default
  of 10 MB throws `FileIsTooBig` otherwise) and
  saved as a stream, not read into memory. The host's PHP
  `upload_max_filesize` and `post_max_size` must allow the same. Locally
  three limits apply: PHP (`php-memory-limits.ini` in the PHP `conf.d`,
  512M on the dev Mac), Valet's nginx `client_max_body_size` in
  `/usr/local/etc/nginx/valet/valet.conf` (1024M after a manual edit;
  `valet install` and `valet upgrade` reset it to 128M, which shows up as
  a 413) and the 1 GB above. Check the host's values before a big upload.
  Upload one WAV per track: zipping saves only ~8% and the field accepts
  only WAV mime types.
- Deleting a release deletes its tracks through Eloquent, so medialibrary
  removes their preview and WAV files (the foreign key alone would not).
- Routes in the `{locale}` group, before `{slug}`: `music.index`
  (`/se/musik`, `?alias=<slug>`, unknown alias shows everything) and
  `music.show` (`/se/musik/{release:slug}`, 404 for drafts).
- Text in `lang/{sv,en}/music.php`, Swedish from the artboards in
  `docs/design/musik/`; English sentences are `[EN]` placeholders (see
  `TODO-INNEHALL.md`).

## The player

- `<x-player-bar>` sits in the layout inside `@persist('player')`, so its one
  `<audio>` keeps playing across `wire:navigate`. Hidden until something
  plays; `<html data-player="on">` then pads the footer. It stays on every
  page until `[data-player-close]` (`<x-player-close>`, in the bar and the
  mini player) stops the audio, empties the queue, hides the bar and drops
  `data-player`; `player:state` then has `release: null`.
- `Release::playlist()` is the single source of what can play: only tracks
  with a preview, null when none. Every play control is rendered only from
  it, and `<x-play-data :releases>` writes the same data as
  `<script type="application/json" data-music>`.
- Controls read by `resources/js/player.js`: `[data-play-release=<slug>]`
  (queue from the first track, toggles while that release plays),
  `[data-play-track=<id>][data-release=<slug>]`, and
  `[data-scrub-track=<id>]`. Those segments are not on the release page:
  `player.js` builds them in the bar's `[data-player-timeline]` (desktop),
  one per track in the queue, width = duration, title above, from the
  queue, not from the page. A click starts the track at the clicked
  fraction. A click copies the queue; `ended` moves to the next track and
  stops after the last.
- Events on `document`:
  - `player:state` `{release, track, playing, time, duration}`
  - `cart:add` `{type: 'release'|'track', id, amount}` from any
    `[data-cart-add]`; the album form sends the amount typed in
    `[data-amount-input]`. `resources/js/cart.js` forwards it to the
    `CartDrawer` Livewire component, and the server clamps the amount to
    `min_price`..`Cart::MAX_AMOUNT`.
  - Buy controls (`[data-cart-add]`) are rendered only when
    `isPurchasable()` (a WAV on the track, on every track for an album); the
    bar's button is hidden by player.js when the playing track's `buyable`
    is false.
