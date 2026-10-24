# Fas 5: portfolions innehåll, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** Startsidan, /projekt och /om-mig i sjerencms ser ut och rör sig
som i sjeren-2026, på båda språken. Allt innehåll går att ändra i Filament.

**Arkitektur:**
- Projekten blir modellen `Project` med en Filament-resurs. Bilderna ligger
  i medialibrary på disken `public`.
- De tre sidorna byggs med block i `pages.content`. Sex nya block läggs
  till, och tre gamla block får nya fält och ny design.
- Blocken med projekt sparar en ordnad lista med projekt-id.
- `{total}` i blockens text byts mot antalet projekt när sidan visas.
- Rörelserna porteras till `defineMotion` från fas 2: rotatorn, vågorna och
  det fastnålade caset. Hovern i registret är en vanlig lyssnare på
  `document`.
- Innehållet kommer från en ögonblicksbild av sjeren-2026 som läggs i
  `database/seeders/data/portfolio/`. Appen läser aldrig sjeren-2026 när
  den körs.

**Tech Stack:** Laravel 13, Filament 5, Livewire 4, Tailwind 4,
spatie/laravel-medialibrary, GSAP 3 med ScrollTrigger (finns redan), Pest 4
och agent-browser.

**Spec:** `docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`,
avsnittet "Fas 5". Läs också Mål 2, Anti-mål, Källor och Begränsningar
(copy och adresser).

## Avvikelser och beslut (Patrik kontrollerar)

1. **19 projekt, inte 16.** Specen säger 16, men `projects.ts` har 19.
   Importen tar det som står i filen. Ingen kod och inget test hårdkodar
   antalet.
2. **Ögonblicksbild, inte import vid körning.** Task 2 exporterar
   `projects.ts` och `about.ts` med node och kopierar `messages/*.json` till
   sjerencms. Underlaget är sjeren-2026:s arbetskopia, inklusive Patriks
   ocommittade ändringar i `about.ts` och `messages/`. sjeren-2026 ändras
   inte.
3. **Projekten seedas med `firstOrCreate`** på slug, inte `updateOrCreate`
   som `MusicSeeder`. Rubrikerna är utkast som Patrik kommer att skriva om,
   och en ny körning får inte skriva över dem.
4. **SitePagesSeeder fyller bara orörda sidor.** De lokala sidorna finns
   redan: startsidan har bara kolofonen, och /om-mig och /projekt är tomma.
   Seedern skriver in de nya blocken bara när `content` är tomt eller exakt
   lika med fas 2:s seed. En sida som ändrats i admin lämnas i fred.
5. **Utvalda case och registret väljer sina projekt.** Varje block sparar en
   ordnad lista med projekt-id. Seedern lägger in de tre utvalda och de fyra
   första som inte är utvalda, precis som `featured` och `registerPreview` i
   `projects.ts`. Ett register med tom lista visar alla projekt i ordning,
   och det är så /projekt fungerar. Ett projekt som tagits bort hoppas över.
6. **Ingen publicerad/opublicerad på projekt.** Specen nämner det inte, och
   ett nytt projekt ska synas på /projekt direkt. Vill Patrik ha utkast blir
   det en egen ändring.
7. **Den höga bildens mått läses ur filen** med `getimagesize()` när sidan
   visas. ScrollTrigger behöver `width` och `height`, och det fungerar då
   både för seedade bilder och för bilder som laddas upp i admin.
   - Bilderna ligger på den lokala disken `public`.
   - Flyttas de till R2 behöver måtten sparas som custom properties i
     stället (`ponytail:`-kommentar i koden).
8. **Bara de rörelser som finns i Next porteras:** rotatorn, vågorna, caset
   och hovern i registret. Tidslinjens `scaleY`, "metadata 120ms" i
   DESIGN.md och fas 2-planens "heron animerar vikten" byggdes aldrig i Next
   och följer inte med.
9. **About-nycklarna `ctaOne`, `ctaTwo`, `ctaAccent` och `cv` porteras
   inte.** Ingen sida i sjeren-2026 visar dem.
10. **Inga filter på /projekt.** De är obeslutade i sjeren-2026 (se dess
    `TODO-INNEHALL.md`).
11. **CV-filerna** `Sjeren-Portfolio-cv-2024.pdf` och
    `Sjeren-Portfolio-cv-en-2024.pdf` kopieras till `public/files/` redan
    nu, eftersom heron länkar dit. Det är beslut 9 i specen, som annars
    görs i fas 6. De gamla `cv_2025_*.pdf` får ligga kvar.
12. **Sektionsnumren (`01`, `02` …) är ett fält i varje block,** inte
    räknade ur ordningen. Patrik ser och ändrar dem i admin.
13. **Registrets luft ovanför beror på sidan.** Startsidan har
    `clamp(72px,10vw,116px)` och /projekt `clamp(56px,8vw,90px)`, som i
    Next.
    - `pages/show.blade.php` sätter `data-home` på sitt omslag när sidan är
      startsidan.
    - Registret använder `pt-[clamp(56px,8vw,90px)] [[data-home]_&]:pt-[clamp(72px,10vw,116px)]`.
    - Övriga block finns bara på en sida och har sin sidas mått.
14. **Det fastnålade caset lämnar plats åt spelarlisten.** I Next fanns
    ingen spelarlist. Här kan den täcka metadataraden.
    - När `html[data-player="on"]` är höjden `100svh` minus listens höjd:
      80px från `lg`, och minispelarens höjd under det (läs måtten i
      `player-bar.blade.php`).
    - Regeln ligger i `app.css` **utanför lagren**, bredvid
      `html[data-player="on"] footer`. Inne i ett lager vinner
      `h-svh`-utilityn, vilket kommentaren där förklarar.
    - `case-pin` kör `ScrollTrigger.refresh()` när `data-player` ändras.

## Innehåll eller etikett

| Vad | Var det hamnar | Källa |
|---|---|---|
| Prosa, rubriker och sektionsetiketter | Blockdata på varje sida och språk (seedas) | `messages/{se,en}.json` `Home`, `Services`, `Colophon`, `About`, `Projects`, och `about.ts` |
| Projektens kund, rubrik, roll, tech, tjänster och länk | `projects`-tabellen | `projects.ts` |
| Etiketterna i metadataraden: Roll, Tech, Hemsida, Ombyggd sedan dess | `lang/{sv,en}/projects.php`: `role`, `tech`, `site`, `rebuilt` | `messages/*.json` `Meta_fields` |
| Härledd roll: "Design & utveckling" / "Utveckling" | `lang/{sv,en}/projects.php`: `roleDesignDev`, `roleDev` | `role()` i `projects.ts` |
| Siffrorna 25, 15, 1989 och CV-sökvägarna | Blockdata (`profile.stats`, `hero.cv_url`) | `om-mig/page.tsx`, `page.tsx` |

`{se,en}` i sjeren-2026 blir språken `sv` och `en` här. Det är
`Page::$language` och `SetLocaleFromPrefix::LOCALES`. Glöm inte bytet
`se` → `sv` när tvåspråkiga objekt läses.

## Globala begränsningar

