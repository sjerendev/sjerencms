# Fas 3: musik, släppsida och spelare, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** Musikfliken finns på `/se/musik` och `/en/musik`:
- alla släpp i ett rutnät med filter per alias,
- en sida per släpp,
- en spelare i en list längst ned som fortsätter spela när man byter sida.

Patrik lägger in släpp, spår och ljudfiler i Filament.

**Arkitektur:**
- `Release` och `Track` är Eloquent-modeller med medialibrary. Omslag och
  MP3-förlyssningar ligger på disken `public`, WAV på den privata disken
  `music`.
- Listan och släppsidan är vanliga Blade-routes i språkgruppen.
- Spelaren är en `@persist('player')`-list i layouten och styrs av
  `resources/js/player.js`. Varje sida bäddar in sin spellista som JSON.
- Köpknapparna skickar händelsen `cart:add`, som korgen i fas 4 tar emot.

**Tech stack:** Laravel 13, Filament 5 med spatie-laravel-media-library-plugin,
Livewire 4 (`@persist`, `wire:navigate`), Tailwind 4, vanlig JS med
`HTMLAudioElement`, Pest 4 och agent-browser.

**Spec:** `docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`.
Läs avsnitten "Fas 3" och "Datamodell för musik och butik", Begränsningar
(WAV utanför webbroten, copy) och beslut 1, 4 och 8.

## Avvikelser och beslut (Patrik kontrollerar)

1. **"Köp spåret" lägger ännu inget i en korg**, eftersom korgen byggs först
   i fas 4. I fas 3 skickar alla köpknappar
   `cart:add {type, id, amount}`, och fas 4 kopplar korgen till händelsen.
2. **Musiklistan är en egen route (`/se/musik`) och inget block.** Specen
   säger att listan *bör* kunna läggas som block. Det kan göras senare utan
   att något byggs om.
3. **Musiktexterna kommer från ritningarna**, eftersom
   `messages/*.json` inte har några. På engelska gäller två regler:
   - Korta funktionsetiketter översätts rakt, till exempel "Köp" →
     "Buy" och "Spår" → "Tracks".
   - Rubriken och meningarna blir en tydlig platshållare,
     `[EN] <svensk text>`.

   Allt detta skrivs upp i `TODO-INNEHALL.md`.
4. **Priser lagras i hela kronor (int).** Stripe räknar i ören, så fas 4
   multiplicerar med 100.
5. **Alias och titlar i seedern kommer från listritningen**, alltså
   `Omslag.dc.html`, och inte från Bandcamps rådata. Rådatan har
   nollbreddsmellanslag och prefix som "S.D.L. - ". Tabellen finns i
   uppgift 1.
6. **Ljudtokens:** ritningarna har tokens som `app.css` saknar, och de läggs
   till. Ritningens `#0F1413` blir sajtens `--ink`/`--plate` (`#111820`).

## Globala begränsningar

- Samma som i fas 2:
  - Kör aldrig `migrate:fresh`, `migrate:refresh`, `migrate:reset`,
    `db:wipe` eller `db:seed --force`.
  - `php artisan migrate`, `db:seed --class=MusicSeeder` och
    `storage:link` mot den lokala databasen är tillåtna.
  - Kör `php artisan config:clear` som eget kommando före den första
    testkörningen.
  - Kör `npm run build` före testerna.
  - Filer tas bort med `git rm <fil>`.
  - Commit görs på `main` med uttryckliga sökvägar. Pusha inte.
- **WAV ligger aldrig på `public` och får aldrig en publik URL** (spec,
  Begränsningar). Disken `music` har sin root i `storage/app/music`.
- **Ingen påhittad copy.** Text som inte står i ritningarna eller i
  `music.json` är platshållare och skrivs upp i `TODO-INNEHALL.md`.
  Beskrivningen av ett släpp är tom tills Patrik skriver den, och tom text
  renderas inte.
- **Ritningar:**
  - `docs/design/musik/Omslag.dc.html`, `Slapp.dc.html` och
    `SlappMobil.dc.html`. De mörka varianterna är samma markup med mörka
    tokens.
  - Mått, avstånd och storlekar kopieras från ritningarna. Headern i
    ritningarna med "Logga in" och "Korg" byggs i fas 4. Här tillkommer
    bara "Musik".
- **Kontroller i webbläsaren:**
  - Kör `agent-browser` mot `http://sjerencms.test`, med en kontroll per
    anrop och ett `eval`-värde till varje skärmbild.
  - Skärmbilder sparas i
    `.superpowers/sdd/2026-10-23-fas-3-musik-och-spelare/evidence/`.
- Kod och tester skrivs på engelska.

## Granskningsfokus

1. **Ett spår utan förlyssning, eller ett släpp där inget spår kan spelas.**
   Då finns ingen spelknapp, listen öppnas aldrig och inget JS-fel uppstår.
   Det är normalläget efter seedern. Testas i uppgift 5.
2. **Ett opublicerat släpp** ska ge 404 på sin adress. Det ska inte synas i
   listan, under "Mer från" eller i aliasens antal. Testas i uppgift 3 och 4.
3. **Ett okänt eller gammalt aliasfilter** (`?alias=finns-inte`) visar alla
   släpp med "Alla" markerat. Det ger varken 404 eller en tom sida. Testas
   i uppgift 3.
4. **WAV-filen kan nås publikt.** Den ska ligga på disken `music` utanför
   `public`. Testas i uppgift 1 och 2.
