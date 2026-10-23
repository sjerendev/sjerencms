# Music

- `Release` (alias as plain text in `artist`, `min_price` in whole kronor,
  `is_published`) has many `Track` (`position`, `duration_seconds`,
  `min_price`, default 20). Scopes `published()` and `newest()`;
  `Release::aliases()` builds the filter from distinct `artist` values.
- Disks: covers (`cover`) and MP3 previews (`preview`) on `public`; WAV
  masters (`wav`) on `music`, `storage/app/music`, private, no URL. A WAV
  must never get a public URL — downloads come in phase 4 through an
  authorised route.
- `MusicSeeder` loads the 22 Bandcamp releases from
  `database/seeders/data/music.json`, with names from `music-names.json` and
  covers from `covers/`, no audio. Idempotent:
  `php artisan db:seed --class=MusicSeeder`.
- Admin: `ReleaseResource` (ids in URLs, the public site routes on slug) with
  `TracksRelationManager` for tracks, preview and WAV.
- Uploads up to 1 GB (`config/livewire.php`, `maxSize` on the fields) and
  saved as a stream, not read into memory. The host's PHP
  `upload_max_filesize` and `post_max_size` must allow the same: locally
  they are 20M/25M, so a real WAV only goes up once they are raised.
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
  plays; `<html data-player="on">` then pads the footer.
- `Release::playlist()` is the single source of what can play: only tracks
  with a preview, null when none. Every play control is rendered only from
  it, and `<x-play-data :releases>` writes the same data as
  `<script type="application/json" data-music>`.
- Controls read by `resources/js/player.js`: `[data-play-release=<slug>]`
  (queue from the first track, toggles while that release plays),
  `[data-play-track=<id>][data-release=<slug>]`, and
  `[data-scrub=<slug>] [data-scrub-track=<id>]` (starts at the clicked
  fraction). A click copies the queue; `ended` moves to the next track and
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
