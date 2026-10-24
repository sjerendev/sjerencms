# Textrörelse, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** Patrik kan slå på en rörelse för de flesta texter i blocken, en
text i taget, och ställa in den i Filament. Texten glider in när den kommer
in i bild, en gång, och står sedan kvar.

**Arkitektur:**
- Inställningarna sparas i blockets JSON under `data.motion.{nyckel}`.
- `App\Support\TextMotion` gör om inställningarna till data-attribut, och
  Blade-direktivet `@textMotion('nyckel')` skriver ut dem på det element
  som redan finns i vyn.
- Filament-delen `MotionSettings::make()` är en hopfälld panel som varje
  block anropar per nyckel.
- `App\Support\PageMotion` hämtar den svenska sidans inställningar till
  den engelska sidan, block för block.
- Rörelsen `text` i `resources/js/motions/text.js` spelar texten med GSAP
  SplitText och ScrollTrigger via `defineMotion`. CSS döljer texten innan
  den är redo och visar den ändå efter 2 s om skriptet inte hann.

**Tech Stack:** Laravel 13, Filament 5, Livewire 4, Tailwind 4, GSAP 3.14.2
(SplitText och ScrollTrigger ingår), Pest 4 och agent-browser.

**Spec:** `docs/superpowers/specs/2026-10-24-textrorelse-design.md`.

## Avvikelser och beslut (Patrik kontrollerar)

1. **Ett Blade-direktiv i stället för en Blade-komponent** (specen,
   beslut 12). En komponent hade lagt ett extra element runt texten och
   ändrat layouten, till exempel avstånden i en flexbox. Direktivet lägger
   attributen på det element som redan finns.
2. **Nycklarna.**
   - Ett fält på toppnivå har sitt namn som nyckel, till exempel
     `intro_one`.
   - Ett fält i en repeater heter `{repeater}_{fält}`, till exempel
     `items_answer`.
   - Sektionshuvudet (`01 ─── ETIKETT`) har nyckeln `head`. Den gäller
     hela raden, och numret, strecket, etiketten och asiden är dess delar.
3. **Vilka fält som får rörelse** (specen ber om listan här):

   | Block | Nycklar |
   |---|---|
   | hero | `lead`, `intro_one`, `intro_two` |
   | page_hero | `lines`, `text` |
   | headline_text | `head`, `headline`, `text`, `cards_title`, `cards_body` |
   | service_cards | `head`, `columns_title`, `columns_items` |
   | faq_accordion | `head`, `items_question`, `items_answer`, `items_note` |
   | profile | `head`, `column_one`, `column_two`, `stats_value`, `stats_label` |
   | timeline | `head`, `entries_years`, `entries_title`, `entries_subtitle`, `entries_body` |
   | stack | `head`, `groups_label`, `groups_items` |
   | featured_cases | `head` |
   | register | `head` |
   | colophon | `label` |

   Utanför listan:
   - Fält som redan rör sig: heroblockets `phrases` och kolofonens `tools`.
   - Länkarna: heroblockets `work_label` och `cv_label` samt registrets
     `all_label`. Deras understrykning och pil är egna element, så det
     hade blivit en egen sak att lösa.
   - Tidslinjens `tags` och dess förklaring (`work_label`,
     `sound_label`).
   - Projektens texter, som Patrik tagit bort ur första versionen.
4. **Hela stycket med mask** delas i rader som glider fram samtidigt, var
   och en bakom sin egen kant. Det ger maskeffekten utan att något runt
   texten ändras.
5. **Testdata i webbläsarkontrollen.** Task 4 skapar en egen sida,
   `textrorelse-test`, i den lokala databasen och avpublicerar den när
   kontrollen är klar. Den raderas inte. Patrik tar bort den i admin om
   han vill.
6. **Före Task 1:** arbetskopian har ocommittade ändringar för ljust läge
   som standard (`site.blade.php`, `ThemeTest.php`) och designdokumentet.
   Task 4 ändrar samma rad i `site.blade.php`. De committas först, med
   Patriks godkännande, i två egna commits.

## Global Constraints

- Kör aldrig `migrate:fresh`, `migrate:refresh`, `migrate:reset`,
  `db:wipe` eller `db:seed --force`, och inget annat som raderar data i
  databasen. Tester körs bara mot sqlite `:memory:`.