5. **Spelaren över sidbyten och köer:**
   - Ljudet fortsätter, och det är samma `<audio>`, efter
     `wire:navigate`.
   - Ett nytt släpp ersätter kön, och ett spår som tar slut går vidare
     till nästa.
   - När sista spåret tagit slut stannar spelaren.
   - En vanlig omladdning ger en stängd list och inga fel.

   Testas i webbläsaren i uppgift 5.

---

### Uppgift 1: Modellerna, disken och seedern

**Files:**
- Create: `database/migrations/2026_10_25_000001_create_releases_table.php` och `2026_10_25_000002_create_tracks_table.php`
- Create: `app/Models/Release.php` och `app/Models/Track.php`
- Create: `database/factories/ReleaseFactory.php` och `TrackFactory.php`
- Create: `database/seeders/MusicSeeder.php` och `database/seeders/data/music-names.json`
- Modify: `config/filesystems.php` (disken `music`)
- Test: `tests/Feature/Music/ReleaseModelTest.php` och `tests/Feature/Music/MusicSeederTest.php`

**Interfaces:**
- Produces:
  - `releases`: `id`, `slug` (unique), `artist` (alias), `title`,
    `released_on` (date), `description` (text, null), `min_price`
    (unsigned int, kronor), `is_published` (bool, false) och timestamps.
  - `tracks`: `id`, `release_id` (FK, cascade), `position` (unsigned
    small int), `title`, `duration_seconds` (unsigned int), `min_price`
    (unsigned int, standard 20) och timestamps. `unique(release_id, position)`.
  - `Release`:
    - Implementerar `HasMedia` och har collection `cover` (singleFile, disk
      `public`).
    - `tracks()` sorteras på `position`. `getRouteKeyName()` är `slug`.
    - Scopes: `published()` och `newest()`, som sorterar på
      `released_on` desc och sedan `id` desc.
    - Metoder: `totalSeconds(): int`, `minutes(): int` (heltalsdivision),
      `aliasSlug(): string` (`Str::slug($this->artist)`), `coverUrl():
      ?string` och `static aliases(): Collection`. `aliases()` ger
      `['slug' => …, 'name' => …, 'count' => …]` för publicerade släpp,
      sorterat på antal desc och sedan namn.
  - `Track`:
    - Implementerar `HasMedia` med collection `preview` (singleFile, disk
      `public`) och `wav` (singleFile, disk `music`).
    - Metoder: `clock(): string`, som ger `m:ss` och `h:mm:ss` från 3600
      sekunder, och `previewUrl(): ?string`.
  - Disken `music`: `local`, root `storage_path('app/music')`,
    `visibility` `private` och ingen `url`.

- [ ] **Steg 1: Skriv testerna.**

```php
// ReleaseModelTest.php
it('sums durations and formats clocks', function () {
    $release = Release::factory()->create();
    Track::factory()->for($release)->create(['position' => 1, 'duration_seconds' => 524]);
    Track::factory()->for($release)->create(['position' => 2, 'duration_seconds' => 1440]);

    expect($release->totalSeconds())->toBe(1964)->and($release->minutes())->toBe(32)
        ->and($release->tracks[0]->clock())->toBe('8:44')
        ->and(Track::factory()->make(['duration_seconds' => 3725])->clock())->toBe('1:02:05');
});

it('defaults a track to 20 kr and orders tracks by position', function () {
    $release = Release::factory()->create();
    $release->tracks()->create(['position' => 2, 'title' => 'B', 'duration_seconds' => 60]);
    $release->tracks()->create(['position' => 1, 'title' => 'A', 'duration_seconds' => 60]);

    expect($release->tracks->pluck('title')->all())->toBe(['A', 'B'])
        ->and($release->tracks[0]->fresh()->min_price)->toBe(20);
});

it('lists aliases of published releases with counts', function () {
    Release::factory()->count(2)->create(['artist' => 'S.D.L.', 'is_published' => true]);
    Release::factory()->create(['artist' => 'Monokii', 'is_published' => true]);
    Release::factory()->create(['artist' => 'Hemlig', 'is_published' => false]);

    expect(Release::aliases()->all())->toBe([
        ['slug' => 'sdl', 'name' => 'S.D.L.', 'count' => 2],
        ['slug' => 'monokii', 'name' => 'Monokii', 'count' => 1],
    ]);
});

it('keeps the wav on the private music disk', function () {
    Storage::fake('music');
    $track = Track::factory()->create();
    $track->addMedia(UploadedFile::fake()->create('master.wav', 10, 'audio/wav'))->toMediaCollection('wav');

    expect($track->getFirstMedia('wav')->disk)->toBe('music')
        ->and(config('filesystems.disks.music.root'))->toBe(storage_path('app/music'))
        ->and(config('filesystems.disks.music'))->not->toHaveKey('url');
});
```

```php
// MusicSeederTest.php
beforeEach(fn () => Storage::fake('public'));

it('seeds all 22 releases with clean names, covers and tracks, idempotently', function () {
    $this->seed(MusicSeeder::class);
    $this->seed(MusicSeeder::class);

    expect(Release::count())->toBe(22)->and(Track::count())->toBe(122)
        ->and(Release::published()->count())->toBe(22)
        ->and(Release::whereNotNull('id')->get()->every(fn ($r) => $r->getFirstMedia('cover') !== null))->toBeTrue()
        ->and(Release::where('artist', 'like', "%\u{200B}%")->exists())->toBeFalse();

    $voyages = Release::where('slug', 's-d-l-past-voyages')->sole();
    expect([$voyages->artist, $voyages->title, $voyages->min_price, $voyages->released_on->toDateString()])
        ->toBe(['S.D.L.', 'Past Voyages', 70, '2026-09-10'])
        ->and($voyages->tracks->pluck('title')->all())->toBe(['Big Dripper', 'Deep Down', 'Saience', 'The Worm'])
        ->and($voyages->tracks->pluck('min_price')->unique()->all())->toBe([20])
        ->and(Release::aliases()->firstWhere('name', 'S.D.L.')['count'])->toBe(6);
});
```