- Samma som i fas 4:
  - Kör aldrig `migrate:fresh`, `migrate:refresh`, `migrate:reset`,
    `db:wipe` eller `db:seed --force`.
  - Mot den lokala databasen är `php artisan migrate`,
    `php artisan db:seed --class=ProjectSeeder` och
    `--class=SitePagesSeeder` tillåtna, eftersom de bara lägger till.
  - Kör `php artisan config:clear` som eget kommando före den första
    testkörningen.
  - Kör `npm run build` före testerna.
  - Filer tas bort med `git rm`.
  - Commit görs på `main` med uttryckliga sökvägar. Pusha inte.
- **sjeren-2026 läses men ändras aldrig.** Exporten skriver bara i
  sjerencms.
- **Copy flyttas oförändrad.** Inga omskrivningar, och inga nya strängar
  utan `[platshållare]` och en rad i `TODO-INNEHALL.md`.
  - Utkastmarkeringarna följer med till sjerencms `TODO-INNEHALL.md`:
    - projektrubrikerna,
    - fråga 03 och 06,
    - AI-gruppen i "Arbetar med",
    - startsidans omskrivna strängar,
    - de härledda rollerna,
    - `rebuilt`,
    - bilderna.
  - Skriv om dem kort och peka på sjeren-2026 `TODO-INNEHALL.md` för
    detaljerna.
- **WeUnite nämns i tidslinjen men illustreras aldrig.** Inget projekt,
  ingen bild.
- **Porta 1:1.** Klasser, mått och SVG tas ur komponenterna i
  `~/Code/sjeren-2026/src/components/` och `src/app/[locale]/*/page.tsx`.
  Varje Task pekar på sina filer. Gissa inte mått.
- **Varje nytt eller ändrat block:**
  - registreras i `ContentBlocksSection.php`,
  - får en vy i `resources/views/blocks/{type}.blade.php`,
  - läggs in i `everyBlock()` i `tests/Feature/Admin/PageBlocksTest.php`
    med sina nya fält. Annars slutar sparkontrollen vid de gamla blocken.
- Blockens `make()` tar `?string $name` med typnamnet som standard, som
  `ColophonBlock`.
- Ett textfält med flera rader skrivs som "en per rad" (`Textarea`). Seedern
  byter `|` i messages mot radbrytning.
- **Shell:** skriv inte `.keys` i kommandon. Hooken läser det som `*.key`
  och blockerar.
- **Kontroller i webbläsaren:**
  - Kör `agent-browser` mot `http://sjerencms.test`, med
    `http://sjeren-2026.test` som referens.
  - Varje skärmbild får ett `eval`-värde.
  - Skärmbilder sparas i `.superpowers/sdd/2026-10-24-fas-5-portfolions-innehall/evidence/`.
- Kod, tester och commit-meddelanden skrivs på engelska.

## Granskningsfokus

1. **Spelarlisten täcker det fastnålade caset.** När musik spelas och man
   rullar till 01 Utvalda case syns metadataraden ovanför listen, och
   fastnålningen räknas om när listen dyker upp. Byggs i Task 4 och testas
   i webbläsaren i Task 9.
2. **Navigering fram och tillbaka staplar inte rörelser.**
   - Efter startsidan → /om-mig → startsidan är `data-motion-active` samma
     som första gången.
   - På /om-mig är den `0`, och inga `.pin-spacer` finns kvar.
   - Rotatorn och vågorna startar inte två gånger.
   - Upprensningen byggs i Task 4 och 5 och testas i webbläsaren i
     Task 9.
3. **Ett projekt som tas bort medan ett block pekar på det.** Sidan svarar
   200, de andra projekten visas, räknaren `01 / 0N` och `{total}` följer
   med, och ingen `<img src="">` uppstår. Testas i Task 3 och 4.
4. **`prefers-reduced-motion`.**
   - Rotatorn står still på första frasen, som också finns i HTML:en.
   - Caset nålas inte fast.
   - Vågorna är en stillbild.
   - Den stillastående HTML:en testas i Task 5, resten i webbläsaren i
     Task 9.
5. **Ombyggda sajter och engelska.**
   - Ett projekt med `rebuilt` blir en `div` utan länk i registret och
     visar "Ombyggd sedan dess" i metadataraden.
   - `/en` visar engelsk rubrik, engelsk roll (också den härledda) och
     engelska etiketter.
   - Testas i Task 1, 3 och 4.

---

### Task 1: Projektmodellen och resursen

**Files:**
- Create: `database/migrations/2026_10_27_000000_create_projects_table.php`
- Create: `app/Models/Project.php`, `database/factories/ProjectFactory.php`
- Create: `app/Filament/Resources/Projects/ProjectResource.php` and its `Pages/{ListProjects,CreateProject,EditProject}.php` (mirror `Releases/`)
- Create: `lang/sv/projects.php`, `lang/en/projects.php`
- Modify: `tests/Feature/Admin/ResourceIndexTest.php`
- Test: `tests/Feature/Portfolio/ProjectModelTest.php`, `tests/Feature/Admin/ProjectResourceTest.php`

**Interfaces:**
- Produces:
  - Table `projects`:
    - `slug` (unique), `client`, `headline_sv`, `headline_en`
    - `role_sv`, `role_en` (nullable)
    - `services` (json), `tech` (nullable), `url`
    - `rebuilt` (bool, false), `position` (unsigned int), timestamps
  - `Project` with media collections `image`, `preview` and `tall`, each
    `singleFile()->useDisk('public')`.
  - `Project::headline(string $language): string`, where the language is
    `sv` or `en`.
  - `Project::role(string $language): string`. Returns `role_{language}`, or
    when that is missing, `__('projects.roleDesignDev', [], $language)` if
    any service matches `/design/i`, else `projects.roleDev`.
  - `Project::tech(): string`. Returns `tech`, or else the services joined
    with `' · '`.
  - `Project::host(): string`. Strips `https?://`, `www.` and a trailing
    slash: `https://www.cadoaqua.se/` → `cadoaqua.se`.
  - `Project::imageUrl(): ?string` and `previewUrl(): ?string`. They return
    `null` when the media is missing, never `''`.
  - `Project::tall(): ?array{src: string, width: int, height: int}`, using
    `getimagesize()` on the media path.
  - `Project::chosen(array $ids): Collection`. The projects with these ids in
    this order, skipping ids that no longer exist. Every project ordered by
    `position` when `$ids` is empty.
  - `Project::withTotal(?string $text): string`. Replaces `{total}` with
    `Project::count()`.
  - Factory states `rebuilt()` and `withRole()`.
  - `lang/{sv,en}/projects.php`: `role`, `tech`, `site` and `rebuilt` from
    `Meta_fields`, plus `roleDesignDev` and `roleDev` from `role()` in
    `projects.ts`.

- [ ] **Step 1: Write the failing tests**