- Läs och ändra aldrig dotenv-filerna. `~/Code/sjeren-2026` är skrivskyddat.
- Temporära filer läggs bara i
  `/private/tmp/claude-501/-Users-patrik-Code-sjeren-2026/ca8887a6-4676-45df-8188-c2ec57ba48ae/scratchpad`,
  aldrig i `/tmp`.
- Committa på `main` med uttryckliga sökvägar. Pusha aldrig.
- Kod, tester och commit-meddelanden skrivs på engelska. Admin är på
  engelska.
- Inga nya beroenden. SplitText importeras från `gsap/SplitText`.
- Rörelsen är **av** som standard. En text utan inställning ska ge exakt
  samma HTML som idag.
- Standard när rörelsen slås på: `from: bottom`, `distance: 24`,
  `split: none`, `stagger: 0.06`, `duration: 0.8`, `delay: 0`,
  `ease: soft`, `mask: false`.
- Easing: `soft` = `power3.out`, `sharp` = `expo.out`,
  `bouncy` = `back.out(1.7)`.
- Rörelsen startar en gång, när elementets överkant når 85 % ned på
  skärmen (`start: 'top 85%'`, `once: true`).
- Med `prefers-reduced-motion: reduce` syns texten direkt, utan att delas.
- Utan JavaScript syns texten. Har skriptet inte tagit hand om texten
  inom 2 s blir den synlig ändå.
- Efter att texten har spelats backas delningen, och elementets innehåll
  är detsamma som före.
- `<html data-motion-active>` går tillbaka till samma värde efter ett
  sidbyte.
- Kör `npm run build` innan tester när vyer, JS eller CSS har ändrats.

## Review Focus

1. **Tillbaka med `wire:navigate` till en sida som lämnades mitt i en
   rörelse.** Texten ska synas hel, utan kvarblivna delningselement och
   utan att fastna på `opacity: 0`. Testas i Task 4, steg 7.
2. **Fönstret ändrar bredd innan en text delad per rad har spelats.**
   Raderna räknas om och brytningarna stämmer. Efter att texten spelats
   står den som vanlig text. Testas i Task 4, steg 7.
3. **Specialtecken och delning per bokstav.** `<b>` i ett fält escapas som
   idag, och å, ä, ö och emoji delas som ett tecken. Escapingen testas i
   Task 3. Tecknen kontrolleras i Task 4, steg 7.
4. **Tomt fält med rörelse på**, till exempel en tom `note` i FAQ. Det
   får inte ge fel, och inget får vänta osynligt. Testas i Task 3.
5. **Engelsk sida utan svensk motsvarighet**, eller vars svenska sida är
   avpublicerad eller har andra block. Den engelska texten står still och
   sidan fungerar. Testas i Task 3.

---

### Task 1: Inställningarna och direktivet

**Files:**
- Create: `app/Support/TextMotion.php`
- Modify: `app/Providers/AppServiceProvider.php` (registrera direktivet i `boot()`)
- Test: `tests/Feature/Site/TextMotionTest.php`

**Interfaces:**
- Produces:
  - `TextMotion::DEFAULTS`: `array` med standardvärdena från Global
    Constraints.
  - `TextMotion::normalize(?array $settings): ?array`. Ger `null` när
    `enabled` inte är sant. Annars ger den alla åtta nycklar utom
    `enabled`, med standardvärdet för det som saknas eller är ogiltigt
    och tal klämda inom gränserna.
    - Gränser: `distance` 0–400 (heltal), `stagger` 0–1, `duration`
      0.1–3, `delay` 0–5.
    - Tillåtna värden: `from` ∈ `bottom|top|left|right`,
      `split` ∈ `none|lines|words|chars`, `ease` ∈ `soft|sharp|bouncy`.
  - `TextMotion::attributes(?array $settings): HtmlString`. Ger en tom
    sträng eller
    ` data-motion="text" data-text-motion="{escapad JSON av normalize}"`.
  - Blade-direktivet `@textMotion('key')` kompileras till
    `TextMotion::attributes(($motion ?? [])['key'] ?? null)`. En vy som
    inkluderas utan `$motion` fungerar alltså också.

- [ ] **Step 1: Skriv de fallerande testerna**

