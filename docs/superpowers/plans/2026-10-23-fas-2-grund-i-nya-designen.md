# Fas 2: grund i den nya designen, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** Den publika sajten har en layout i den nya designen, med header,
footer, ljust och mörkt läge, `/se/` och `/en/`, de gamla adresserna och
sidbyten med `wire:navigate`. En livscykel för GSAP startar om rörelserna
efter varje sidbyte.

**Arkitektur:**
- Sidorna är `Page`-poster som slås upp på språk och slug. Varje block i
  `content` renderas med `@includeIf("blocks.{typ}")`. Kolofonen är det
  första blocket som får en vy.
- Layouten är en Blade-komponent med Livewires skript, så att
  `wire:navigate` fungerar på sidor utan Livewire-komponenter.
- JavaScript ligger i en Vite-entry, `resources/js/app.js`, som laddas en
  gång i `<head>`. Den sköter läget och rörelsernas livscykel.

**Tech stack:** Laravel 13, Livewire 4 (`wire:navigate`), Tailwind 4,
GSAP 3, `@fontsource-variable/archivo`, Pest 4 och agent-browser för
kontroller i webbläsaren.

**Spec:** `docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`,
avsnittet "Fas 2". Läs också Begränsningar (adresser och copy) och beslut
1 och 9.

## Avvikelser från specen (Patrik kontrollerar)

1. **Bara kolofonen porteras i fas 2.** Specen vill ha rörelserna för hero,
   register, kolofon och fastnålade case här, men markupen för hero,
   register och case kommer först i fas 5. Fas 2 bygger livscykeln och
   kolofonen, eftersom kolofonen inte behöver projektdata. De andra
   rörelserna porteras i fas 5 tillsammans med sin markup.
2. **Kolofonen blir ett block nu.** Fas 5 bygger startsidan med block, och
   kolofonen behövs redan för att livscykeln ska gå att kontrollera. Den
   flyttas därför fram från fas 5.
3. **Adresserna saknar avslutande snedstreck.** `/se/om-mig/` och
   `/se/om-mig` ger båda sidan. På hostup skickar den vanliga
   Laravel-`.htaccess` en 301 från snedstrecksformen. Gamla länkar
   fungerar, men canonical byter form.

## Globala begränsningar

- PHP 8.5, Laravel 13, Filament 5, Livewire 4 och Tailwind 4 är redan
  installerade. Lägg inte till paket utöver `@fontsource-variable/archivo`
  (`^5.3.0`).
- **Databasen:**
  - Kör aldrig `migrate:fresh`, `migrate:refresh`, `migrate:reset`,
    `db:wipe` eller `db:seed --force`.
  - `php artisan migrate` och `php artisan db:seed --class=SitePagesSeeder`
    mot den lokala databasen är tillåtna, eftersom de bara lägger till.
- **Tester:**
  - Kör `php artisan config:clear` som ett eget kommando före den första
    testkörningen.
  - Testerna går mot sqlite `:memory:`, och `tests/TestCase.php` vägrar
    allt annat.
  - Kör `npm run build` innan testerna, eftersom layouten läser Vites
    manifest.
- Filer tas bort med `git rm <fil>`, en fil per sökväg. Hooken blockerar
  `rm`.
- Commit görs på `main` med uttryckliga sökvägar. Pusha inte.
- **Copy:**
  - Ingen påhittad copy. UI-text flyttas en till en från
    `~/Code/sjeren-2026/messages/se.json` och `en.json`, och `{year}` blir
    `:year`.
  - Text som saknas skrivs upp i `TODO-INNEHALL.md` i sjerencms.
  - WeUnite illustreras aldrig.
- sjeren-2026 läses men ändras inte. Klasser, mått och SVG-data kopieras
  från dess komponenter, och vilken fil står i varje uppgift.
- Kod och tester skrivs på engelska, i samma stil som koden runt omkring.
- Kontroller i webbläsaren:
  - Kör `agent-browser` mot `http://sjerencms.test` (Valet). Den gamla
    sajten finns på `http://sjeren-2026.test` som referens.
  - Skärmbilder sparas med absolut sökväg i
    `.superpowers/sdd/2026-10-23-fas-2-grund-i-nya-designen/evidence/`.
  - Skärmbilden ensam bevisar inget. Varje kontroll har ett `eval` med
    förväntat värde.

## Granskningsfokus

1. **Gamla adresser:** `/en/about-me/`, `/en/projects/`, `/en/services/`
   och `/en/reputation/`, samt alla adresser med avslutande snedstreck,
   ska ge 301 eller sidan och aldrig 404. Testas i uppgift 1 (`RoutingTest`).