```php
it('derives the role from the services in both languages', function () {
    $design = Project::factory()->create(['services' => ['Wordpress', 'UI-design']]);
    $dev = Project::factory()->create(['services' => ['Wordpress', 'Live Canvas']]);

    expect($design->role('sv'))->toBe('Design & utveckling')
        ->and($design->role('en'))->toBe('Design & development')
        ->and($dev->role('sv'))->toBe('Utveckling')
        ->and($dev->role('en'))->toBe('Development');
});

it('prefers a role that is set', function () {
    $project = Project::factory()->create(['role_sv' => 'Design & frontend', 'role_en' => 'Design & frontend', 'services' => ['Wordpress']]);

    expect($project->role('sv'))->toBe('Design & frontend');
});

it('joins the services as tech when none is set', function () {
    expect(Project::factory()->create(['services' => ['Laravel', 'Vue.js'], 'tech' => null])->tech())->toBe('Laravel · Vue.js');
});

it('strips scheme, www and the trailing slash from the host', function () {
    expect(Project::factory()->create(['url' => 'https://www.cadoaqua.se/'])->host())->toBe('cadoaqua.se');
});

it('returns chosen projects in the given order and skips missing ones', function () {
    [$a, $b, $c] = Project::factory()->count(3)->sequence(fn ($s) => ['position' => $s->index + 1])->create();

    expect(Project::chosen([$c->id, $a->id, 999])->pluck('id')->all())->toBe([$c->id, $a->id])
        ->and(Project::chosen([])->pluck('id')->all())->toBe([$a->id, $b->id, $c->id]);
});

it('replaces {total} with the number of projects', function () {
    Project::factory()->count(2)->create();

    expect(Project::withTotal('Tre av {total}'))->toBe('Tre av 2');
});

it('reads the size of the tall image from the file', function () {
    Storage::fake('public');
    $project = Project::factory()->create();
    $project->addMedia(UploadedFile::fake()->image('tall.png', 40, 120))->toMediaCollection('tall');

    expect($project->fresh()->tall())->toMatchArray(['width' => 40, 'height' => 120])
        ->and(Project::factory()->create()->tall())->toBeNull()
        ->and(Project::factory()->create()->previewUrl())->toBeNull();
});
```

In `ProjectResourceTest`:
- An admin sees the list at `/admin/projects`.
- An admin creates a project through `CreateProject` with
  `client`, `slug`, `headline_sv`, `headline_en`, `services`, `url` and
  `position`, without form errors.
- A missing `headline_en` gives a form error.

Add `ProjectResource` to `ResourceIndexTest`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: FAIL, because `Project` does not exist.

- [ ] **Step 3: Implement**
- **The form:**
  - `services` is a `TagsInput`.
  - `rebuilt` is a `Toggle` with the helper text
    "Rebuilt by someone else since: no link".
  - The three images use `SpatieMediaLibraryFileUpload` with their
    collections.
  - Helper texts:
    - `preview`: "Portrait, 450×526, shown on hover in the register".
    - `tall`: "Full-page screenshot. Pins the case and scrolls inside its frame".
- **The table:**
  - Columns: the `preview` image, `client` (searchable), `headline_sv` and
    `position`.
  - `->reorderable('position')` and `->defaultSort('position')`.
- **Navigation:** icon `heroicon-o-briefcase`, and
  `$recordRouteKeyName = 'id'` as in `ReleaseResource`.

- [ ] **Step 4: Run them**

Run: `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`, then `./vendor/bin/pest`
Expected: PASS. The suite is 215 plus the new tests.

- [ ] **Step 5: Migrate locally**