- [ ] **Steg 2: Kör.** `./vendor/bin/pest tests/Feature/Music`. Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **Migrationer och modeller** enligt Interfaces. Medialibrary behöver
    inga konverteringar, eftersom omslagen är 700 px.
  - **Factories:**
    - `ReleaseFactory` har en unik slug, `artist` från faker,
      `released_on` faker-datum, `min_price` 50 och `is_published` true.
    - `TrackFactory` har `release_id` via `Release::factory()`, `position`
      1, en titel och `duration_seconds` 300.
  - **`music-names.json`** har exakt detta innehåll. Det kommer från
    `Omslag.dc.html` och matchar `path` i `music.json`:

    ```json
    {
      "s-d-l-past-voyages": ["S.D.L.", "Past Voyages"],
      "s-d-l-vol-1": ["S.D.L.", "Vol. 1"],
      "s-d-l-vol-2-ascend-from-the-mind": ["S.D.L.", "Vol. 2: Ascend from the Mind"],
      "s-d-l-vol-3": ["S.D.L.", "Vol. 3"],
      "s-d-l-space-traveller-remixes": ["S.D.L.", "Space Traveller Remixes"],
      "s-d-l-03": ["S.D.L.", "03("],
      "trax-beyond-subconcious-ii": ["Trax Beyond Subconcious", "II"],
      "trax-beyond-subconcious-ambient-acid-cut-outs-1": ["Trax Beyond Subconcious", "Ambient Acid Cut Outs #1"],
      "trax-beyond-subconcious-haxx-ep": ["Trax Beyond Subconcious", "Haxx EP"],
      "disturbed-genitals-vol-1": ["Disturbed Genitals", "Vol. 1"],
      "disturbed-genitals-vol-2": ["Disturbed Genitals", "Vol. 2"],
      "acid-asylum-untitled": ["Acid Asylum", "Untitled"],
      "weird-genetics-voyage-ep": ["Weird Genetics", "Voyage EP"],
      "weird-genetics-voyage-pats-revisit-edit": ["Weird Genetics", "Voyage (pat's revisit edit)"],
      "analogue-sessions-1": ["Patrik Sjerén", "Analogue Sessions #1"],
      "patrik-sjeren-x-volume-1-uwr-blue": ["Patrik Sjerén", "X Volume 1 (UWR Blue)"],
      "patrik-sjeren-tracks-from-the-past": ["Patrik Sjerén", "Tracks from the Past"],
      "patrik-sjeren-clusters-1": ["Patrik Sjerén", "Clusters #1"],
      "from-the-vault": ["Patrik Sjerén", "From the Vault"],
      "dj-wise-bounced-ep-uwr": ["DJ Wise", "Bounced EP (UWR)"],
      "monokii-framtida-skuggor": ["Monokii", "Framtida Skuggor"],
      "the-early-years": ["Olika alias", "The Early Years"]
    }
    ```
  - **`MusicSeeder`:**
    - Läser `music.json` och `music-names.json`. `slug` är sista delen av
      `path`, och alias och titel kommer ur namntabellen.
    - `released_on` är `Carbon::parse($row['date'])->toDateString()`.
    - `min_price` är `(int) $row['min']` och `is_published` true.
    - Använder `updateOrCreate` på `slug`.
    - Omslaget läggs till med
      `addMedia(database_path("seeders/data/covers/{$slug}.jpg"))->preservingOriginal()`,
      men bara om `cover` saknas.
    - Spåren skrivs med `updateOrCreate` på `(release_id, position)`.
      Positionen är index + 1, `title` och `duration_seconds` kommer från
      `tracks`, och `min_price` är 20 (beslut 8).
    - Den signerade `mp3sample`-länken från Bandcamp används inte.
    - Docblocket anger `php artisan db:seed --class=MusicSeeder`.

- [ ] **Steg 4: Kör.** `./vendor/bin/pest`. Förväntat: allt grönt. Kör
  sedan lokalt, i ordning:
  1. `php artisan migrate`
  2. `php artisan storage:link` om `public/storage` saknas
  3. `php artisan db:seed --class=MusicSeeder`
  4. `php artisan tinker --execute 'echo App\Models\Release::count(), " ", App\Models\Track::count();'`

  Förväntat: `22 122`.

- [ ] **Steg 5: Commit:** `Add Release and Track models, music disk and seeder`.

### Uppgift 2: Släppen i Filament

**Files:**
- Create: `app/Filament/Resources/Releases/ReleaseResource.php`, `Pages/{ListReleases,CreateRelease,EditRelease}.php` och `RelationManagers/TracksRelationManager.php`
- Modify: `tests/Feature/Admin/ResourceIndexTest.php` (lägg till `/admin/releases`)
- Test: `tests/Feature/Admin/ReleaseResourceTest.php`

**Interfaces:**
- Consumes: modellerna från uppgift 1.
- Produces: `ReleaseResource` och `TracksRelationManager`, i samma stil som
  `Resources/Pages/PageResource.php`, med form och tabell i resursklassen.