2. **Läget följer med vid sidbyte och blinkar inte vid första målningen.**
   Uppgift 2 testar att skriptet ligger synkront i `<head>`, och uppgift 5
   testar läget i webbläsaren efter `wire:navigate`.
3. **Rörelser som läcker eller startar två gånger** efter upprepade sidbyten
   (ticker eller scroll-lyssnare som blir kvar). Uppgift 5 räknar aktiva
   rörelser efter fem sidbyten.
4. **En sida som bara finns på ett språk** ska inte ha en `hreflang` som
   pekar på en 404, och sidan på det andra språket ska ge 404. Testas i
   uppgift 1.
5. **Tomma inställningar:**
   - En tom länk i inställningarna döljer raden i footern.
   - En tom e-postadress döljer knappen i headern.
   - En blocktyp utan vy i `content` ger inte 500.
   Testas i uppgift 1, 3 och 4.

---

### Uppgift 1: Språk, routes, sidor, omdirigeringar och 404

**Files:**
- Create: `app/Http/Middleware/SetLocaleFromPrefix.php`
- Create: `app/Http/Controllers/ShowPage.php`
- Create: `database/factories/PageFactory.php`
- Create: `resources/views/components/layouts/site.blade.php`
- Create: `resources/views/pages/show.blade.php`
- Create: `resources/views/errors/404.blade.php`
- Create: `lang/sv/meta.php` och `lang/en/meta.php`
- Create: `TODO-INNEHALL.md`
- Modify: `routes/web.php`, `app/Models/Page.php`, `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Site/RoutingTest.php`

**Interfaces:**
- Produces:
  - `SetLocaleFromPrefix::LOCALES = ['se' => 'sv', 'en' => 'en']`. Det
    mappar URL-prefixet till appens locale och `Page.language`.
  - Routerna `page.home` (`{locale}`) och `page.show` (`{locale}/{slug}`).
    `URL::defaults(['locale' => …])` sätts, så att `route('page.show',
    ['slug' => 'om-mig'])` fungerar i vyerna.
  - `<x-layouts.site :title :description :alternates>` med `{{ $slot }}` i
    `<main>`. `$alternates` är `array<string,string>`, html-lang → absolut
    URL, och innehåller bara språk där sidan finns.
  - `Page::scopePublished()`. `PageFactory` med tillstånden `homepage()`,
    `unpublished()` och `english()`.

- [ ] **Steg 1: Skriv testerna** i `tests/Feature/Site/RoutingTest.php`:

```php
it('redirects the root to Swedish', fn () =>
    $this->get('/')->assertStatus(302)->assertRedirect('/se'));

it('redirects old English slugs', function (string $from, string $to) {
    $this->get($from)->assertStatus(301)->assertRedirect($to);
})->with([
    ['/en/about-me', '/en/om-mig'], ['/en/about-me/', '/en/om-mig'],
    ['/en/projects', '/en/projekt'], ['/en/services', '/en'], ['/en/reputation', '/en'],
]);

it('shows the home page in each language', function () {
    Page::factory()->homepage()->create();
    Page::factory()->homepage()->english()->create();

    $this->get('/se')->assertOk()->assertSee('<html lang="sv"', false);
    $this->get('/en/')->assertOk()->assertSee('<html lang="en"', false);
});

it('serves a page with and without trailing slash', function () {
    Page::factory()->create(['slug' => 'om-mig']);
    $this->get('/se/om-mig')->assertOk();
    $this->get('/se/om-mig/')->assertOk();
});

it('answers 404 for what does not exist', function (string $url) {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->unpublished()->create(['slug' => 'utkast']);
    Page::factory()->english()->create(['slug' => 'bara-engelska']);

    $this->get($url)->assertNotFound()->assertSee('Sidan finns inte');
})->with(['/de/om-mig', '/se/finns-inte', '/se/utkast', '/se/bara-engelska', '/se/Om-Mig']);

it('keeps admin and health routes reachable', function () {
    $this->get('/admin/login')->assertOk();
    $this->get('/up')->assertOk();
});

it('falls back to the default title and description', function () {
    Page::factory()->create(['slug' => 'om-mig', 'meta_title' => null, 'meta_description' => null]);
    $this->get('/se/om-mig')
        ->assertSee('<title>Patrik Sjerén — Design och kod</title>', false)
        ->assertSee('UI/UX-designer och frontend-utvecklare i Malmö.');
});

it('links alternates only to languages where the page exists', function () {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->english()->create(['slug' => 'om-mig']);
    Page::factory()->create(['slug' => 'bara-svenska']);

    $this->get('/se/om-mig')
        ->assertSee('rel="canonical" href="'.url('/se/om-mig').'"', false)
        ->assertSee('hreflang="en" href="'.url('/en/om-mig').'"', false);
    $this->get('/se/bara-svenska')->assertDontSee('hreflang="en"', false);
});

it('renders a page holding a block type that has no view', function () {
    Page::factory()->create(['slug' => 'gammal', 'content' => [['type' => 'logo_showcase', 'data' => []]]]);
    $this->get('/se/gammal')->assertOk();
});

it('loads Livewire and tracks assets for wire:navigate', function () {
    Page::factory()->homepage()->create();
    expect($this->get('/se')->getContent())
        ->toMatch('#<script[^>]+livewire[^>]*\.js#')
        ->toContain('data-navigate-track');
});
```