```php
it('prints nothing when motion is off or missing', function ($settings) {
    expect((string) TextMotion::attributes($settings))->toBe('');
})->with([null, [[]], [['enabled' => false, 'from' => 'left']]]);

it('fills in the defaults when motion is turned on', function () {
    expect(TextMotion::normalize(['enabled' => true]))->toEqual([
        'from' => 'bottom', 'distance' => 24, 'split' => 'none', 'stagger' => 0.06,
        'duration' => 0.8, 'delay' => 0, 'ease' => 'soft', 'mask' => false,
    ]);
});

it('replaces invalid values and clamps numbers', function () {
    expect(TextMotion::normalize(['enabled' => true, 'from' => 'diagonal', 'distance' => 9999,
        'split' => 'sentences', 'duration' => '0', 'delay' => -2, 'ease' => 'wobbly']))
        ->toMatchArray(['from' => 'bottom', 'distance' => 400, 'split' => 'none',
            'duration' => 0.1, 'delay' => 0, 'ease' => 'soft']);
});

it('prints the motion attributes with escaped json', function () {
    $html = (string) TextMotion::attributes(['enabled' => true, 'split' => 'words']);

    expect($html)->toStartWith(' data-motion="text" data-text-motion="')
        ->and($html)->toContain('&quot;split&quot;:&quot;words&quot;');
});

it('reads the key from $motion in a view, and works without it', function () {
    expect(Blade::render('<p @textMotion(\'intro_one\')>x</p>', ['motion' => ['intro_one' => ['enabled' => true]]]))
        ->toContain('<p  data-motion="text"');
    expect(Blade::render('<p @textMotion(\'intro_one\')>x</p>'))->toBe('<p >x</p>');
});
```

- [ ] **Step 2: Kör testerna och se dem fallera**

Run: `php artisan config:clear` and then `./vendor/bin/pest tests/Feature/Site/TextMotionTest.php`
Expected: FAIL, `Class "App\Support\TextMotion" not found`

- [ ] **Step 3: Skriv `App\Support\TextMotion` och registrera direktivet**

`distance` blir ett heltal. `stagger`, `duration` och `delay` blir
flyttal. `mask` blir ett booleskt värde.

- [ ] **Step 4: Kör testerna och se dem gå igenom**

Run: `./vendor/bin/pest tests/Feature/Site/TextMotionTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Support/TextMotion.php app/Providers/AppServiceProvider.php tests/Feature/Site/TextMotionTest.php
git commit -m "Add text motion settings and the @textMotion directive"
```

### Task 2: Panelen i admin

**Files:**
- Create: `app/Filament/Forms/Components/MotionSettings.php`
- Modify: `app/Filament/Forms/Components/Blocks/{Hero,PageHero,HeadlineText,ServiceCards,FaqAccordion,Profile,Timeline,Stack,FeaturedCases,Register,Colophon}Block.php`
- Test: `tests/Feature/Admin/MotionSettingsTest.php`, `tests/Feature/Admin/PageBlocksTest.php`

**Interfaces:**
- Consumes: `TextMotion::DEFAULTS` (Task 1).
- Produces: `MotionSettings::make(string $key, string $label): Section`.
  - Panelen är en hopfälld `Section` med rubriken `"Motion: {$label}"`
    och `statePath("motion.{$key}")`.
  - Fält:
    - `Toggle enabled`, live.
    - `Select from`: Bottom, Top, Left, Right.
    - `TextInput distance`, numeric, suffix px.
    - `Select split`: Whole text, Lines, Words, Letters.
    - `TextInput stagger`, numeric, suffix s. Syns bara när `split` inte
      är `none`.
    - `TextInput duration` och `TextInput delay`, numeric, suffix s.
    - `Select ease`: Soft, Sharp, Bouncy.
    - `Toggle mask`.
  - Alla fält utom `enabled` syns bara när `enabled` är på och har
    standardvärdet från `TextMotion::DEFAULTS`.
  - På en engelsk sida visas bara texten
    `Motion is set on the Swedish page.`. Språket läses ur
    `$livewire->data['language']` på CreatePage och EditPage.
- Varje block lägger panelen direkt efter fältet den gäller. För en
  repeater ligger den efter repeatern, och för `head` efter `label`.

- [ ] **Step 1: Skriv de fallerande testerna**