- [ ] **Steg 1: Skriv testerna.**

```php
it('creates a release with a cover', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateRelease::class)
        ->fillForm([
            'title' => 'Past Voyages', 'slug' => 's-d-l-past-voyages', 'artist' => 'S.D.L.',
            'released_on' => '2026-09-10', 'min_price' => 70, 'is_published' => true,
            'cover' => [UploadedFile::fake()->image('cover.jpg', 700, 700)],
        ])
        ->call('create')->assertHasNoFormErrors();

    expect(Release::sole()->getFirstMedia('cover'))->not->toBeNull();
});

it('rejects a minimum price below zero and a duplicate slug', function () {
    $this->actingAs(User::factory()->create());
    Release::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateRelease::class)
        ->fillForm(['title' => 'X', 'slug' => 'taken', 'artist' => 'A', 'released_on' => '2026-01-01', 'min_price' => -1])
        ->call('create')->assertHasFormErrors(['slug' => 'unique', 'min_price']);
});

it('adds a track with preview and wav from the release page', function () {
    Storage::fake('public');
    Storage::fake('music');
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->create();

    Livewire::test(TracksRelationManager::class, ['ownerRecord' => $release, 'pageClass' => EditRelease::class])
        ->callAction(TestAction::make('create')->table(), [
            'position' => 1, 'title' => 'Big Dripper', 'duration_seconds' => 524, 'min_price' => 20,
            'preview' => [UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg')],
            'wav' => [UploadedFile::fake()->create('m.wav', 10, 'audio/wav')],
        ])->assertHasNoFormErrors();

    $track = $release->tracks()->sole();
    expect($track->getFirstMedia('preview')->disk)->toBe('public')
        ->and($track->getFirstMedia('wav')->disk)->toBe('music');
});

it('renders the edit page with the tracks table', function () {
    $this->actingAs(User::factory()->create());
    $release = Release::factory()->has(Track::factory(), 'tracks')->create();

    $this->get("/admin/releases/{$release->id}/edit")->assertOk()->assertSee($release->tracks[0]->title);
});
```

Fungerar inte `TestAction::make('create')->table()` i den här
Filament-versionen? Läs `vendor/filament/actions/src/Testing/` och använd
motsvarande hjälpare. Skriv upp det som en ruling.

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **Formuläret för `ReleaseResource`:**
    - `title` är obligatoriskt.
    - `slug` är obligatoriskt och `unique(ignoreRecord: true)`. Det fylls
      från titeln vid skapande, som i `BaseContentSection`.
    - `artist` heter "Alias" i formuläret, är obligatoriskt och har
      `->datalist(Release::aliases()->pluck('name'))`.
    - `released_on` är en obligatorisk `DatePicker`.
    - `min_price` är numeriskt, `minValue(0)`, obligatoriskt och har
      suffixet `kr`.
    - `description` är en `Textarea` och `is_published` en `Toggle`.
    - `cover` är en `SpatieMediaLibraryFileUpload`, collection `cover`,
      `image()`.
  - **Tabellen:** `SpatieMediaLibraryImageColumn` `cover`, `title`
    (sökbar), `artist`, `released_on` (sorterbar, standard desc), antal
    spår (`counts('tracks')`) och en `ToggleColumn` för `is_published`.
  - **`TracksRelationManager`:**
    - `position` är numeriskt och obligatoriskt, `title` obligatoriskt,
      `duration_seconds` numeriskt och obligatoriskt med hjälptexten
      "Seconds", och `min_price` numeriskt med standard 20.
    - `preview` är en `SpatieMediaLibraryFileUpload`, collection
      `preview`, `acceptedFileTypes(['audio/mpeg'])`.
    - `wav` är en `SpatieMediaLibraryFileUpload`, collection `wav`,
      `disk('music')`, `visibility('private')` och
      `acceptedFileTypes(['audio/wav', 'audio/x-wav', 'audio/wave'])`.
    - Tabellen sorteras på `position` och visar `position`, `title`,
      `clock()` och `min_price`.

- [ ] **Steg 4: Kör.** `./vendor/bin/pest`. Förväntat: allt grönt.
- [ ] **Steg 5: Commit:** `Add releases and tracks to the admin`.

### Uppgift 3: Musiklistan med aliasfilter och fliken i headern

**Files:**
- Create: `app/Http/Controllers/ShowMusic.php`, `resources/views/music/index.blade.php` och `resources/views/components/release-card.blade.php`
- Create: `lang/sv/music.php` och `lang/en/music.php`
- Modify: `routes/web.php`, `resources/views/components/site-header.blade.php`, `lang/*/nav.php` (`music`), `resources/css/app.css` (ljudtokens) och `TODO-INNEHALL.md`
- Test: `tests/Feature/Music/MusicIndexTest.php` och tillägg i `tests/Feature/Site/HeaderTest.php`

**Interfaces:**
- Consumes: `Release::published()`, `newest()` och `aliases()`, samt layouten och `SetLocaleFromPrefix`.
- Produces:
  - Routen `music.index`, `{locale}/musik`. Den registreras före
    `page.show` i gruppen, annars tar sidroutern `musik`.
  - `<x-release-card :release>` och `lang/*/music.php`, som uppgift 4
    och 5 använder.
  - Tailwind-färgerna `sound`, `bar`, `bar-sound`, `buy`, `panel` och
    `panel-line`.

- [ ] **Steg 1: Skriv testerna.**