Run: `php artisan migrate`
Expected: `create_projects_table` runs and nothing else.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_10_27_000000_create_projects_table.php app/Models/Project.php database/factories/ProjectFactory.php app/Filament/Resources/Projects lang/sv/projects.php lang/en/projects.php tests/Feature/Portfolio/ProjectModelTest.php tests/Feature/Admin/ProjectResourceTest.php tests/Feature/Admin/ResourceIndexTest.php
git commit -m "Add projects with their admin resource"
```

---

### Task 2: Ögonblicksbilden och ProjectSeeder

**Files:**
- Create: `database/seeders/data/portfolio/projects.json`, `about.json`, `messages-se.json`, `messages-en.json`
- Create: `database/seeders/data/portfolio/images/*` (only the files `projects.json` names)
- Create: `database/seeders/ProjectSeeder.php`
- Test: `tests/Feature/Portfolio/ProjectSeederTest.php`

**Interfaces:**
- Consumes: `Project` from Task 1.
- Produces:
  - `projects.json` has the shape
    `{"projects": [ …the objects in projects.ts, in file order… ], "featured": ["konditori-katarina", …]}`.
    `featured` is `featured.map(p => p.slug)`.
  - `about.json` has the shape `{"timeline": […], "stack": […], "questions": […]}`
    and is the arrays in `about.ts` as they are.
  - `messages-{se,en}.json` are byte-for-byte copies of
    `~/Code/sjeren-2026/messages/{se,en}.json`.
  - `ProjectSeeder`:
    - It is idempotent and calls `firstOrCreate(['slug' => …])`.
    - `position` is the index in the file plus 1.
    - A media item is added only when its collection is empty.
    - Field mapping:
      - `headline.se` → `headline_sv`
      - `role.se` → `role_sv`
      - `href` → `url`
      - `rebuilt` → `rebuilt`

- [ ] **Step 1: Export the snapshot**

Run from `~/Code/sjeren-2026`. The command writes to sjerencms only:

```bash
node --experimental-strip-types --no-warnings -e "
const fs = require('fs'); const out = '/Users/patrik/Code/sjerencms/database/seeders/data/portfolio';
fs.mkdirSync(out + '/images', {recursive: true});
Promise.all([import('./src/data/projects.ts'), import('./src/data/about.ts')]).then(([p, a]) => {
  fs.writeFileSync(out + '/projects.json', JSON.stringify({projects: p.projects, featured: p.featured.map(x => x.slug)}, null, 2) + '\n');
  fs.writeFileSync(out + '/about.json', JSON.stringify({timeline: a.timeline, stack: a.stack, questions: a.questions}, null, 2) + '\n');
  for (const x of p.projects) for (const src of [x.image, x.preview, x.tall?.src].filter(Boolean))
    fs.copyFileSync('public' + src, out + '/images/' + src.split('/').pop());
});"
cp messages/se.json /Users/patrik/Code/sjerencms/database/seeders/data/portfolio/messages-se.json
cp messages/en.json /Users/patrik/Code/sjerencms/database/seeders/data/portfolio/messages-en.json
git -C /Users/patrik/Code/sjeren-2026 status --short
```

Expected:
- `projects.json` holds 19 projects and three featured slugs.
- `images/` holds every file it names.
- sjeren-2026's `git status` shows the same lines as before the command.

- [ ] **Step 2: Write the failing test**

```php
function portfolio(): array
{
    return json_decode(file_get_contents(database_path('seeders/data/portfolio/projects.json')), true);
}

beforeEach(fn () => Storage::fake('public'));

it('seeds every project in file order with its images', function () {
    $this->seed(ProjectSeeder::class);

    $rows = portfolio()['projects'];
    expect(Project::orderBy('position')->pluck('slug')->all())->toBe(array_column($rows, 'slug'));

    $tall = array_column(array_filter($rows, fn ($r) => isset($r['tall'])), 'slug');
    Project::all()->each(function (Project $project) use ($tall) {
        expect($project->imageUrl())->not->toBeNull()
            ->and($project->previewUrl())->not->toBeNull()
            ->and($project->tall() !== null)->toBe(in_array($project->slug, $tall));
    });
});

it('maps the bilingual fields and the flags', function () {
    $this->seed(ProjectSeeder::class);

    $katarina = Project::where('slug', 'konditori-katarina')->first();
    expect($katarina->headline('sv'))->toStartWith('Konditori i Malmö.')
        ->and($katarina->role('en'))->toBe('Design & development')
        ->and($katarina->tech())->toBe('Laravel · Vue.js · Tailwind · UI/UX')
        ->and(Project::where('slug', 'champion-generators')->value('rebuilt'))->toBeTrue();
});

it('keeps edits made in the admin when it runs again', function () {
    $this->seed(ProjectSeeder::class);
    $media = Media::count();
    Project::where('slug', 'kalibr')->update(['headline_sv' => 'Ändrad i admin']);

    $this->seed(ProjectSeeder::class);

    expect(Project::where('slug', 'kalibr')->value('headline_sv'))->toBe('Ändrad i admin')
        ->and(Project::count())->toBe(count(portfolio()['projects']))
        ->and(Media::count())->toBe($media);
});
```

- [ ] **Step 3: Run it and watch it fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/ProjectSeederTest.php`
Expected: FAIL, because `ProjectSeeder` does not exist.

- [ ] **Step 4: Implement `ProjectSeeder`**

Write a docblock like the one in `MusicSeeder`. It names the source
(sjeren-2026's working tree on 2026-10-24) and the command in Step 1, so
that someone can redo the snapshot. Images are added with
`->preservingOriginal()`.

- [ ] **Step 5: Run it, then seed locally**

Run: `./vendor/bin/pest tests/Feature/Portfolio`, then `php artisan db:seed --class=ProjectSeeder`
Expected: PASS. Afterwards `/admin/projects` lists 19 projects with images.

- [ ] **Step 6: Commit**

```bash
git add database/seeders/data/portfolio database/seeders/ProjectSeeder.php tests/Feature/Portfolio/ProjectSeederTest.php
git commit -m "Seed the projects from a snapshot of the Next site"
```

---

### Task 3: Registret

**Files:**
- Create: `app/Filament/Forms/Components/Blocks/RegisterBlock.php`, `resources/views/blocks/register.blade.php`
- Create: `resources/views/components/register-row.blade.php`, `resources/js/register.js`
- Create: `app/Filament/Forms/Components/Blocks/ProjectPicker.php`. This is the shared `Repeater` used by Task 4 too.
- Modify: `resources/js/app.js`, `resources/views/pages/show.blade.php` (`data-home`, ruling 13), `ContentBlocksSection.php`, `tests/Feature/Admin/PageBlocksTest.php`, `tests/Pest.php` (`blockPage`)
- Test: `tests/Feature/Portfolio/RegisterBlockTest.php`
- Port 1:1: `RegisterRow.tsx`, the register sections in `page.tsx` and `projekt/page.tsx`

**Interfaces:**
- Consumes: `Project::chosen()`, `withTotal()`, `headline()`, `imageUrl()`
  and `previewUrl()`.
- Produces:
  - `ProjectPicker::make(): Repeater` named `projects`, holding one `Select`
    `project_id` with its options from `Project` ordered by `position`.
    It is reorderable. The block data is
    `projects: [['project_id' => 3], …]`.
  - Block `register` with the fields:
    - `number`, `label`, `aside` (`{total}` allowed)
    - `projects` (an empty list means all)
    - `all_label` (nullable, with `{total}`). When it is filled, a link to
      `route('page.show', ['slug' => 'projekt'])` closes the list.
    - When `all_label` is empty, the list ends with a bare `border-t` row,
      as on /projekt.
  - `<x-register-row :project :number />`.
    - It renders `/ 001`, the client, the headline in the app language, the
      services, and both images from `RegisterRow.tsx`.
    - It is an `<a href target="_blank" rel="noreferrer" data-register-row>`,
      or a `<div data-register-row>` when `rebuilt`.
    - An image is left out when its URL is `null`.
  - `register.js`: one delegated `mousemove` on `document` that sets
    `--mx` and `--my` on the closest `[data-register-row]`, as `follow()`
    does.

- [ ] **Step 1: Write the failing tests**

Helper, in `tests/Pest.php`: `blockPage(string $type, array $data, string $language = 'sv'): Page`
creates the published home page for that language holding that one block.
Tasks 4–7 use it too.

```php
it('lists the chosen projects in order with zero-padded numbers', function () {
    [$a, $b] = Project::factory()->count(2)->sequence(['client' => 'Alfa', 'headline_sv' => 'Rubrik A'], ['client' => 'Beta'])->create();
    blockPage('register', ['number' => '02', 'label' => 'Fler utvalda uppdrag', 'aside' => '{total} totalt', 'projects' => [['project_id' => $b->id], ['project_id' => $a->id]]]);

    $this->get('/se')->assertOk()->assertSeeInOrder(['/ 001', 'Beta', '/ 002', 'Alfa', 'Rubrik A'])->assertSee('2 totalt');
});

it('lists every project in position order when none are chosen', function () {
    Project::factory()->count(3)->sequence(fn ($s) => ['client' => "Kund {$s->index}", 'position' => 3 - $s->index])->create();
    blockPage('register', ['number' => '01', 'label' => 'Uppdrag', 'aside' => '{total}', 'projects' => []]);

    $this->get('/se')->assertSeeInOrder(['Kund 2', 'Kund 1', 'Kund 0']);
});

it('never links a rebuilt site', function () {
    $rebuilt = Project::factory()->rebuilt()->create(['url' => 'https://gammal.se/']);
    blockPage('register', ['number' => '02', 'label' => 'L', 'aside' => '', 'projects' => [['project_id' => $rebuilt->id]]]);

    $this->get('/se')->assertDontSee('href="https://gammal.se/"', false)->assertSee('<div data-register-row', false);
});

it('skips a deleted project and leaves no empty image', function () {
    [$a, $b] = Project::factory()->count(2)->create();
    blockPage('register', ['number' => '02', 'label' => 'L', 'aside' => '{total} totalt', 'projects' => [['project_id' => $a->id], ['project_id' => $b->id]]]);
    $b->delete();

    $this->get('/se')->assertOk()->assertSee('1 totalt')->assertDontSee('src=""', false);
});

it('links to all projects and uses the English headline on /en', function () {
    $project = Project::factory()->create(['headline_en' => 'English line']);
    blockPage('register', ['number' => '02', 'label' => 'More', 'aside' => '', 'all_label' => 'See all {total}', 'projects' => [['project_id' => $project->id]]], 'en');

    $this->get('/en')->assertSee('English line')->assertSee('See all 1')->assertSee('href="'.url('/en/projekt').'"', false);
});
```

Add `register` with two chosen projects to `everyBlock()`. Create the
projects in `beforeEach`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/RegisterBlockTest.php tests/Feature/Admin/PageBlocksTest.php`
Expected: FAIL, because the block view is missing and `PageBlocksTest`
cannot find the block type.

- [ ] **Step 3: Implement** the block, the row, `ProjectPicker` and
  `register.js` (imported in `app.js`). Then add the `data-home` wrapper in
  `pages/show.blade.php`.

- [ ] **Step 4: Run them**

Run: `npm run build`, then `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/Blocks/RegisterBlock.php app/Filament/Forms/Components/Blocks/ProjectPicker.php resources/views/blocks/register.blade.php resources/views/components/register-row.blade.php resources/js/register.js resources/js/app.js resources/views/pages/show.blade.php app/Filament/Forms/Components/ContentBlocksSection.php tests/Feature/Portfolio/RegisterBlockTest.php tests/Feature/Admin/PageBlocksTest.php tests/Pest.php
git commit -m "Add the project register block"
```

---

### Task 4: Utvalda case med fastnålning

**Files:**
- Create: `app/Filament/Forms/Components/Blocks/FeaturedCasesBlock.php`, `resources/views/blocks/featured_cases.blade.php`
- Create: `resources/views/components/project-meta.blade.php`, `resources/js/motions/case-pin.js`
- Modify: `resources/js/app.js`, `resources/css/app.css` (ruling 14), `ContentBlocksSection.php`, `tests/Feature/Admin/PageBlocksTest.php`
- Test: `tests/Feature/Portfolio/FeaturedCasesBlockTest.php`
- Port 1:1: `FeaturedCase.tsx`, `CaseImage.tsx`, `ProjectMeta.tsx`, and the 01 section in `page.tsx`

**Interfaces:**
- Consumes:
  - `ProjectPicker` from Task 3.
  - `Project::chosen()`, `withTotal()`, `tall()`, `imageUrl()`, `role()`,
    `tech()` and `host()`.
  - `lang/*/projects.php` from Task 1.
- Produces:
  - Block `featured_cases` with `number`, `label`, `aside` (`{total}`
    allowed) and `projects`.
    - The counter is `01 / 03`.
    - Its total is the number of cases actually rendered, not the number
      chosen.
  - `<x-project-meta :project />` renders the `dl` from `ProjectMeta.tsx`.
    The labels come from `__('projects.*')`.
  - A project with `tall()`:
    - The wrapper is `data-motion="case-pin"`.
    - The image is `data-case-image` with the `width` and `height`
      attributes from `tall()`.
  - A project without `tall()` gets the cropped frame from
    `FeaturedCase.tsx`, without `data-motion`.
  - `case-pin.js` does the following:
    - It runs `gsap.registerPlugin(ScrollTrigger)` once.
    - Inside `gsap.matchMedia('(prefers-reduced-motion: no-preference)')`
      it creates the trigger from `CaseImage.tsx`: `MAX_SCROLL` 1.8, no pin
      under 40px of travel.
    - It uses a `MutationObserver` on `<html>` `data-player` to call
      `ScrollTrigger.refresh()`.
    - Its cleanup kills the trigger, disconnects the observer and reverts
      `mm`. No `.pin-spacer` remains afterwards.

- [ ] **Step 1: Write the failing tests**

```php
it('pins a case that has a tall image and crops one that has not', function () {
    Storage::fake('public');
    [$tall, $flat] = Project::factory()->count(2)->create();
    $tall->addMedia(UploadedFile::fake()->image('t.png', 40, 120))->toMediaCollection('tall');
    $flat->addMedia(UploadedFile::fake()->image('f.png', 40, 30))->toMediaCollection('image');
    featuredPage([$tall->id, $flat->id]);

    $html = $this->get('/se')->assertOk()->getContent();
    expect(substr_count($html, 'data-motion="case-pin"'))->toBe(1)
        ->and($html)->toContain('width="40" height="120"')
        ->toContain('01 / 02')->toContain('02 / 02');
});