I `MotionSettingsTest.php`, ett dataset med varje block och nyckel från
tabellen i beslut 3. Använd blockdata från `everyBlock()` i
`PageBlocksTest.php` (flytta funktionen till `tests/Pest.php` så att båda
filerna når den).

```php
it('keeps motion settings when the page is saved', function (string $type, string $key) {
    $block = collect(everyBlock())->firstWhere('type', $type);
    $block['data']['motion'] = [$key => ['enabled' => true, 'from' => 'left', 'split' => 'words']];
    $page = Page::create(['title' => 'M', 'slug' => 'm', 'language' => 'sv', 'content' => [$block]]);

    Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->call('save')->assertHasNoFormErrors();

    expect(collect($page->fresh()->content)->first()['data']['motion'][$key])
        ->toMatchArray(['enabled' => true, 'from' => 'left', 'split' => 'words']);
})->with([
    ['hero', 'lead'], ['hero', 'intro_one'], ['hero', 'intro_two'],
    ['page_hero', 'lines'], ['page_hero', 'text'],
    ['headline_text', 'head'], ['headline_text', 'headline'], ['headline_text', 'text'],
    ['headline_text', 'cards_title'], ['headline_text', 'cards_body'],
    ['service_cards', 'head'], ['service_cards', 'columns_title'], ['service_cards', 'columns_items'],
    ['faq_accordion', 'head'], ['faq_accordion', 'items_question'], ['faq_accordion', 'items_answer'], ['faq_accordion', 'items_note'],
    ['profile', 'head'], ['profile', 'column_one'], ['profile', 'column_two'], ['profile', 'stats_value'], ['profile', 'stats_label'],
    ['timeline', 'head'], ['timeline', 'entries_years'], ['timeline', 'entries_title'], ['timeline', 'entries_subtitle'], ['timeline', 'entries_body'],
    ['stack', 'head'], ['stack', 'groups_label'], ['stack', 'groups_items'],
    ['featured_cases', 'head'], ['register', 'head'], ['colophon', 'label'],
]);

it('tells an English page that motion is set on the Swedish page', function () {
    $sv = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data']);
    $en = blockPage('hero', collect(everyBlock())->firstWhere('type', 'hero')['data'], 'en');

    $this->get("/admin/pages/{$en->id}/edit")->assertSee('Motion is set on the Swedish page.');
    $this->get("/admin/pages/{$sv->id}/edit")->assertDontSee('Motion is set on the Swedish page.')
        ->assertSee('Motion: Intro one');
});
```

`beforeEach` loggar in en admin och skapar två projekt, som i
`PageBlocksTest.php`.

- [ ] **Step 2: Kör testerna och se dem fallera**

Run: `./vendor/bin/pest tests/Feature/Admin/MotionSettingsTest.php`
Expected: FAIL. `motion` saknas efter sparning, eftersom Filament bara
behåller fält som finns i formuläret, och panelrubriken finns inte.

- [ ] **Step 3: Skriv `MotionSettings` och lägg in panelerna i de elva blocken**

- [ ] **Step 4: Kör admintesterna**