- [ ] **Steg 2: Kör.** `./vendor/bin/pest tests/Feature/Site/RoutingTest.php`.
  Förväntat: allt fallerar (routes, factory och vyer saknas).

- [ ] **Steg 3: Implementera.**
  - **`routes/web.php`** ska vara exakt:

    ```php
    Route::redirect('/', '/se');
    Route::permanentRedirect('en/about-me', '/en/om-mig');
    Route::permanentRedirect('en/projects', '/en/projekt');
    // Decision 9: targets that never existed go to the home page.
    Route::permanentRedirect('en/services', '/en');
    Route::permanentRedirect('en/reputation', '/en');

    Route::prefix('{locale}')->whereIn('locale', array_keys(SetLocaleFromPrefix::LOCALES))
        ->middleware(SetLocaleFromPrefix::class)->group(function () {
            Route::get('/', ShowPage::class)->name('page.home');
            Route::get('{slug}', ShowPage::class)->where('slug', '[a-z0-9-]+')->name('page.show');
        });
    ```
  - **`SetLocaleFromPrefix::handle`** sätter `app()->setLocale(LOCALES[$prefix])`
    och `URL::defaults(['locale' => $prefix])`. Ändra inte `config/app.php`,
    eftersom admin ska ligga kvar på engelska.
  - **`ShowPage::__invoke(string $locale, ?string $slug = null): View`**:
    - Slå upp sidan med `Page::published()->language(...)`, plus
      `where('is_homepage', true)` utan slug eller `where('slug', $slug)`
      med slug.
    - `abort(404)` om sidan inte finns.
    - Bygg `$alternates` med samma fråga för båda språken.
    - Titeln är `meta_title ?: __('meta.title')` och beskrivningen
      `meta_description ?: __('meta.description')`.
  - **`Page`:**
    - Casta `is_published` och `is_homepage` till `boolean`.
    - Lägg till `HasFactory` och `scopePublished()`.
    - Factoryns standard är `language` `sv`, publicerad, inte startsida,
      och `title`/`slug` från faker.
  - **Layouten** innehåller i `<head>`:
    - `<meta charset>`, viewport, `<title>`, description och canonical,
      som är `url()->current()`.
    - En `hreflang`-länk per post i `$alternates`.
    - `@vite(['resources/css/app.css'])` och `@livewireStyles`.

    I `<body class="font-sans">` ligger `<main>{{ $slot }}</main>` och
    `@livewireScripts`. `<html lang>` hämtas från `app()->getLocale()`.
  - **`AppServiceProvider::boot`**: `Vite::useScriptTagAttributes(['data-navigate-track' => true])`
    och `Vite::useStyleTagAttributes(['data-navigate-track' => true])`.
  - **`pages/show.blade.php`**: för varje block i `content`,
    `@includeIf('blocks.'.$block['type'], ['data' => $block['data'] ?? []])`.
  - **`errors/404.blade.php`** är en egen, fristående `<html lang="sv">`,
    som `not-found.tsx`. Den visar `<h1>404</h1>` och "Sidan finns inte."
    med länken "Till startsidan" till `/se`.
  - **`lang/*/meta.php`**: `title` och `description`, kopierade från
    `Meta` i `messages/*.json`.
  - **`TODO-INNEHALL.md`** får en rubrik och en första rad: "404-sidan: bara
    svensk text och ingen design, samma som i sjeren-2026
    (`TODO-INNEHALL.md` där)."

- [ ] **Steg 4: Kör igen.** `./vendor/bin/pest`. Förväntat: allt grönt,
  också `tests/Feature/Admin`.

- [ ] **Steg 5: Commit.** Ta med alla filer i listan ovan:
  `Add locale routing, page lookup, redirects and site layout`.

### Uppgift 2: Tokens, typsnitt och läget före första målningen