it('counts only the cases it renders', function () {
    [$a, $b, $c] = Project::factory()->count(3)->create();
    featuredPage([$a->id, $b->id, $c->id]);
    $c->delete();

    $this->get('/se')->assertOk()->assertSee('02 / 02')->assertDontSee('/ 03');
});

it('shows the meta row with the host link, or no link for a rebuilt site', function () {
    $live = Project::factory()->create(['url' => 'https://www.cadoaqua.se/', 'services' => ['UI-design']]);
    $old = Project::factory()->rebuilt()->create();
    featuredPage([$live->id, $old->id]);

    $this->get('/se')->assertSee(['Roll', 'Design & utveckling', 'cadoaqua.se', 'Ombyggd sedan dess'])
        ->assertSee('href="https://www.cadoaqua.se/"', false);
});

it('uses the English labels and role on /en', function () {
    $project = Project::factory()->create(['services' => ['Laravel']]);
    featuredPage([$project->id], 'en');

    $this->get('/en')->assertSee(['Role', 'Development', $project->headline_en]);
});
```

`featuredPage(array $ids, string $language = 'sv')` is a local helper in
the test file. It calls `blockPage('featured_cases', …)` with
`{number: '01', label: 'Utvalda case', aside: 'Tre av {total}', projects: …}`.

Add `featured_cases` to `everyBlock()`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/FeaturedCasesBlockTest.php`
Expected: FAIL, because the view is missing.

- [ ] **Step 3: Implement** the block, `project-meta`, `case-pin.js`
  (imported in `app.js`) and the height rule from ruling 14 in `app.css`.

- [ ] **Step 4: Run them**

Run: `npm run build`, then `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: PASS.

The pin, the player bar and the leak check run in the browser in Task 9,
on the pages Task 8 seeds. Do not edit the local pages here. Task 8 only
fills pages whose content is untouched.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/Blocks/FeaturedCasesBlock.php resources/views/blocks/featured_cases.blade.php resources/views/components/project-meta.blade.php resources/js/motions/case-pin.js resources/js/app.js resources/css/app.css app/Filament/Forms/Components/ContentBlocksSection.php tests/Feature/Portfolio/FeaturedCasesBlockTest.php tests/Feature/Admin/PageBlocksTest.php
git commit -m "Add the pinned featured cases block"
```

---

### Task 5: Heron med rotator och vågor

**Files:**
- Create: `app/Filament/Forms/Components/Blocks/HeroBlock.php`, `resources/views/blocks/hero.blade.php`
- Create: `resources/js/motions/hero-rotator.js`, `resources/js/motions/hero-waves.js`
- Create: `public/files/Sjeren-Portfolio-cv-2024.pdf`, `public/files/Sjeren-Portfolio-cv-en-2024.pdf`, copied from `~/Code/sjeren-2026/public/files/`
- Modify: `resources/js/app.js`, `ContentBlocksSection.php`, `tests/Feature/Admin/PageBlocksTest.php`
- Test: `tests/Feature/Portfolio/HeroBlockTest.php`
- Port 1:1: `HeroRotator.tsx`, `HeroWaves.tsx`, `WaveUnderline` (already `<x-wave-underline>`), and the hero section in `page.tsx`

**Interfaces:**
- Produces:
  - Block `hero` with the fields:
    - `lead`, `phrases` (one per line), `intro_one`, `intro_two`
    - `work_label`, which links to the projekt page in the current
      language
    - `cv_label` and `cv_url`
  - The markup in the Blade view:
    - The `<h1>` has the lead, an `sr-only` holding the first phrase, and
      the roll `span[aria-hidden][data-motion="hero-rotator"]`.
    - The roll carries `data-phrases="{{ json_encode($phrases) }}"`.
    - Server-side, the roll holds letter spans for phrase 0 (`data-out`)
      and phrase 1 (`data-in`). Without JavaScript or with reduced motion,
      the first phrase stands still.
  - The `<canvas data-motion="hero-waves">` is the canvas in
    `HeroWaves.tsx`, with its mask as an inline style.
  - `hero-rotator.js`:
    - It runs the timeline from `HeroRotator.tsx`: `HOLD` 2.2, 0.62s,
      `power3.inOut`, stagger 0.03.
    - `onComplete` moves the index on, rebuilds both letter rows, updates
      the `sr-only` text and starts again.
    - Its cleanup kills the running timeline and reverts the `matchMedia`.
  - `hero-waves.js` ports the draw loop, the layers, `BULGE` and the pointer
    easing.
    - Reduced motion gives one still frame.
    - Its cleanup does the following:
      - cancels the frame,
      - removes `pointermove`,
      - disconnects the `ResizeObserver` and the theme `MutationObserver`.