```php
// MusicIndexTest.php
beforeEach(function () {
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Vol. 1', 'released_on' => '2019-09-18', 'min_price' => 50]);
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages', 'released_on' => '2026-09-10', 'min_price' => 70]);
    Release::factory()->create(['artist' => 'Monokii', 'title' => 'Framtida Skuggor', 'released_on' => '2016-08-11']);
    Release::factory()->create(['artist' => 'Hemlig', 'title' => 'Utkastet', 'is_published' => false]);
});

it('lists published releases newest first with price and year', function () {
    $html = $this->get('/se/musik')->assertOk()->assertDontSee('Utkastet')->getContent();

    expect(strpos($html, 'Past Voyages'))->toBeLessThan(strpos($html, 'Vol. 1'))
        ->and(strpos($html, 'Vol. 1'))->toBeLessThan(strpos($html, 'Framtida Skuggor'))
        ->and($html)->toContain('Köp · 70 kr')->toContain('2026 ·')
        ->toContain('Sju alias<br>och mitt<br>eget namn');
});

it('filters on alias and counts only published releases', function () {
    $this->get('/se/musik?alias=sdl')->assertSee('Past Voyages')->assertDontSee('Framtida Skuggor');
    expect($this->get('/se/musik')->getContent())
        ->toMatch('#Alla\s*</span>\s*<span[^>]*>\s*3#')
        ->not->toContain('Hemlig');
});

it('shows everything for an unknown alias', function () {
    $this->get('/se/musik?alias=finns-inte')->assertOk()->assertSee('Framtida Skuggor')->assertSee('Past Voyages');
});

it('speaks English on the English list', function () {
    $this->get('/en/musik')->assertOk()->assertSee('All releases')->assertSee('Buy · 70 kr')
        ->assertSee('<html lang="en"', false);
});
```

```php
// HeaderTest.php, nytt test
it('links Musik and marks the current section', function () {
    Release::factory()->create();
    expect($this->get('/se/musik')->getContent())
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/musik'), '#').'"[^>]*wire:navigate[^>]*aria-current="page"#');
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **Routen:** `Route::get('musik', ShowMusic::class)->name('music.index')`
    i språkgruppen, före `{slug}`.
  - **`ShowMusic::__invoke(Request $request, string $locale): View`:**
    - Hämtar `aliases()`. Är `?alias` en känd slug filtreras på
      `artist`, annars visas alla.
    - Släppen hämtas med `published()->newest()->withCount('tracks')`.
    - Titeln är `__('music.title')` och beskrivningen
      `__('meta.description')`.
    - `alternates` är båda språken.
  - **`music/index.blade.php`** är sidhuvudet och sektionen "01 Alla släpp"
    ur `Omslag.dc.html`:
    - Aliaslistan är en `<nav>` med länkar
      `route('music.index', ['alias' => …])` och `wire:navigate`. "Alla"
      är först.
    - Det aktiva aliaset har `font-semibold` och `aria-current="page"`.
    - Sektionshuvudet är `<x-section-head number="01" :label="__('music.all')" :aside="__('music.newest')" />`.
    - Rutnätet är `grid-cols-2 gap-x-[16px] gap-y-[32px] lg:grid-cols-4 lg:gap-x-[32px] lg:gap-y-[56px]`.
  - **`release-card.blade.php`** är ett kort ur ritningen:
    - Omslaget visas med `alt` = `__('music.coverAlt', ['title' => …])`.
    - Under det står alias, titeln som länk, och raden `år · antal spår`
      via `trans_choice('music.tracks', n)`.
    - Raden har spelknapp och köpknapp. Spelknappen kopplas i uppgift 5.
      Köpknappen är `<button type="button" data-cart-add="release"
      data-id data-amount>{{ __('music.buy') }} · {{ $min_price }} kr</button>`.
    - Länkar till släppet går till `route('music.show', …)`, som kommer i
      uppgift 4. Fram till dess är det `url("/{$prefix}/musik/{$slug}")`.
  - **Headern:**
    - "Musik" (`nav.music`, sv "Musik", en "Music") läggs efter "Om mig",
      både på desktop och i mobilmenyn.
    - Den aktiva länken får `aria-current="page"` och
      `border-b border-ink pb-[2px]`, som i ritningen. "Musik" är aktiv
      för `music.*`, och de andra när `slug` matchar.
  - **`app.css`:** lägg till variablerna nedan i `:root` och
    `[data-theme="dark"]`, och samma namn som färger i `@theme inline`.

    | Token | Ljust | Mörkt |
    |---|---|---|
    | `--sound` | `var(--ink)` | `#61f2c2` |
    | `--bar` | `#111820` | `#171d1c` |
    | `--bar-sound` | `var(--accent)` | `#61f2c2` |
    | `--buy` | `var(--ink)` | `var(--accent)` |
    | `--panel` | `#111820` | `#1a2120` |
    | `--panel-line` | `rgba(237, 236, 234, 0.55)` | `rgba(237, 236, 234, 0.3)` |
  - **`lang/sv/music.php`** har nycklarna och texten ordagrant ur
    ritningarna. `lang/en/music.php` har samma nycklar.

    | Nyckel | sv | en |
    |---|---|---|
    | `title` | "Musik — Patrik Sjerén" | "Music — Patrik Sjerén" |
    | `headline` | "Sju alias<br>och mitt<br>eget namn" | "[EN] Sju alias<br>och mitt<br>eget namn" |
    | `filter` | "Filtrera på alias" | "Filter by alias" |
    | `everything` | "Alla" | "All" |
    | `all` | "Alla släpp" | "All releases" |
    | `newest` | "Nyast först" | "Newest first" |
    | `tracks` | "1 spår\|:count spår" | "1 track\|:count tracks" |
    | `buy` | "Köp" | "Buy" |
    | `play` | "Förlyssna :title" | "Preview :title" |
    | `pause` | "Pausa :title" | "Pause :title" |
    | `coverAlt` | "Omslaget till :title" | "Cover of :title" |

    Rubriken skrivs ut med `{!! !!}`, eftersom den bara innehåller
    `<br>` från språkfilen. Uppgift 4 och 5 lägger till sina nycklar här.
  - **`TODO-INNEHALL.md`:** lägg till "Musik: rubriken på /en/musik är
    platshållare", "Musik: engelska etiketter översatta av Claude, Patrik
    granskar" och "Musik: svensk text tagen ur ritningarna, Patrik
    granskar".