Run: `./vendor/bin/pest tests/Feature/Admin`
Expected: PASS, och `PageBlocksTest` är fortfarande grönt.

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Forms/Components/MotionSettings.php app/Filament/Forms/Components/Blocks tests/Pest.php tests/Feature/Admin/MotionSettingsTest.php tests/Feature/Admin/PageBlocksTest.php
git commit -m "Add a motion panel to every text in the blocks"
```

### Task 3: Inställningarna når sidan

**Files:**
- Create: `app/Support/PageMotion.php`
- Modify: `app/Http/Controllers/ShowPage.php`, `resources/views/pages/show.blade.php`, `resources/views/components/section-head.blade.php`, de elva blockvyerna i `resources/views/blocks/`
- Test: `tests/Feature/Site/PageMotionTest.php`

**Interfaces:**
- Consumes: `@textMotion` (Task 1), nycklarna (beslut 3).
- Produces:
  - `PageMotion::for(Page $page): array<int, array>`. Ger
    blockindex → `data.motion` för blocket.
    - Svensk sida: blockens egen `motion`.
    - Engelsk sida: `motion` från blocket på samma plats på den svenska
      sidan med samma slug (`is_homepage` för startsidan). Det gäller
      bara när den svenska sidan är publicerad och blocket där har samma
      `type`. Annars `[]`.
    - Den engelska sidans egen `motion` används aldrig.
  - `show.blade.php` skickar `'motion' => $motions[$loop->index] ?? []` till
    varje block.
  - `<x-section-head :motion="($motion ?? [])['head'] ?? null" … />`.
    Komponenten lägger attributen på sin yttre `div` och
    `data-motion-piece` på numret, strecket, etiketten och asiden.
- **Regeln i vyerna:**
  - Ett fält som blir ett element får `@textMotion('key')` på det
    elementet.
  - Ett fält som blir flera element får attributen på deras gemensamma
    behållare, som redan finns. Varje element får `data-motion-piece`.
    Det gäller profilens kolumner (`div` runt styckena) och tjänsternas
    `columns_items` (`div` runt raderna).
  - I en repeater får varje rads element attributen, så att raderna
    startar var för sig.

- [ ] **Step 1: Skriv de fallerande testerna**

```php
$hero = fn (array $motion = []) => ['lead' => 'Jag levererar', 'phrases' => "A\nB", 'intro_one' => 'Ett <b>fett</b>.',
    'intro_two' => 'Två.', 'work_label' => 'W', 'cv_label' => 'C', 'cv_url' => '/x.pdf', 'motion' => $motion];

it('prints no motion attributes when nothing is turned on', function () use ($hero) {
    blockPage('hero', $hero());
    $this->get('/se')->assertDontSee('data-text-motion', false)->assertDontSee('data-motion="text"', false);
});

it('puts the motion on the text and keeps it escaped', function () use ($hero) {
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]));
    $this->get('/se')->assertSee('data-motion="text"', false)->assertSee('Ett &lt;b&gt;fett&lt;/b&gt;.', false);
});

it('gives the English page the Swedish motion, block by block', function () use ($hero) {
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]));
    blockPage('hero', $hero(['intro_two' => ['enabled' => true]]), 'en');
    // Only intro_one moves on /en: the English page's own motion is ignored.
    expect(substr_count($this->get('/en')->getContent(), 'data-motion="text"'))->toBe(1);
});

it('leaves English text still when the Swedish page differs or is missing', function (callable $swedish) use ($hero) {
    $swedish();
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]), 'en');
    $this->get('/en')->assertOk()->assertDontSee('data-motion="text"', false);
})->with([
    'no Swedish page' => [fn () => null],
    'unpublished' => [fn () => Page::factory()->homepage()->unpublished()->create(['content' => [['type' => 'hero', 'data' => ['motion' => ['intro_one' => ['enabled' => true]]]]]])],
    'other block type' => [fn () => blockPage('stack', ['number' => '1', 'label' => 'L', 'groups' => [], 'motion' => ['intro_one' => ['enabled' => true]]])],
]);

it('starts every repeater row on its own', function () {
    blockPage('faq_accordion', ['number' => '04', 'label' => 'Q', 'items' => [
        ['question' => 'A?', 'answer' => 'A.', 'note' => ''], ['question' => 'B?', 'answer' => 'B.', 'note' => ''],
    ], 'motion' => ['items_answer' => ['enabled' => true], 'items_note' => ['enabled' => true]]]);
    // Two answers and two empty notes, each its own motion; the empty note renders without error.
    expect(substr_count($this->get('/se')->getContent(), 'data-motion="text"'))->toBe(4);
});

it('marks each paragraph of a multi-element field as a piece', function () {
    blockPage('profile', ['number' => '01', 'label' => 'P', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tre.',
        'stats' => [], 'motion' => ['column_one' => ['enabled' => true]]]);
    $html = $this->get('/se')->getContent();
    expect(substr_count($html, 'data-motion="text"'))->toBe(1)->and(substr_count($html, 'data-motion-piece'))->toBe(2);
});