- [ ] **Step 1: Write the failing tests**

```php
function heroData(): array
{
    return ['lead' => 'Jag levererar', 'phrases' => "UI/UX-design\nDesignsystem\nFrontend",
        'intro_one' => 'Ett.', 'intro_two' => 'Två.', 'work_label' => 'Se arbetet',
        'cv_label' => 'Ladda ner CV', 'cv_url' => '/files/Sjeren-Portfolio-cv-2024.pdf'];
}

it('renders the lead with the first phrase standing still', function () {
    Page::factory()->homepage()->create(['content' => [['type' => 'hero', 'data' => heroData()]]]);

    $html = $this->get('/se')->assertOk()->getContent();
    expect($html)->toContain('Jag levererar')
        ->toContain('<span class="sr-only">UI/UX-design</span>')
        ->toContain('data-motion="hero-rotator"')
        ->toContain('data-phrases="'.e(json_encode(['UI/UX-design', 'Designsystem', 'Frontend'])).'"')
        ->toContain('data-motion="hero-waves"');
});

it('links to the work and the CV', function () {
    Page::factory()->homepage()->create(['content' => [['type' => 'hero', 'data' => heroData()]]]);

    $this->get('/se')->assertSee('href="'.url('/se/projekt').'"', false)
        ->assertSee('href="/files/Sjeren-Portfolio-cv-2024.pdf"', false);
});

it('serves both CVs at their old addresses', function () {
    expect(file_exists(public_path('files/Sjeren-Portfolio-cv-2024.pdf')))->toBeTrue()
        ->and(file_exists(public_path('files/Sjeren-Portfolio-cv-en-2024.pdf')))->toBeTrue();
});
```

Add `hero` to `everyBlock()`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/HeroBlockTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement** the block, the two motions (imported in
  `app.js`) and the two PDF copies (`cp`).

- [ ] **Step 4: Run them**

Run: `npm run build`, then `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: PASS.

The rotator, the waves and reduced motion are checked in the browser in
Task 9. Do not edit the local pages here.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/Blocks/HeroBlock.php resources/views/blocks/hero.blade.php resources/js/motions/hero-rotator.js resources/js/motions/hero-waves.js resources/js/app.js public/files/Sjeren-Portfolio-cv-2024.pdf public/files/Sjeren-Portfolio-cv-en-2024.pdf app/Filament/Forms/Components/ContentBlocksSection.php tests/Feature/Portfolio/HeroBlockTest.php tests/Feature/Admin/PageBlocksTest.php
git commit -m "Add the home hero with the rotator and the waves"
```

---

### Task 6: Tre gamla block i ny design

**Files:**
- Modify: `app/Filament/Forms/Components/Blocks/{ServiceCardsBlock,HeadlineTextBlock,FaqAccordionBlock}.php`
- Create: `resources/views/blocks/{service_cards,headline_text,faq_accordion}.blade.php`
- Modify: `tests/Feature/Admin/PageBlocksTest.php`. The three entries get their new data, and the `assertSee` list in its first test follows the new values ('Services' and 'Questions' were old `section_title` and `heading` values).
- Test: `tests/Feature/Portfolio/ReworkedBlocksTest.php`
- Port 1:1:
  - `Services.tsx` with section 03 in `page.tsx`
  - section 04 "Om mig" in `page.tsx`
  - section 04 "frågor" in `om-mig/page.tsx`

**Interfaces:**
- Produces new schemas, with the old fields removed (no data relies on
  them, see the spec "Databasen börjar om från noll"):
  - `service_cards`: `number`, `label`, and `columns` (a repeater with
    `title` and `items`, one item per line).
    - The view is the grid from `Services.tsx`.
    - It draws three dividers: none before the first column, none after
      the last, and only after column 1 below `lg`.
  - `headline_text`: `number`, `label`, `headline`, `text`, and `cards` (a
    repeater with `title` and `body`, which may be empty).
  - `faq_accordion`: `number`, `label`, and `items` (a repeater with
    `question`, `answer` and `note`, all plain text).
    - Rows are numbered `01`, `02` and so on.
    - The list closes with a `border-t` div.

- [ ] **Step 1: Write the failing tests**

```php
it('renders four service columns with one item per line', function () {
    blockPage('service_cards', ['number' => '03', 'label' => 'Tjänster', 'columns' => [
        ['title' => 'Design', 'items' => "UI/UX-design\nDesignsystem"],
        ['title' => 'Utveckling', 'items' => 'Frontend'],
    ]]);

    $this->get('/se')->assertSeeInOrder(['03', 'Tjänster', 'Design', 'UI/UX-design', 'Designsystem', 'Utveckling', 'Frontend']);
});

it('renders the about teaser with its cards', function () {
    blockPage('headline_text', ['number' => '04', 'label' => 'Om mig', 'headline' => 'Först house.', 'text' => 'Jag har byggt.', 'cards' => [['title' => 'Design', 'body' => 'Logotyp.']]]);

    $this->get('/se')->assertSeeInOrder(['04', 'Om mig', 'Först house.', 'Jag har byggt.', 'Design', 'Logotyp.']);
});

it('numbers the questions and shows the note', function () {
    blockPage('faq_accordion', ['number' => '04', 'label' => 'Sex frågor', 'items' => [
        ['question' => 'Vad gör du?', 'answer' => 'Jag designar.', 'note' => 'I samklang.'],
        ['question' => 'Och innan?', 'answer' => 'Ljud.', 'note' => 'Rytm.'],
    ]]);

    $this->get('/se')->assertSeeInOrder(['01', 'Vad gör du?', 'Jag designar.', 'I samklang.', '02', 'Och innan?']);
});
```