- [ ] **Steg 4: Kör.** `npm run build && ./vendor/bin/pest`. Förväntat: allt grönt.
- [ ] **Steg 5: Commit:** `Add the music list with alias filter and the Musik tab`.

### Uppgift 4: Släppsidan

**Files:**
- Create: `app/Http/Controllers/ShowRelease.php` och `resources/views/music/show.blade.php`
- Modify: `routes/web.php`, `resources/views/components/release-card.blade.php` (`route('music.show')`) och `lang/*/music.php`
- Test: `tests/Feature/Music/ReleasePageTest.php`

**Interfaces:**
- Consumes: modellerna, `<x-release-card>`, `music.*` och `<x-section-head>`.
- Produces:
  - Routen `music.show`, `{locale}/musik/{release:slug}`. Den löser bara
    upp publicerade släpp och ger annars 404.
  - Markupen som uppgift 5 kopplar spelaren till:
    - `[data-play-release]` och `[data-play-track]`.
    - Skrubbern `[data-scrub]`, med ett segment `[data-scrub-track="<id>"]`
      per spår, där `style="flex: <sekunder> 1 0"`.
    - `[data-cart-add="track"]`.

- [ ] **Steg 1: Skriv testerna.**

```php
beforeEach(function () {
    $this->release = Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages', 'slug' => 's-d-l-past-voyages', 'released_on' => '2026-09-10', 'min_price' => 70]);
    foreach ([['Big Dripper', 524], ['Deep Down', 621], ['Saience', 467], ['The Worm', 352]] as $i => [$title, $s]) {
        $this->release->tracks()->create(['position' => $i + 1, 'title' => $title, 'duration_seconds' => $s]);
    }
});

it('shows the release with meta, tracks and the album form', function () {
    $this->get('/se/musik/s-d-l-past-voyages')->assertOk()
        ->assertSee('Past Voyages')->assertSee('10 sep 2026')->assertSee('32 min')->assertSee('WAV')
        ->assertSee('8:44')->assertSee('10:21')->assertSee('Köp · 20 kr')
        ->assertSee('Köp hela albumet')->assertSee('Du väljer, minst 70')
        ->assertSee('min="70"', false)->assertSee('style="flex: 524 1 0"', false);
});

it('shows more from the same alias without itself or drafts', function () {
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Vol. 3']);
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Dold', 'is_published' => false]);
    Release::factory()->create(['artist' => 'Monokii', 'title' => 'Annat alias']);

    $this->get('/se/musik/s-d-l-past-voyages')
        ->assertSee('Mer från S.D.L.')->assertSee('Vol. 3')->assertDontSee('Dold')->assertDontSee('Annat alias')
        ->assertSee('Alla 3 släpp →');
});

it('hides the section when the alias has nothing else and skips an empty description', function () {
    $this->get('/se/musik/s-d-l-past-voyages')->assertDontSee('Mer från')->assertDontSee('[Text om släppet');
});

it('answers 404 for a draft or unknown release', function () {
    Release::factory()->create(['slug' => 'utkast', 'is_published' => false]);
    $this->get('/se/musik/utkast')->assertNotFound();
    $this->get('/se/musik/finns-inte')->assertNotFound();
});

it('speaks English', function () {
    $this->get('/en/musik/s-d-l-past-voyages')->assertSee('10 Sep 2026')->assertSee('Buy · 20 kr');
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **Routen** `musik/{release:slug}` heter `music.show`.
    `ShowRelease::__invoke` gör `abort_unless($release->is_published, 404)`.
    - "Mer från" är högst 4 publicerade släpp med samma `artist`,
      `newest()`, utan släppet självt.
    - Antalet i "Alla N släpp" är `Release::published()->count()`.
    - Titeln är `":title — Patrik Sjerén"`, och beskrivningen
      `description` eller `__('meta.description')`.
  - **`music/show.blade.php`** följer `Slapp.dc.html` på `lg` och uppåt
    och `SlappMobil.dc.html` under:
    - Bakåtlänken är "← Musik / {alias}" till
      `route('music.index', ['alias' => slug])`.
    - Omslaget är 600 px på desktop och full bredd på mobil.
    - Alias och h1 kommer från ritningens mått: 96 px desktop och 46 px
      mobil.
    - **Metadataraden** är "Släppt", "Spår", "Längd" och "Nedladdning".
      Datumet skrivs med
      `$release->released_on->locale(app()->getLocale())->translatedFormat('j M Y')`.
      På mobil blir raden "4 spår · 32 min · WAV".
    - **Skrubbern** har spelknappen (64 px), namnet på spåret och raden
      `Spår 1 · 0:00 / 8:44 · Förlyssning`. Segmenten har
      `flex: <duration_seconds> 1 0`. På mobil ersätts den av knappen
      "Förlyssna allt".
    - **Spårlistan** följer `.trk`-rutnätet, med nummer, spelknapp,
      titel, `clock()` och köpknappen
      `<button data-cart-add="track" data-id data-amount="{{ min_price }}">Köp · 20 kr</button>`.
    - **Albumformuläret** ligger på `bg-panel` med
      `<input type="number" inputmode="numeric" min="{{ min_price }}" value="{{ min_price }}">`
      och knappen "Lägg i korgen".
      - Knappen har `data-cart-add="release"`. JS läser beloppet ur
        fältet.
      - Formuläret skickas inte. `onsubmit` stoppas, och servern
        kontrollerar beloppet i fas 4.
    - **Beskrivningen** visas bara om den inte är tom.
    - **"Mer från"** använder `<x-section-head number="01">`, med fyra
      kolumner på desktop och en horisontell rad med 130 px omslag på
      mobil.
  - **`music.php`** får nycklarna nedan. På engelska blir de rakt
    översatta, utom de som står i kolumnen.

    | Nyckel | sv | en (om inte rak översättning) |
    |---|---|---|
    | `back` | "Musik" | |
    | `released` | "Släppt" | |
    | `trackCount` | "Spår" | |
    | `length` | "Längd" | |
    | `download` | "Nedladdning" | |
    | `minutes` | ":n min" | |
    | `trackLine` | "Spår :n · :time · Förlyssning" | |
    | `playAll` | "Förlyssna allt" | |
    | `buyAlbum` | "Köp hela albumet" | |
    | `inWav` | ":count spår i WAV" | |
    | `youChoose` | "Du väljer, minst :min" | `[EN] Du väljer, minst :min` |
    | `atLeast` | "Minst :min" | |
    | `amount` | "Belopp i kronor" | |
    | `addToCart` | "Lägg i korgen" | |
    | `moreFrom` | "Mer från :alias" | |
    | `allCount` | "Alla :count släpp →" | |

- [ ] **Steg 4: Kör.** `npm run build && ./vendor/bin/pest`. Förväntat: allt grönt.
- [ ] **Steg 5: Commit:** `Add the release page`.

### Uppgift 5: Spelaren

**Files:**
- Create: `resources/js/player.js`, `resources/views/components/player-bar.blade.php` och `resources/views/components/play-data.blade.php`
- Modify: `resources/js/app.js`, layouten (`@persist('player')` före `@livewireScripts`), `release-card.blade.php`, `music/show.blade.php`, `music/index.blade.php`, `resources/css/app.css` och `lang/*/music.php` (`buyTrack`: "Köp spåret", `pausePlayer`: "Pausa", `playPlayer`: "Spela")
- Test: `tests/Feature/Music/PlayerMarkupTest.php`

**Interfaces:**
- Consumes: markupen från uppgift 3 och 4.
- Produces:
  - **`<x-play-data :releases>`** skriver
    `<script type="application/json" data-music>` med
    `{slug: {slug, alias, title, cover, url, tracks: [{id, title, seconds, clock, price, preview}]}}`.
    Bara spår med förlyssning tas med. Ett släpp utan sådana tas inte med.
  - **Knappar:**
    - `[data-play-release="<slug>"]` spelar kön från första spåret.
      Tillhör det spelande spåret släppet, växlar knappen mellan spela
      och paus.
    - `[data-play-track="<id>"][data-release="<slug>"]` spelar det spåret.
    - Ett klick på `[data-scrub-track="<id>"]` startar spåret vid den
      andel av segmentet man klickade på.
  - **`player:state`** skickas på `document` med
    `{release, track, playing, time, duration}`.
  - **`cart:add`** skickas på `document` med `{type, id, amount}` från
    `[data-cart-add]`. Listens knapp skickar det spelande spåret.
  - **`<html data-player="on">`** sätts när listen syns.

- [ ] **Steg 1: Skriv testerna.**

```php
it('persists one player bar in the layout', function () {
    Page::factory()->homepage()->create();
    expect($this->get('/se')->getContent())->toMatch('#x-persist="player"#')->toContain('<audio');
});

it('only offers play for tracks that have a preview', function () {
    Storage::fake('public');
    $release = Release::factory()->create(['slug' => 'med-ljud']);
    $withPreview = $release->tracks()->create(['position' => 1, 'title' => 'Hörs', 'duration_seconds' => 60]);
    $withPreview->addMedia(UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');
    $release->tracks()->create(['position' => 2, 'title' => 'Tyst', 'duration_seconds' => 60]);
    Release::factory()->create(['slug' => 'utan-ljud'])->tracks()->create(['position' => 1, 'title' => 'Inget', 'duration_seconds' => 60]);

    $show = $this->get('/se/musik/med-ljud')->getContent();
    expect($show)->toContain('data-play-track="'.$withPreview->id.'"')
        ->and(substr_count($show, 'data-play-track='))->toBe(1);

    $index = $this->get('/se/musik')->getContent();
    $data = json_decode(Str::between($index, '<script type="application/json" data-music>', '</script>'), true);
    expect($data)->toHaveKey('med-ljud')->not->toHaveKey('utan-ljud')
        ->and($data['med-ljud']['tracks'])->toHaveCount(1)
        ->and($index)->toContain('data-play-release="med-ljud"')->not->toContain('data-play-release="utan-ljud"');
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **`player-bar.blade.php`** följer spelarlisten i `Omslag.dc.html`
    (80 px, rutnätet `56px 300px 1fr 90px 180px`, `bg-bar`) och minispelaren
    i `SlappMobil.dc.html` under `lg`.
    - Roten är `<div id="player" data-player-root hidden>`, och den är
      dold tills något spelas.
    - Den innehåller ett `<audio preload="none">`.
    - Delarna har hakarna `data-player-title`, `data-player-meta`,
      `data-player-cover`, `data-player-time`, `data-player-progress`
      (vågens bredd i procent) och knappen
      `data-player-toggle`. Köpknappen är `data-cart-add="track"`, och JS
      fyller i `id` och `amount`.
    - I layouten ligger den inom `@persist('player') … @endpersist`.
  - **`player.js`** är en modul utan beroenden. Den laddas en gång via
    `app.js`.
    - **Klick:** en `click`-lyssnare på `document` för de fyra
      selektorerna och `[data-cart-add]`. Kön läses ur
      `script[data-music]` i den aktuella sidan, när man klickar.
    - **När spåret tar slut:** `ended` startar nästa spår i kön, och
      efter sista spåret stannar spelaren.
    - **Sync:** `timeupdate`, `play`, `pause` och `ended` uppdaterar
      listen och skickar `player:state`.
    - **Spelknapparna på sidan:** på `player:state` och
      `livewire:navigated` synkas `aria-pressed`, ikonen och
      `aria-label`. Etiketterna kommer från `data-label-play` och
      `data-label-pause` på knappen. Samtidigt synkas skrubbersegmentets
      förlopp (`[data-scrub-progress]`) och raden "Spår n · tid".
    - **Ikoner:** play och pause är samma SVG som i ritningarna.
    - **Tid:** formateras som `m:ss`.
  - **Spelknappar:** `release-card` och spårraderna får spelknapparna,
    men bara när släppet eller spåret finns i spellistan. `index` och
    `show` renderar `<x-play-data>` för de släpp sidan visar. På `show`
    är det släppet och "Mer från".
  - **`app.css`:** `html[data-player="on"] footer { padding-bottom: 120px; }`,
    så att listen inte täcker footern.

- [ ] **Steg 4: Kör.** `npm run build && ./vendor/bin/pest`. Förväntat: allt grönt.

- [ ] **Steg 5: Kontroll i webbläsaren.**
  1. **Testljud och koppling.** Skapa ett testljud i workspace-katalogen:
     `say -o fixture.aiff "test"` följt av
     `afconvert -f WAVE -d LEI16 fixture.aiff fixture.wav`.
     Koppla det som förlyssning till de två första spåren i
     `s-d-l-past-voyages`. Det görs med tinker,
     `$track->addMedia($path)->preservingOriginal()->toMediaCollection('preview')`,
     och lägger bara till. Skriv upp i ledgern att två lokala spår har en
     testförlyssning.
  2. Öppna `/se/musik`, klicka på
     `[data-play-release="s-d-l-past-voyages"]` och läs
     `[!document.querySelector('[data-player-root]').hidden, !document.querySelector('#player audio').paused, document.querySelector('[data-player-title]').textContent.trim()]`.
     Förväntat: `true,true,Big Dripper`.
  3. Kör `eval "window.__a=document.querySelector('#player audio')"`.
     Klicka sedan på omslaget till Past Voyages, som navigerar med
     `wire:navigate`. Läs
     `[window.__a===document.querySelector('#player audio'), !window.__a.paused, window.__a.currentTime>0]`.
     Förväntat: `true,true,true`.
  4. Klicka på `[data-scrub-track]` för spår 2. Förväntat: titeln blir
     "Deep Down" och `aria-pressed` är `true` på spår 2:s rad.
  5. Låt spår 2 spela färdigt. Testljudet är kort, så sätt
     `currentTime = duration - 0.2` med `eval`. Förväntat: spelaren
     stannar, eftersom spår 3 saknar förlyssning och kön tar slut, och
     knappen visar spela.
  6. Navigera till Om mig medan något spelar. Förväntat: samma
     `<audio>`, och den spelar fortfarande.
  7. Ladda om sidan. Förväntat: listen är `hidden` och
     `agent-browser errors` är tom.
  8. Klicka på "Köp spåret" i listen efter att ha lagt till en lyssnare,
     `document.addEventListener('cart:add', e => window.__cart = e.detail)`.
     Förväntat: `{type: "track", id: <spår 1:s id>, amount: 20}`.
  9. Ta skärmbilder av `/se/musik` och `/se/musik/s-d-l-past-voyages` i
     ljust och mörkt läge, vid 1440 och 390 px, med listen öppen. Jämför
     mot ritningarna i <https://claude.ai/artifact/XM79Vkr14cBC8kpZcSxWLy>
     och skriv avvikelserna i ledgern. Kör `git status`. Förväntat: inga
     nya filer i repot.

- [ ] **Steg 6: Commit:** `Add the persistent preview player`.

### Uppgift 6: Dokumentation

- [ ] **Steg 1:** Lägg till "Music" i `CLAUDE.md`:
  - modellerna, diskarna (`public` för omslag och förlyssning, `music` för
    WAV) och seedern,
  - routerna `music.index` och `music.show`,
  - spelarens kontrakt: `data-music`, `data-play-*`, `player:state` och
    `cart:add`, som fas 4 lyssnar på.

  Blir `CLAUDE.md` längre än cirka 160 rader, flytta avsnitten om sajten
  och musiken till `.claude/rules/site.md` och `.claude/rules/music.md`
  och länka dit.
- [ ] **Steg 2: Commit:** `Document music and the player`.