it('moves the section head as one row', function () {
    blockPage('stack', ['number' => '03', 'label' => 'Arbetar med', 'groups' => [], 'motion' => ['head' => ['enabled' => true]]]);
    expect(substr_count($this->get('/se')->getContent(), 'data-motion-piece'))->toBe(3); // number, line, label; no aside
});
```

- [ ] **Step 2: Kör testerna och se dem fallera**

Run: `./vendor/bin/pest tests/Feature/Site/PageMotionTest.php`
Expected: FAIL. Inga `data-motion="text"` finns i sidan, och
`PageMotion` finns inte.

- [ ] **Step 3: Skriv `PageMotion::for()` och skicka `$motions` från `ShowPage` till `show.blade.php`**

- [ ] **Step 4: Lägg in `@textMotion` och `data-motion-piece` i section-head och de elva vyerna enligt regeln ovan**

- [ ] **Step 5: Kör sidtesterna och hela sviten**

Run: `npm run build` and then `./vendor/bin/pest`
Expected: PASS. Alla tidigare tester är gröna, eftersom en text utan
rörelse ger samma HTML som förut.

- [ ] **Step 6: Commit**

```bash
git add app/Support/PageMotion.php app/Http/Controllers/ShowPage.php resources/views/pages/show.blade.php resources/views/components/section-head.blade.php resources/views/blocks tests/Feature/Site/PageMotionTest.php
git commit -m "Print text motion on the page, Swedish settings for both languages"
```

### Task 4: Rörelsen i webbläsaren

**Files:**
- Create: `resources/js/motions/text.js`
- Modify:
  - `resources/js/app.js`: `import './motions/text';`
  - `resources/css/app.css`: döljregeln och reservregeln
  - `resources/views/components/layouts/site.blade.php`: `data-js` i
    headskriptet
  - `.claude/rules/site.md`, `CLAUDE.md`: dokumentationen
- Test: `tests/Feature/Site/ThemeTest.php` (headskriptet), kontroll med agent-browser

**Interfaces:**
- Consumes: `data-motion="text"`, `data-text-motion` (JSON enligt
  `TextMotion::normalize`) och `data-motion-piece` (Task 1 och 3).
- Produces:
  - `defineMotion('text', init)`. `init` returnerar en cleanup som dödar
    tween och ScrollTrigger, backar delningen och tar bort inline-stilar.
  - Attributet `data-text-motion-armed` sätts när texten har fått sitt
    startläge, och direkt vid reducerad rörelse.
- **Döljregeln och reservregeln**, utanför CSS-lagren som spelarlistens
  regel, så att den slår Tailwind:

```css
/* Hidden until text.js has set the start state; shown anyway after 2 s if it never does. */
html[data-js] [data-text-motion]:not([data-text-motion-armed]) {
    visibility: hidden;
    animation: text-motion-failsafe 0s 2s forwards;
}
@keyframes text-motion-failsafe { to { visibility: visible; } }
```

- **Rörelsen:**
  - `init` tar först bort `data-text-motion-armed` och kvarblivna
    inline-stilar. Livewire kan visa en ögonblicksbild från förra besöket
    när man går tillbaka.
  - Målen är elementets `[data-motion-piece]`, eller elementet självt om
    det inte har några.
  - Delning:
    - `split: none` utan mask: målen själva rör sig.
    - `split: none` med mask: SplitText delar i rader med `mask: 'lines'`,
      och alla rader rör sig samtidigt (beslut 4).
    - Annars: SplitText på varje mål med `type` = `split` och
      `mask` = `split` när masken är på. Delarna samlas i ordning, så att
      staggern löper över hela fältet.
  - Startläget är `opacity: 0` plus förskjutning:
    - Utan mask: bottom `y: +distance`, top `y: -distance`,
      left `x: -distance`, right `x: +distance`.
    - Med mask: `yPercent` eller `xPercent` ±100 åt samma håll, och
      ingen `opacity`.
  - Tweenen har `delay`, `duration`, `stagger` och easingen från Global
    Constraints.
  - Triggern är det första målet, med `start: 'top 85%'` och
    `once: true`.
  - `onComplete` backar delningen och tar bort inline-stilarna.
  - Delning per rad använder SplitText `autoSplit: true` med `onSplit`,
    så att raderna räknas om vid ny bredd innan texten spelats.
  - SplitText behåller sin standard `aria: 'auto'`, så att skärmläsare
    läser hela meningen.

- [ ] **Step 1: Skriv det fallerande testet för headskriptet**

```php
it('marks that JavaScript runs, in the blocking head script', function () {
    $head = Str::between($this->get('/se')->getContent(), '<head>', '</head>');
    expect($head)->toContain('document.documentElement.dataset.js=""');
});
```

- [ ] **Step 2: Kör testet och se det fallera**

Run: `./vendor/bin/pest tests/Feature/Site/ThemeTest.php`
Expected: FAIL på det nya testet. De andra tre är gröna.

- [ ] **Step 3: Lägg till `document.documentElement.dataset.js=""` först i `try`-blocket i headskriptet, och sedan CSS-regeln, `text.js` och importen**

Behåll början `<script>(function(){try{var t=localStorage.getItem("theme");`.
`ThemeTest` kontrollerar den.

- [ ] **Step 4: Bygg och kör hela sviten**

Run: `npm run build` and then `./vendor/bin/pest`
Expected: PASS

- [ ] **Step 5: Skapa testsidan lokalt**

Run, i en `php artisan tinker --execute=...`: skapa en publicerad svensk
sida med `slug` `textrorelse-test` och följande innehåll:
- en `hero` med `intro_one` = `{enabled, split: lines, mask: true}` och
  `intro_two` = `{enabled, delay: 0.4}`,
- en `faq_accordion` med sex frågor och `items_answer` =
  `{enabled, split: words}`,
- en `page_hero` med `lines` = `{enabled, split: chars}` och texten
  `Åtta år, ä ö 🎧`.

Ingen annan rad i databasen ändras.

- [ ] **Step 6: Kontrollera i webbläsaren med agent-browser mot `http://sjerencms.test/se/textrorelse-test`**

Para ihop varje skärmbild med ett `eval`-värde. Spara skärmbilder med
absolut sökväg i scratchpad. Kontrollera:
1. **Heron spelar.** Före rörelsen är
   `[data-text-motion]:not([data-text-motion-armed])` 0 efter 1 s, och
   `intro_one` har mask-element. Efter 2 s är `intro_one.innerHTML`
   samma som serverns HTML, alltså att delningen har backats.
2. **En fråga längre ned** har `opacity` 0 innan du scrollar och 1 efter
   att du scrollat dit och väntat 1,5 s. Scrolla upp och ned igen: den
   spelas inte en gång till.
3. **Reducerad rörelse** (emulera `prefers-reduced-motion: reduce` med
   agent-browser och ladda om): all text syns direkt och ingen delning
   görs.
4. **JavaScript avstängt:** all text syns.
5. **Reserven:** lägg till ett element med `data-text-motion` via `eval`
   utan att det armeras. Dess `visibility` är `visible` efter 2,5 s.

- [ ] **Step 7: Kontrollera Review Focus 1–3 i samma flik**
- **Review Focus 1:** gå till `/se` med `wire:navigate` mitt i heron,
  och sedan tillbaka. Texten syns hel, inga element med mask finns kvar,
  och `data-motion-active` är samma som före besöket.
- **Review Focus 2:** ändra fönsterbredden från 1440 till 700 innan
  `intro_one` har spelats. Radernas antal följer den nya bredden.
- **Review Focus 3:** i `page_hero` syns `Å`, `ä`, `ö` och `🎧` som hela
  tecken under och efter rörelsen.

- [ ] **Step 8: Avpublicera testsidan**

Run: `php artisan tinker --execute=` som sätter `is_published = false` på
`textrorelse-test`. Radera den inte. Kör `git status`: inga skärmbilder i
repot.

- [ ] **Step 9: Dokumentera**
- **`.claude/rules/site.md`:**
  - Lägg till `text` i listan över rörelser.
  - Beskriv nycklarna, `@textMotion`, `data-motion-piece`, att den
    svenska sidan styr båda språken och reservregeln på 2 s.
- **`CLAUDE.md`, "Adding a New Block":** ett femte steg. Lägg
  `MotionSettings::make()` efter varje textfält och `@textMotion` i vyn,
  och lägg till nycklarna i datasetet i `MotionSettingsTest.php`.

- [ ] **Step 10: Commit**

```bash
git add resources/js/motions/text.js resources/js/app.js resources/css/app.css resources/views/components/layouts/site.blade.php tests/Feature/Site/ThemeTest.php .claude/rules/site.md CLAUDE.md
git commit -m "Play text motion once when it scrolls into view"
```

`.claude/` står i `.gitignore`, men `.claude/rules/site.md` är redan
spårad, så en vanlig `git add` räcker.