**Files:**
- Modify: `resources/css/app.css` (ersätts helt), `package.json`, `vite.config.js`, `resources/views/components/layouts/site.blade.php`
- Create: `resources/js/app.js`, `resources/js/theme.js`
- Create: `resources/views/components/theme-toggle.blade.php`
- Create: `lang/sv/nav.php` och `lang/en/nav.php`
- Test: `tests/Feature/Site/ThemeTest.php`

**Interfaces:**
- Consumes: layouten från uppgift 1.
- Produces:
  - Tailwind-färgerna `paper`, `ink`, `ink-dim`, `ink-dimmer`, `hair`,
    `hair-mid`, `hair-strong`, `accent`, `mint`, `open` och `plate`, samt
    `rounded-pill` och klasserna `.gut`, `.lbl`, `.wave-link` och
    `.wave-clip`.
  - `<x-theme-toggle />`.
  - `resources/js/app.js` som enda JS-entry. Den importerar `./theme` och,
    från uppgift 5, `./motion`.
  - Hela `Nav`-namnrymden i `lang/*/nav.php`. Nycklarna är `work`, `about`,
    `playground`, `cta`, `theme`, `themeLight`, `themeDark` och `menu`.

- [ ] **Steg 1: Skriv testerna** i `ThemeTest.php`:

```php
beforeEach(fn () => Page::factory()->homepage()->create());

it('sets the theme in a blocking head script before any stylesheet', function () {
    $html = $this->get('/se')->getContent();
    $head = Str::between($html, '<head>', '</head>');

    expect($head)->toContain('<script>(function(){try{var t=localStorage.getItem("theme");')
        ->and(strpos($head, 'localStorage.getItem("theme")'))->toBeLessThan(strpos($head, 'rel="stylesheet"'));
});

it('renders the theme toggle with translated labels', function () {
    app()->setLocale('sv');
    expect(Blade::render('<x-theme-toggle />'))
        ->toContain('aria-label="Byt läge"')->toContain('data-theme-pick="light"')
        ->toContain('aria-label="Ljust läge"')->toContain('aria-label="Mörkt läge"');
    app()->setLocale('en');
    expect(Blade::render('<x-theme-toggle />'))->toContain('aria-label="Light mode"');
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL, eftersom skriptet och komponenten saknas.

- [ ] **Steg 3: Implementera.**
  - **`app.css`** blir `~/Code/sjeren-2026/src/app/globals.css` rad för rad:
    `@import "tailwindcss";`, `:root`, `[data-theme="dark"]`, mobil-gutter,
    `@theme inline`, `@layer base`, `@layer components` och
    reduced-motion.
    - Två ändringar. Den första: lägg till
      `@import "@fontsource-variable/archivo";` direkt efter
      `@import "tailwindcss";`.
    - Den andra: `--font-sans: "Archivo Variable", "Helvetica Neue", Arial, sans-serif;`.
    - Den variabla axeln behövs, eftersom heron animerar vikten i fas 5.
  - **Skriptet i layoutens `<head>`** ligger före `@vite`. Det är strängen
    ur `ThemeScript.tsx` ordagrant, i en vanlig `<script>` utan
    `type="module"`, `defer` eller `async`.
  - **`@vite`** får `resources/js/app.js` tillagd, och samma sökväg läggs i
    `vite.config.js` `input`.
  - **`theme.js`:**
    - `apply(theme)` sätter `document.documentElement.dataset.theme` och
      synkar `aria-pressed` och `data-on` på alla `[data-theme-pick]`.
    - En klicklyssnare på `document` delegerar till `[data-theme-pick]`.
      Den kör `apply` och sedan `localStorage.setItem("theme", …)` i
      `try`.
    - På `livewire:navigating`: `e.detail.onSwap(() => apply(current))`,
      eftersom Livewire rekommenderar det mot blink i mörkt läge.
    - På `livewire:navigated` synkas knapparna.
  - **`theme-toggle.blade.php`** är markupen och klasserna ur
    `ThemeToggle.tsx`. Knapparna får `data-theme-pick="light|dark"`.
    Etiketterna kommer från `__('nav.theme')`, `__('nav.themeLight')` och
    `__('nav.themeDark')`.
  - **`lang/*/nav.php`** är `Nav` ur `messages/*.json`, en till en.

- [ ] **Steg 4: Kör.** Kör `npm install @fontsource-variable/archivo@^5.3.0`
  och sedan `npm run build`. Förväntat: manifestet har
  `resources/js/app.js`. Kör därefter `./vendor/bin/pest`. Förväntat: allt
  grönt.

- [ ] **Steg 5: Kontroll i webbläsaren.** Seeda först lokalt med
  `php artisan tinker --execute 'App\Models\Page::firstOrCreate(["slug" => "start", "language" => "sv"], ["title" => "Patrik Sjerén", "is_homepage" => true, "is_published" => true]);'`.
  Uppgift 5 ersätter det med en seeder. Kör sedan:

  ```bash
  agent-browser set media light && agent-browser open http://sjerencms.test/se
  agent-browser eval "getComputedStyle(document.documentElement).backgroundColor"   # rgb(237, 236, 234)
  agent-browser eval "localStorage.setItem('theme','dark')" && agent-browser reload
  agent-browser eval "[document.documentElement.dataset.theme, getComputedStyle(document.documentElement).backgroundColor].join()"  # dark,rgb(17, 24, 32)
  agent-browser eval "localStorage.clear()" && agent-browser set media dark && agent-browser reload
  agent-browser eval "document.documentElement.dataset.theme"   # dark
  agent-browser eval "getComputedStyle(document.body).fontFamily"  # börjar med "Archivo Variable"
  ```

  Förväntat: värdena i kommentarerna.

- [ ] **Steg 6: Commit:** `Port design tokens, Archivo and the theme script`.

### Uppgift 3: Header med ordbild, nav, språkbyte och kontaktinställningar

**Files:**
- Create: `resources/views/components/wordmark.blade.php`, `site-header.blade.php` och `locale-switch.blade.php` under `resources/views/components/`
- Create: `lang/sv/bar.php` och `lang/en/bar.php`
- Create: `database/settings/2026_10_24_000000_add_contact_links_to_general_settings.php`
- Modify: `app/Settings/GeneralSettings.php` (`public ?string $bandcamp_url = null;`), `app/Filament/Pages/ManageSettings.php` (fältet `bandcamp_url` under Social Media, samma form som `youtube_url`), layouten (headern först i `<body>`)
- Test: `tests/Feature/Site/HeaderTest.php`

**Interfaces:**
- Consumes: `page.home`, `page.show`, `SetLocaleFromPrefix::LOCALES`, `<x-theme-toggle />` och `nav.*`.
- Produces:
  - `<x-wordmark class="…" />`.
  - `GeneralSettings::$email`, `$linkedin_url`, `$facebook_url`,
    `$youtube_url` och `$bandcamp_url`, ifyllda med Patriks riktiga
    adresser. Uppgift 4 läser dem.

- [ ] **Steg 1: Skriv testerna** i `HeaderTest.php`:

```php
beforeEach(function () {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->english()->create(['slug' => 'om-mig']);
});

it('links the wordmark and nav with wire:navigate', function () {
    expect($this->get('/se/om-mig')->getContent())
        ->toContain('aria-label="Patrik Sjerén"')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se'), '#').'"[^>]*wire:navigate#')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/projekt'), '#').'"[^>]*wire:navigate[^>]*>\s*Projekt#')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/om-mig'), '#').'"[^>]*wire:navigate[^>]*>\s*Om mig#');
});

it('switches language on the same path', function () {
    expect($this->get('/se/om-mig')->getContent())
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/en/om-mig'), '#').'"[^>]*hreflang="en"#')
        ->toMatch('#aria-current="true"[^>]*>\s*SV#');
});

it('shows the bar and the call to action from settings', function () {
    $this->get('/se/om-mig')
        ->assertSee("Portfolio '26 — Malmö, Sverige")
        ->assertSee('Tillgänglig för uppdrag / anställning')
        ->assertSee('href="mailto:patrik.sjeren@gmail.com"', false)
        ->assertSee('Boka ett samtal');
    $this->get('/en/om-mig')->assertSee('Available for contract or full-time')->assertSee('Book a call');
});

it('hides the call to action when no email is set', function () {
    $settings = app(GeneralSettings::class);
    $settings->email = '';
    $settings->save();

    $this->get('/se/om-mig')->assertDontSee('mailto:', false);
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **`wordmark.blade.php`** är SVG:n ur `Wordmark.tsx`, med samma
    `viewBox`, samma `path` ordagrant, `fill="currentColor"`,
    `role="img"` och `aria-label="Patrik Sjerén"`. Attributen slås ihop
    med `$attributes`.
  - **`site-header.blade.php`** är markupen och klasserna ur `Header.tsx`.
    - Navlänkarna är `route('page.show', ['slug' => 'projekt'])` och
      `'om-mig'` med `wire:navigate`. Ordbilden länkar till
      `route('page.home')`.
    - Mobilmenyn är samma `<details>`.
    - CTA:n är `mailto:{settings.email}` och renderas bara om e-posten
      inte är tom.
  - **`locale-switch.blade.php`:**
    - Ta resten av sökvägen efter prefixet ur `request()->segments()`.
    - Rendera en länk per post i `LOCALES` med `url("/{$prefix}/{$rest}")`.
    - Etiketterna är `SV` och `EN`, med `hreflang` `sv` och `en`.
    - Det aktiva språket får `aria-current="true"`, de andra `text-ink-dimmer hover:text-accent`.
    - Alla länkar har `wire:navigate`.
  - **Settings-migrationen** lägger till `general.bandcamp_url` som
    `https://patriksjeren.bandcamp.com/`. Med `$this->migrator->update(...)`
    sätter den:

    | Nyckel | Värde |
    |---|---|
    | `general.email` | `patrik.sjeren@gmail.com` |
    | `general.linkedin_url` | `https://www.linkedin.com/in/patrik-sjeren/` |
    | `general.facebook_url` | `https://www.facebook.com/sjeren` |
    | `general.youtube_url` | `https://www.youtube.com/@patriksjeren/` |

    Värdena kommer från `Footer.tsx` och `Header.tsx`.
  - **`lang/*/bar.php`** är `Bar` ur `messages/*.json`.

- [ ] **Steg 4: Kör.** Kör `./vendor/bin/pest` och sedan `php artisan migrate`
  mot den lokala databasen. Förväntat: testerna är gröna och migrationen
  är `DONE`.

- [ ] **Steg 5: Commit:** `Add site header with wordmark, nav, locale switch and contact settings`.

### Uppgift 4: Footer, vågen och sektionshuvudet

**Files:**
- Create: `resources/views/components/site-footer.blade.php`, `wave-underline.blade.php` och `section-head.blade.php`
- Create: `lang/sv/footer.php` och `lang/en/footer.php`
- Modify: layouten (footern efter `<main>`)
- Test: `tests/Feature/Site/FooterTest.php`

**Interfaces:**
- Consumes: `GeneralSettings` från uppgift 3.
- Produces:
  - `<x-section-head number="01" label="…" aside="…" />`. `aside` är
    valfri.
  - `<x-wave-underline />` läggs i en länk med `wave-link` och
    `border-b`.

- [ ] **Steg 1: Skriv testerna** i `FooterTest.php`:

```php
beforeEach(fn () => Page::factory()->homepage()->create());

it('shows the headline and every contact link from settings', function () {
    $html = $this->get('/se')->getContent();
    expect($html)->toContain('Har du något på gång?')->toContain('Kontakta mig!');
    foreach ([
        'mailto:patrik.sjeren@gmail.com' => 'patrik.sjeren@gmail.com',
        'https://www.linkedin.com/in/patrik-sjeren/' => 'LinkedIn',
        'https://www.facebook.com/sjeren' => 'Facebook',
        'https://www.youtube.com/@patriksjeren/' => 'YouTube',
        'https://patriksjeren.bandcamp.com/' => 'Bandcamp',
    ] as $href => $label) {
        expect($html)->toMatch('#<a[^>]+href="'.preg_quote($href, '#').'"[^>]*>\s*'.preg_quote($label, '#').'#');
    }
    expect($html)->toMatch('#href="https://www\.linkedin\.com[^"]*"[^>]*target="_blank"[^>]*rel="noreferrer"#');
});

it('hides a contact row whose url is empty', function () {
    $settings = app(GeneralSettings::class);
    $settings->youtube_url = null;
    $settings->save();

    $this->get('/se')->assertDontSee('YouTube');
});

it('prints the current year and place', function () {
    Page::factory()->homepage()->english()->create();
    $this->travelTo('2027-03-01');

    $this->get('/se')->assertSee('© 2027 Patrik Sjerén')->assertSee('Malmö · Sverige');
    $this->get('/en')->assertSee('Malmö · Sweden');
});

it('renders the section head with and without aside', function () {
    expect(Blade::render('<x-section-head number="01" label="Utvalda case" aside="Tre av 16" />'))
        ->toContain('>01<')->toContain('w-[26px]')->toMatch('#<h2[^>]*>\s*Utvalda case#')->toContain('Tre av 16');
    expect(Blade::render('<x-section-head number="03" label="Tjänster" />'))->not->toContain('ml-auto');
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **`site-footer.blade.php`** är markupen och klasserna ur `Footer.tsx`.
    - Raderna är `email`, `linkedin`, `facebook`, `youtube` och
      `bandcamp`, i den ordningen.
    - `href` kommer från inställningarna. E-posten blir `mailto:`.
    - Etiketten är `__('footer.<nyckel>')`.
    - Externa länkar får `target="_blank" rel="noreferrer"`.
    - En rad utan värde renderas inte.
    - Copyrighten är `__('footer.copyright', ['year' => now()->year])`.
  - **`wave-underline.blade.php`** är markupen och båda `path`-strängarna
    ur `WaveUnderline.tsx`, ordagrant. CSS:en finns redan i `app.css` från
    uppgift 2.
  - **`section-head.blade.php`** är `SectionHead.tsx`.
  - **`lang/*/footer.php`** är `Footer` ur `messages/*.json`, där
    `{year}` blir `:year`.

- [ ] **Steg 4: Kör.** `npm run build && ./vendor/bin/pest`. Förväntat: allt grönt.

- [ ] **Steg 5: Commit:** `Add site footer, wave underline and section head`.

### Uppgift 5: GSAP-livscykeln och kolofonen som block

**Files:**
- Create: `resources/js/motion.js`, `resources/js/motions/colophon.js`
- Create: `app/Filament/Forms/Components/Blocks/ColophonBlock.php`
- Create: `resources/views/blocks/colophon.blade.php`
- Create: `database/seeders/SitePagesSeeder.php`
- Modify: `resources/js/app.js`, `app/Filament/Forms/Components/ContentBlocksSection.php`, `tests/Feature/Admin/PageBlocksTest.php` (lägg `colophon` i `everyBlock()`)
- Test: `tests/Feature/Site/ColophonTest.php`, `tests/Feature/Site/SitePagesSeederTest.php`

**Interfaces:**
- Consumes: `@includeIf('blocks.'.$type, ['data' => …])` från uppgift 1 och `app.js` från uppgift 2.
- Produces:
  - `defineMotion(name: string, init: (el: HTMLElement) => (() => void) | void): void`
    i `motion.js`. Ett element med `data-motion="<name>"` får sin
    `init` vid varje `livewire:navigated`, vilket också gäller första
    laddningen. Upprensningen körs vid `livewire:navigating`.
  - `document.documentElement.dataset.motionActive` är antalet rörelser som
    körs just nu. Det är kontrollpunkten i webbläsaren, och fas 5 bygger
    vidare på den.
  - Blocket `colophon` med `data.label` och `data.tools`, där verktygen
    skiljs åt med `·`.

- [ ] **Steg 1: Skriv testerna.**

```php
// ColophonTest.php
it('renders the colophon band with the tools twice', function () {
    Page::factory()->homepage()->create(['content' => [[
        'type' => 'colophon',
        'data' => ['label' => 'Verktyg', 'tools' => 'Figma · Laravel · GSAP'],
    ]]]);

    $html = $this->get('/se')->getContent();
    expect($html)->toContain('data-motion="colophon"')->toContain('Verktyg')
        ->and(substr_count($html, '>Laravel<'))->toBe(2)
        ->and($html)->toContain('aria-hidden="true" class="flex shrink-0');
});

// SitePagesSeederTest.php
it('seeds the six site pages idempotently', function () {
    $this->seed(SitePagesSeeder::class);
    $this->seed(SitePagesSeeder::class);

    expect(Page::count())->toBe(6);
    foreach (['/se', '/en', '/se/om-mig', '/en/om-mig', '/se/projekt', '/en/projekt'] as $url) {
        $this->get($url)->assertOk();
    }
    $this->get('/en/om-mig')->assertSee('<title>About — Patrik Sjerén</title>', false);
});
```

- [ ] **Steg 2: Kör.** Förväntat: FAIL.

- [ ] **Steg 3: Implementera.**
  - **`motion.js`:**
    - Håll en `Map` från namn till `init` och en lista med upprensningar.
    - `start()` kör `init` för varje `[data-motion]` som har en
      registrerad rörelse, sparar returvärdet och sätter `motionActive`.
    - `stop()` kör alla upprensningar, tömmer listan och sätter
      `motionActive` till `0`.
    - Koppla `start` till `livewire:navigated` och `stop` till
      `livewire:navigating`.
    - Använd ingen `DOMContentLoaded`, eftersom `livewire:navigated`
      redan körs vid första laddningen och rörelserna annars startar två
      gånger.
  - **`motions/colophon.js`** kör `defineMotion('colophon', el => …)` med
    kroppen ur `useLayoutEffect` i `ToolMarquee.tsx`, ordagrant:
    - `DRIFT = 55`, `MAX = 900` och `PUSH = 0.35`.
    - `gsap.matchMedia` med `(prefers-reduced-motion: no-preference)`.
    - `gsap.ticker` och scroll-lyssnaren.
    - Den returnerar `() => mm.revert()`.

    `el` är spåret, alltså elementet som bär `data-motion`.
  - **`blocks/colophon.blade.php`** är JSX:en ur `ToolMarquee.tsx` med samma
    klasser. Spåret får `data-motion="colophon"`, och
    `explode('·', $data['tools'])` trimmas och filtreras.
  - **`ColophonBlock::make(?string $name = 'colophon')`** har två fält:
    - `TextInput` `label`, obligatoriskt.
    - `Textarea` `tools`, obligatoriskt, med hjälptexten "Separate tools with ·".

    Registrera blocket i `ContentBlocksSection`.
  - **`SitePagesSeeder`** använder `updateOrCreate` på `slug` och
    `language`, och skapar sex sidor:

    | Sida | title | meta_title | meta_description | Innehåll |
    |---|---|---|---|---|
    | Startsida | "Patrik Sjerén" | – | – | ett `colophon`-block med `Colophon.label` och `Colophon.tools` |
    | `om-mig` | `Nav.about` | `About.title` | `About.description` | – |
    | `projekt` | `Nav.work` | `Projects.title` | `Projects.description` | – |

    Startsidan har `is_homepage`. Varje sida finns på sv och en, med texten
    kopierad ordagrant ur `messages/*.json`. Allt är publicerat.

- [ ] **Steg 4: Kör.** `npm run build && ./vendor/bin/pest`. Förväntat: allt
  grönt. Kör sedan `php artisan db:seed --class=SitePagesSeeder` lokalt.

- [ ] **Steg 5: Kontroll i webbläsaren.** Gör en kontroll per anrop, och
  läs om läget efter en `os error 35`. Mät mot `http://sjerencms.test`.
  1. Kör `set media light`, öppna `/se` och `eval "document.documentElement.dataset.motionActive"`.
     Förväntat: `"1"`.
  2. Kör `eval "(async()=>{const t=document.querySelector('[data-motion=colophon]');const x=()=>new DOMMatrix(getComputedStyle(t).transform).m41;const a=x();await new Promise(r=>setTimeout(r,1000));return Math.round(a-x())})()"`.
     Förväntat: 45–65. Bandet driver alltså med cirka 55 px/s.
  3. Sätt `eval "window.__probe=1"`. Klicka på `Om mig` med
     `find text "Om mig" click` och läs sedan
     `eval "[window.__probe, document.documentElement.dataset.motionActive].join()"`.
     Förväntat: `1,0`. Sidan laddades inte om och rörelsen stoppades.
  4. Gå mellan startsidan och Om mig fem gånger via ordbilden och navlänken.
     På startsidan ska `motionActive` vara `"1"` och steg 2 ge 45–65.
  5. Klicka på knappen för mörkt läge och sedan på `Projekt`. Läs
     `dataset.theme` och bakgrundsfärgen. Förväntat: `dark,rgb(17, 24, 32)`.
  6. Klicka på `EN` på `/se/om-mig`. Läs `[location.pathname, document.documentElement.lang]`.
     Förväntat: `/en/om-mig,en`.
  7. Kör `set media light reduced-motion` och ladda om `/se`. Läs
     `getComputedStyle(track).transform` efter en sekund. Förväntat: `none`.
  8. Jämför med den gamla sajten. Kör `set viewport 1440 900` och läs
     `document.querySelector('header').getBoundingClientRect().height` och
     `getComputedStyle(document.querySelector('footer h2')).fontSize` på
     `http://sjerencms.test/se` och `http://sjeren-2026.test/se/`.
     Förväntat: lika, ±1 px. Gör samma sak vid `set viewport 390 844`.
  9. Ta skärmbilder i ljust och mörkt läge, vid 1440 och 390 px, för
     båda sajterna. Spara dem i evidence-katalogen som
     `<sajt>-<läge>-<bredd>.png`. Kör `git status` efteråt.
     Förväntat: inga nya filer i repot.

- [ ] **Steg 6: Commit:** `Add GSAP motion lifecycle and the colophon block`.

### Uppgift 6: Dokumentation

**Files:** `CLAUDE.md`, `TODO-INNEHALL.md`

- [ ] **Steg 1: Uppdatera `CLAUDE.md`.**
  - Ersätt "Phase 2 adds a Blade view per block" med: block får en vy i
    `resources/views/blocks/` när de får den nya designen, och kolofonen
    var först.
  - Beskriv `routes/web.php` med prefix och omdirigeringar,
    `lang/{sv,en}`, layoutkomponenterna, `defineMotion` och kontrollen med
    `motionActive`.
  - Beskriv hur kontrollen i webbläsaren går till: Valet-adresserna, att
    seedern körs med `--class` och var skärmbilderna sparas.
- [ ] **Steg 2:** Kontrollera att `TODO-INNEHALL.md` har med allt som blev
  platshållare under uppgift 1–5. Innehåller den bara 404-raden, skriv det
  i commit-meddelandet.
- [ ] **Steg 3: Commit:** `Document the site foundation`.