`blockPage()` comes from Task 3, in `tests/Pest.php`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/ReworkedBlocksTest.php tests/Feature/Admin/PageBlocksTest.php`
Expected: FAIL. The three views are missing. Once the new fields are in
`everyBlock()`, the save test fails against the old required fields.

- [ ] **Step 3: Implement** the three schemas and views.

- [ ] **Step 4: Run them**

Run: `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/Blocks/ServiceCardsBlock.php app/Filament/Forms/Components/Blocks/HeadlineTextBlock.php app/Filament/Forms/Components/Blocks/FaqAccordionBlock.php resources/views/blocks/service_cards.blade.php resources/views/blocks/headline_text.blade.php resources/views/blocks/faq_accordion.blade.php tests/Feature/Portfolio/ReworkedBlocksTest.php tests/Feature/Admin/PageBlocksTest.php tests/Pest.php
git commit -m "Redesign the services, about teaser and questions blocks"
```

---

### Task 7: Blocken på /om-mig och /projekt

**Files:**
- Create: `app/Filament/Forms/Components/Blocks/{PageHeroBlock,ProfileBlock,TimelineBlock,StackBlock}.php`
- Create: `resources/views/blocks/{page_hero,profile,timeline,stack}.blade.php`
- Modify: `ContentBlocksSection.php`, `tests/Feature/Admin/PageBlocksTest.php`
- Test: `tests/Feature/Portfolio/AboutBlocksTest.php`
- Port 1:1:
  - the hero sections in `om-mig/page.tsx` and `projekt/page.tsx`
  - sections 01–03 in `om-mig/page.tsx`
  - `Timeline.tsx`

**Interfaces:**
- Consumes: `Project::withTotal()`.
- Produces:
  - `page_hero`:
    - Fields: `lines` (one per line, joined with `<br>`), `text`
      (`{total}` allowed) and `size` (`large` | `medium`).
    - `large` is the h1 on om-mig, `clamp(42px,8vw,96px)`.
    - `medium` is the h1 on projekt, `clamp(38px,7vw,86px)`.
  - `profile`: `number`, `label`, `column_one`, `column_two` and `stats`.
    - The two column fields hold paragraphs separated by a blank line.
    - The first paragraph of `column_one` gets the 17px ink style from
      `om-mig/page.tsx`. The rest are dim.
    - `stats` is a repeater of `value` and `label`.
  - `timeline`: `number`, `label`, `work_label`, `sound_label` and
    `entries`.
    - Each entry is a repeater item with `years`, `title`, `subtitle`,
      `body`, `tags` (a `TagsInput`, may be empty) and `track`
      (`work` | `sound`).
    - The head with its legend is ported from `om-mig/page.tsx` and is not
      `<x-section-head>`.
  - `stack`: `number`, `label`, and `groups` (a repeater of `label` and
    `items`).

- [ ] **Step 1: Write the failing tests**

```php
it('breaks the hero lines and fills in the total', function () {
    Project::factory()->count(2)->create();
    blockPage('page_hero', ['lines' => "Trettiosju år,\ntvå spår", 'text' => 'De {total} här.', 'size' => 'large']);

    $this->get('/se')->assertSee('Trettiosju år,<br>', false)->assertSee('De 2 här.')->assertSee('clamp(42px,8vw,96px)', false);
});

it('renders the profile columns and the margin numbers', function () {
    blockPage('profile', ['number' => '01', 'label' => 'Profil', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tredje.', 'stats' => [['value' => '25', 'label' => 'År i branschen']]]);

    $this->get('/se')->assertSeeInOrder(['Profil', 'Först.', 'Sedan.', 'Tredje.', '25', 'År i branschen'])
        ->assertSee('text-[17px] leading-[1.65]">Först.', false);
});

it('marks each timeline entry with its track and lists its tags', function () {
    blockPage('timeline', ['number' => '02', 'label' => 'Erfarenhet', 'work_label' => 'Arbete', 'sound_label' => 'Ljud', 'entries' => [
        ['years' => '1989', 'title' => 'Ibiza', 'subtitle' => 'Restresa', 'body' => 'Sista.', 'tags' => [], 'track' => 'sound'],
        ['years' => '2024 —', 'title' => 'Eget', 'subtitle' => 'Malmö', 'body' => 'Design.', 'tags' => ['Laravel'], 'track' => 'work'],
    ]]);

    $this->get('/se')->assertSee('data-track="sound"', false)->assertSee('data-track="work"', false)
        ->assertSeeInOrder(['Ibiza', 'Ljud', 'Eget', 'Laravel', 'Arbete']);
});

it('renders the stack groups', function () {
    blockPage('stack', ['number' => '03', 'label' => 'Arbetar med', 'groups' => [['label' => 'Design', 'items' => 'Figma · Penpot']]]);

    $this->get('/se')->assertSeeInOrder(['Arbetar med', 'Design', 'Figma · Penpot']);
});
```

Add all four to `everyBlock()`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/AboutBlocksTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement** the four blocks and their views.

- [ ] **Step 4: Run them**

Run: `./vendor/bin/pest tests/Feature/Portfolio tests/Feature/Admin`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/Blocks/PageHeroBlock.php app/Filament/Forms/Components/Blocks/ProfileBlock.php app/Filament/Forms/Components/Blocks/TimelineBlock.php app/Filament/Forms/Components/Blocks/StackBlock.php resources/views/blocks/page_hero.blade.php resources/views/blocks/profile.blade.php resources/views/blocks/timeline.blade.php resources/views/blocks/stack.blade.php app/Filament/Forms/Components/ContentBlocksSection.php tests/Feature/Portfolio/AboutBlocksTest.php tests/Feature/Admin/PageBlocksTest.php
git commit -m "Add the about and projects page blocks"
```

---

### Task 8: Sidorna fylls med innehållet

**Files:**
- Modify: `database/seeders/SitePagesSeeder.php`
- Modify: `tests/Feature/Site/SitePagesSeederTest.php`
- Test: `tests/Feature/Portfolio/PortfolioPagesTest.php`

**Interfaces:**
- Consumes:
  - every block type from Tasks 3–7, plus `colophon`
  - `ProjectSeeder` and the snapshot from Task 2
- Produces: `SitePagesSeeder::run()`.
  - It first calls `$this->call(ProjectSeeder::class)`.
  - It then builds the content of each page from `messages-{se,en}.json`,
    `about.json` and `projects.json`. Swedish reads `se`.
  - Content per page:
    - **start**:
      - `hero`: `Home.heroLead`, `heroPhrases`, `introOne`, `introTwo`,
        `linkWork` and `linkCv`. `cv_url` is the path from `page.tsx` for
        that language.
      - `featured_cases` 01: `featuredLabel` and `featuredCount`, with the
        ids of `featured`.
      - `register` 02: `registerLabel`, `registerCount` and `registerAll`,
        with the first four projects not in `featured`.
      - `service_cards` 03: `servicesLabel`, with the four columns from
        `Services.*`.
      - `headline_text` 04: `aboutLabel`, `aboutHeadline`, `aboutBody`,
        and the cards Design, Dev and Free.
      - `colophon`: `Colophon.*`.
    - **om-mig**:
      - `page_hero` (large): `heroOne` and `heroTwo` as two lines, then
        `heroBody`.
      - `profile` 01: `profileOne`–`profileFour` (one and two in the first
        column, three and four in the second), with the stats 25, 15 and
        1989 and their `stat*` labels.
      - `timeline` 02: `experienceLabel`, `trackWork`, `trackSound`, and
        `about.timeline`. `years`, `subtitle` and `body` are in the page's
        language, `title` as it is, `tags` or `[]`.
      - `stack` 03: `stackLabel` and `about.stack`.
      - `faq_accordion` 04: `questionsLabel` and `about.questions`, mapped
        `q`, `a`, `note`.
    - **projekt**:
      - `page_hero` (medium): `heroCount` and `heroBody`. `{total}` is
        kept as it is.
      - `register` 01: `listLabel`, with `aside` `{total}`, no projects and
        no `all_label`.
  - Every `{total}` in messages stays a literal token.
  - The fill rule:
    - A missing page is created with its content.
    - An existing page has its `content` replaced only when it is `[]` or
      exactly the phase-2 content. For start that is
      `[colophon(Verktyg|Tools)]`.
    - Anything else is left alone. Titles and meta keep the
      `firstOrCreate` rule they have today.

- [ ] **Step 1: Write the failing tests**

In `PortfolioPagesTest` (`Storage::fake('public')` in `beforeEach`):

```php
it('builds the start page in the order of the Next site', function () {
    $this->seed(SitePagesSeeder::class);

    expect(collect(Page::where('is_homepage', true)->where('language', 'sv')->first()->content)->pluck('type')->all())
        ->toBe(['hero', 'featured_cases', 'register', 'service_cards', 'headline_text', 'colophon']);
});

it('features the three cases and previews the next four', function () {
    $this->seed(SitePagesSeeder::class);
    $data = json_decode(file_get_contents(database_path('seeders/data/portfolio/projects.json')), true);
    $preview = array_slice(array_values(array_diff(array_column($data['projects'], 'slug'), $data['featured'])), 0, 4);
    $content = collect(Page::where('is_homepage', true)->where('language', 'sv')->first()->content)->keyBy('type');
    $slugs = fn (string $type) => Project::chosen(array_column($content[$type]['data']['projects'], 'project_id'))->pluck('slug')->all();

    expect($slugs('featured_cases'))->toBe($data['featured'])->and($slugs('register'))->toBe($preview);
});

it('serves the three pages in both languages with the live total', function () {
    $this->seed(SitePagesSeeder::class);
    $total = Project::count();

    $this->get('/se')->assertOk()->assertSee(['Jag levererar', "Tre av {$total}", "Visa alla {$total}"]);
    $this->get('/en/om-mig')->assertOk()->assertSee(['Last-minute trip, aged 19', 'Hosting & ops']);
    $this->get('/se/projekt')->assertOk()->assertSee('/ '.str_pad((string) $total, 3, '0', STR_PAD_LEFT));
});

it('fills a page that still has its phase-2 content', function () {
    Page::create(['title' => 'Patrik Sjerén', 'slug' => 'start', 'language' => 'sv', 'is_homepage' => true, 'is_published' => true,
        'content' => [['type' => 'colophon', 'data' => ['label' => 'Verktyg', 'tools' => 'Figma · Penpot · Affinity · Adobe CC · Next.js · React · Nuxt · Vue.js · Laravel · Livewire · Filament · Astro · Node · Tailwind · GSAP · Docker · Forge · Envoyer · Vercel · Netlify · Webflow · Wordpress']]]]);

    $this->seed(SitePagesSeeder::class);

    expect(count(Page::where('slug', 'start')->where('language', 'sv')->first()->content))->toBe(6);
});

it('leaves content edited in the admin alone', function () {
    $this->seed(SitePagesSeeder::class);
    $page = Page::where('slug', 'om-mig')->where('language', 'sv')->first();
    $page->update(['content' => [['type' => 'stack', 'data' => ['number' => '01', 'label' => 'Egen', 'groups' => []]]]]);

    $this->seed(SitePagesSeeder::class);

    expect($page->fresh()->content[0]['data']['label'])->toBe('Egen');
});
```

In `SitePagesSeederTest`, the existing test keeps its six pages and its
colophon check. Add `Storage::fake('public')`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Portfolio/PortfolioPagesTest.php tests/Feature/Site/SitePagesSeederTest.php`
Expected: FAIL. The start page holds only the colophon, and om-mig is
empty.

- [ ] **Step 3: Implement**
- Keep the seeder readable. Use one private method per page that returns
  its blocks, and a small `block(string $type, array $data): array`.
- The phase-2 colophon content stays as the constant it already is, so the
  fill rule can compare against it.
- Compare with `==`, not `===`. The stored JSON has `data` before `type`
  (checked locally on 2026-10-24), and `===` fails on key order.
- Locally there are 6 pages: start has only the colophon, and om-mig and
  projekt are `[]` in both languages. The rule fills all six.

- [ ] **Step 4: Run them, then the whole suite**

Run: `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Seed locally**

Run: `php artisan db:seed --class=SitePagesSeeder`
Expected:
- `/se`, `/en`, `/se/om-mig`, `/en/om-mig`, `/se/projekt` and
  `/en/projekt` show their full content.
- Check `php artisan tinker --execute="echo App\Models\Page::count();"`:
  6, the same as before.

- [ ] **Step 6: Commit**

```bash
git add database/seeders/SitePagesSeeder.php tests/Feature/Portfolio/PortfolioPagesTest.php tests/Feature/Site/SitePagesSeederTest.php
git commit -m "Fill the start, about and projects pages from the Next content"
```

---

### Task 9: Dokumentation och sidorna i webbläsaren

**Files:**
- Create: `.claude/rules/portfolio.md`
- Modify: `CLAUDE.md` (pointer under "Public Site and Music", the `Models/` line, and the seeding line under "Checking in the Browser")
- Modify: `.claude/rules/site.md` (motions list: `hero-rotator`, `hero-waves`, `case-pin`, and `register.js`)
- Modify: `TODO-INNEHALL.md`

**Interfaces:**
- `portfolio.md` covers:
  - the `Project` model and its fallbacks
  - the snapshot and how to redo it (Task 2 Step 1)
  - the block types with their fields
  - `{total}`
  - the seeder's fill rule
  - `data-home`
  - the player-bar rule
  - the motions with their cleanup
- `TODO-INNEHALL.md` gets the draft list from the Global Constraints, with
  a pointer to sjeren-2026 `TODO-INNEHALL.md`.

- [ ] **Step 1: Write the docs**

- [ ] **Step 2: Compare against the Next site**

Use agent-browser and screenshot each page of both sites at 1440×900 and
390×844, in light and dark mode:
- `/se`, `/se/om-mig` and `/se/projekt` on `sjerencms.test`
- the same paths on `sjeren-2026.test`

Each screenshot gets an eval of `document.title`. Fix anything that
differs in markup or classes.

Expected: the pairs match. The fonts, the rotator phrase and the wave
phase may differ.

- [ ] **Step 3: Check the Review Focus in the browser**

Eval for steps 2 and 3:
`[document.documentElement.dataset.motionActive, document.querySelectorAll('.pin-spacer').length]`.

1. On `/se`, scroll to the first case.
   Eval: the number of `.pin-spacer`.
   Expected: 3, because the three featured projects all have a tall image.
2. Navigate start → om-mig → start → projekt with `wire:navigate`.
   Expected:
   - On start, before the first case: `["6", 3]`. Six is the rotator, the
     waves, three cases and the colophon.
   - On om-mig: `["0", 0]`.
   - Back on start: the same as the first time.
   - On projekt: `["0", 0]`.
3. Back on `/se`, wait 3 seconds.
   Eval: `document.querySelector('h1 .sr-only').textContent`, and the
   number of `[data-in]` spans under the roll.
   Expected:
   - The second phrase.
   - The span count equals the letters of one phrase, so no second
     timeline is running.
4. Play a preview, then scroll through 01 Utvalda case.
   Expected: the screenshot shows the meta row above the bar.
5. Toggle dark mode.
   Expected: the screenshot shows the waves in the dark tokens.
6. Run with `prefers-reduced-motion: reduce`. Use agent-browser if it can
   emulate the media feature, otherwise `mcp__chrome-devtools__emulate`.
   Expected:
   - no `.pin-spacer`
   - after 3 seconds the phrase is still the first one
   - the waves stand still
7. On `/en`, hover a register row.
   Expected: the screenshot shows the preview tilted in.
8. Run `git status`.
   Expected: no stray files outside `evidence/`.

- [ ] **Step 4: Update the graph and run the suite**

Run: `graphify update .`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add .claude/rules/portfolio.md .claude/rules/site.md CLAUDE.md TODO-INNEHALL.md
git commit -m "Document the portfolio content"
```
