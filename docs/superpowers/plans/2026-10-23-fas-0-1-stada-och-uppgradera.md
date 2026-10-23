# Fas 0 och 1: städa och uppgradera, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** sjerencms utan Vue, SSR och död kod, och på Laravel 13, Filament 5,
Livewire 4 och Tailwind 4, med smoketester som är gröna efter varje steg.

**Arkitektur:** Först skrivs smoketester mot admin som det ser ut idag.
Sedan tas allt bort som den nya designen inte använder. Därefter
uppgraderas ramverken ett steg i taget, och testerna ska vara gröna efter
varje steg innan nästa påbörjas. Ingen ny funktionalitet byggs i den här
planen. Publika sidan ger 404 tills fas 2.

**Tech stack:** PHP 8.5, Laravel 11 → 13, Filament 3 → 5, Livewire 3 → 4,
Tailwind 3 → 4, Pest 3 → 4, Vite, Spatie medialibrary/settings.

**Spec:** `docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`
(avsnitten "Fas 0" och "Fas 1").

## Globala begränsningar

- PHP 8.5 lokalt och på hostup. Laravel 13 kräver 8.3 eller senare.
- Filament 5 kräver Livewire 4.0+ och Tailwind CSS 4.0+. Filament 4 kräver
  Tailwind 4.1+ eftersom appen har ett eget tema
  (`resources/css/filament/admin/theme.css`).
- Ingen Node-process i drift. `npm run build` ska bara bygga statiska
  tillgångar, ingen SSR-bundle.
- Kompatibilitet kontrolleras med `composer why-not <paket> <version>`. Den
  tas inte ur minnet. Säger Composer att ordningen måste ändras, följ
  Composer och skriv upp varför i commit-meddelandet.
- Commit bara med uttryckliga sökvägar (`git add <sökvägar>`), aldrig
  `git add -A` eller `git commit -a`.
- Pusha inte. Patriks regel gäller före `AGENTS.md`.
- kalibr (`~/Code/kalibr`) och sjeren-2026 rörs inte.
- Kommentarer och namn följer stilen i koden runt omkring. Testerna skrivs
  på engelska som resten av Laravel-koden.

## Granskningsfokus

Fel som ingen uppgift testar av sig själv. Varje rad har fått ett test eller
en kontroll i den uppgift som äger koden.

1. **Migrationer som går på SQLite men inte på MySQL** (driften kör MySQL).
   Uppgift 8 kör `migrate:fresh` mot den lokala MySQL-databasen.
2. **Admin blir utan stil när temat inte byggs om** efter bytet till
   Tailwind 4. Uppgift 5 och 6 kontrollerar att `npm run build` går igenom
   och att `public/build/manifest.json` innehåller
   `resources/css/filament/admin/theme.css`.
3. **En kvarvarande referens till en borttagen klass** (en widget i
   panelprovidern, ett block i `ContentBlocksSection`) ger 500 i admin.
   `AdminAccessTest` i uppgift 1 fångar det på `/admin`.
4. **Inställningssidan sparar ett fält som saknas i `GeneralSettings`**
   (`youtube_url` finns i formuläret men inte i klassen). Uppgift 1 täcker
   det med `SettingsTest`.
5. **En sida med block som sparats före uppgraderingen går inte att öppna
   efter.** Filament 4 flyttade layoutkomponenterna (till exempel
   `Section`). `PageResourceTest` i uppgift 1 sparar ett block och öppnar
   redigeringssidan igen, och testet körs efter varje steg.

---

### Uppgift 0: Ren arbetskopia (Patrik bestämmer)

Arbetskopian har ändringar som inte är den här planens:
`D .claude/settings.local.json` (köad borttagning), ändrad `.gitignore`
(+8 rader) och ändrad `resources/css/app.css` (+17 rader).

- [ ] **Steg 1:** Visa `git status --short` och `git diff .gitignore resources/css/app.css` för Patrik och fråga vad som ska göras med varje fil. Det kan vara att committa, stasha eller slänga. `app.css` byts ut helt i uppgift 2.
- [ ] **Steg 2:** Gör det han säger. Committa sedan specen, planen och källmaterialet så att seeddatan inte kan försvinna i städningen:
```bash
git add docs/superpowers docs/design/musik database/seeders/data
git commit -m "Add spec, phase 0-1 plan and music design sources" -- docs/superpowers docs/design/musik database/seeders/data
```
Kör `git status --short`. Förväntat: inget kvar.
- [ ] **Steg 3:** Fråga om arbetet ska göras på `main` eller på en egen gren, och gör som han säger.

### Uppgift 1: Smoketester mot admin som det är idag

**Files:**
- Modify: `phpunit.xml:25-26` (SQLite i minnet)
- Modify: `tests/Pest.php` (`RefreshDatabase` för Feature)
- Create: `tests/Feature/Admin/AdminAccessTest.php`
- Create: `tests/Feature/Admin/ResourceIndexTest.php`
- Create: `tests/Feature/Admin/PageResourceTest.php`
- Create: `tests/Feature/Admin/SettingsTest.php`
- Delete: `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`

**Interfaces:**
- Produces: testsviten `./vendor/bin/pest tests/Feature/Admin` som alla
  senare uppgifter kör som grind.

- [ ] **Steg 1: Testmiljö.** Avkommentera `DB_CONNECTION=sqlite` och `DB_DATABASE=:memory:` i `phpunit.xml`. Lägg `->use(Illuminate\Foundation\Testing\RefreshDatabase::class)` på `pest()->extend(...)->in('Feature')` i `tests/Pest.php`. Ta bort exempeltesterna och exempelhelpern `something()`.

- [ ] **Steg 2: Skriv testerna.**

```php
// AdminAccessTest.php
it('redirects guests to the admin login', fn () =>
    $this->get('/admin')->assertRedirect('/admin/login'));

it('shows the dashboard to a logged in user', fn () =>
    $this->actingAs(User::factory()->create())->get('/admin')->assertOk());
```

```php
// ResourceIndexTest.php — listan krymper i uppgift 2
it('renders every resource index', function (string $url) {
    $this->actingAs(User::factory()->create())->get($url)->assertOk();
})->with(['/admin/pages', '/admin/posts', '/admin/categories',
          '/admin/products', '/admin/product-categories', '/admin/manage-settings']);
```

```php
// PageResourceTest.php
it('saves a page with a headline_text block and reopens it', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Om mig', 'slug' => 'om-mig', 'language' => 'sv',
            'content' => [[
                'type' => 'headline_text',
                'data' => ['container_type' => <första giltiga option>,
                           'headline' => 'Rubrik', 'text' => 'Brödtext'],
            ]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $page = Page::where('slug', 'om-mig')->sole();
    expect($page->content[0]['data']['headline'])->toBe('Rubrik');

    $this->get("/admin/pages/{$page->id}/edit")->assertOk()->assertSee('Rubrik');
});
```

```php
// SettingsTest.php
it('saves general settings', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageSettings::class)
        ->fillForm(['site_name' => 'Patrik Sjerén', 'tagline' => 'Portfolio',
                    'navigation_type' => <första giltiga option>,
                    'youtube_url' => 'https://youtube.com/@x'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(GeneralSettings::class)->refresh()->site_name)->toBe('Patrik Sjerén');
});
```

Läs de faktiska URL:erna med `php artisan route:list --path=admin` och
de giltiga värdena för `container_type` och `navigation_type` i
`HeadlineTextBlock.php` och `ManageSettings.php`. Fyll i dem, gissa inte.

- [ ] **Steg 3: Kör.** `./vendor/bin/pest tests/Feature/Admin`. Det som fallerar beror på befintliga fel. Laga dem på det här sättet och inget annat:
  - Inställningstabellen saknas: lägg till `'migrations_paths' => [database_path('settings')]` i `config/settings.php`.
  - Två settingsmigrationer heter `create_general_settings` och den tomma (`2024_10_31_211007`) krockar: ta bort den tomma.
  - `youtube_url` saknas: lägg till `public ?string $youtube_url = null;` i `GeneralSettings` och en settingsmigration som lägger till `general.youtube_url` med värdet `null`. Footern i den nya designen länkar till YouTube.
  - Migrationer som är MySQL-specifika: skriv om dem så att de fungerar på båda databaserna. Det går eftersom databasen börjar om från noll.

- [ ] **Steg 4: Kör igen.** `./vendor/bin/pest tests/Feature/Admin`. Förväntat: alla gröna.

- [ ] **Steg 5: Commit.**
```bash
git add phpunit.xml tests/ config/settings.php app/Settings database/settings
git commit -m "Add admin smoke tests as upgrade gate"
```

### Uppgift 2: Ta bort Vue, SSR och det som inte följer med

**Files:**
- Delete: `resources/js/`, `node-server.js`, `server.js`, `app/helpers.php`, `resources/views/app.blade.php`, `resources/views/welcome.blade.php`, `tailwind.config.js`
- Delete: `app/Http/Controllers/{SsrController,ShopController,ContactFormController,SitemapController}.php`, `app/Http/Controllers/Api/`, `routes/api.php`, `app/Http/Kernel.php`, `app/Providers/RouteServiceProvider.php`, `app/Http/Resources/`, `app/Mail/ContactForm*.php` och dess vyer under `resources/views/emails/`
- Delete: modellerna `Post`, `Category`, `Product`, `ProductCategory`, `ProductAddon`, `Order`, `OrderItem`, `Download` med Filament-resurser, `PostSettingsSection.php` och widgeten `LatestPosts`
- Delete: blocken `LogoShowcase`, `TeamMembers`, `PricingTables`, `IconCards`, `BlogPosts`, `Carousel`, `Gallery`, `ColumnCards`, `ThreeColumnCards`, `Video`, `ContactForm`, `FullImage`, `PortfolioItems`, `ColumnsLayout`, samt `resources/views/blocks/contact-form.blade.php`
- Delete: migrationerna för `posts`, `categories`, `category_post`, `product_*`, `orders` och `order_items`, plus migrationer som bara rör `posts`
- Modify: migrationer som rör både `pages` och `posts` (`2024_11_01_201608_add_meta_fields_…`, `2025_02_09_170000_add_language_…`): stryk `posts`-delen
- Modify: `routes/web.php` (tom på routes; publika sidan ger 404 tills fas 2), `composer.json` (ta bort `files`-autoload för `app/helpers.php`), `app/Filament/Forms/Components/ContentBlocksSection.php`, `app/Providers/Filament/AdminPanelProvider.php`, `app/Filament/Widgets/StatsOverview.php` (bara `Page::count()`), `config/services.php` (ta bort Stripe-nyckeln om den finns där)
- Modify: `package.json` (ta bort `vue`, `pinia`, `vue-router`, `@vue/server-renderer`, `@vueuse/head`, `vue3-carousel`, `@iconify/vue`, `@stripe/stripe-js`, `@vitejs/plugin-vue`, `axios`, `concurrently`, `dompurify`, `marked`, `three`; behåll `gsap`; skripten `build` = `vite build`, ta bort `serve` och `ssr`), `vite.config.js` (bara `laravel()` med `resources/css/app.css` och temat, ingen `ssr`)
- Replace: `resources/css/app.css` med enbart `@tailwind base; @tailwind components; @tailwind utilities;` (fas 2 skriver den nya)
- Modify: `tests/Feature/Admin/ResourceIndexTest.php` (bara `/admin/pages` och inställningssidan)
- Modify: `composer.json`: ta bort `stripe/stripe-php`. Butiken byggs om i fas 4 och lägger då till det som behövs. Behåll `spatie/laravel-sitemap` tills vidare.

Behålls: blocken `HeadlineText`, `StaggerText`, `Ticker`, `FaqAccordion`, `CallToAction`, `ServiceCards`, `StickyCarousel`, `ImageText`, `FullText`, plus `resources/views/blocks/ticker.blade.php` och `resources/css/blocks/` om något kvarvarande pekar dit.

- [ ] **Steg 1:** Ta bort och ändra enligt listan.
- [ ] **Steg 2: Leta efter rester.** `grep -rnE "Post|Product|Category|Order|Download|ContactForm|node\(|Ssr" app config routes resources/views database --include=*.php`. Förväntat: inga träffar utom ord som råkar innehålla mönstret (till exempel `ValidatePostSize`, som försvinner med `Kernel.php`).
- [ ] **Steg 3:** `composer dump-autoload && npm install && npm run build`. Förväntat: bygget går igenom och `public/build/manifest.json` har nycklarna `resources/css/app.css` och `resources/css/filament/admin/theme.css`.
- [ ] **Steg 4:** `./vendor/bin/pest tests/Feature/Admin`. Förväntat: alla gröna.
- [ ] **Steg 5: Commit.** Ta med borttagna filer via `git add -u <katalog>` för varje berörd katalog och nya ändringar med uttryckliga sökvägar. Meddelande: `Remove Vue/SSR frontend, blog, old shop and unused blocks`.

### Uppgift 3: Laravel 11 → 12

**Files:** `composer.json`, `composer.lock`, och det som upgrade-guiden kräver.

- [ ] **Steg 1:** `composer why-not laravel/framework ^12.0`. Förväntat: inga blockerare. Vid blockerare, uppgradera blockeraren först eller skriv upp den.
- [ ] **Steg 2:** I `composer.json`: `laravel/framework ^12.0`, `phpunit/phpunit ^11.0` (om det står direkt), `pestphp/pest ^3.0`. Kör `composer update -W`.
- [ ] **Steg 3:** Läs [12.x upgrade guide](https://laravel.com/docs/12.x/upgrade) (eller Context7 `/websites/laravel_framework_12_x`) och sök i koden efter det som har hög och medelhög risk. `HasUuids` används inte i dag. Kontrollera med `grep -rn HasUuids app`.
- [ ] **Steg 4:** `php artisan optimize:clear && ./vendor/bin/pest tests/Feature/Admin`. Förväntat: alla gröna.
- [ ] **Steg 5: Commit** `composer.json composer.lock` och eventuella kodändringar: `Upgrade to Laravel 12`.

### Uppgift 4: Filament 3 → 4 och Tailwind 4

**Files:** `composer.json`, `composer.lock`, `app/Filament/**`, `app/Providers/Filament/AdminPanelProvider.php`, `resources/css/filament/admin/theme.css`, `resources/css/app.css`, `vite.config.js`, `package.json`, `postcss.config.js` (tas bort), `resources/css/filament/admin/tailwind.config.js` (tas bort), `tests/Feature/Admin/*.php` (nya klassnamn).

- [ ] **Steg 1:** `composer why-not filament/filament ^4.0`, och samma för `filament/spatie-laravel-media-library-plugin` och `filament/spatie-laravel-settings-plugin` med `^4.0`. Förväntat: inga blockerare. Om ett Spatie-plugin saknas för v4, stoppa och fråga Patrik.
- [ ] **Steg 2:** Kör uppgraderingsskriptet:
```bash
composer require filament/upgrade:"^4.0" -W --dev
vendor/bin/filament-v4
# kör de composer-kommandon som skriptet skriver ut
```
Om installationen fallerar: skriptet kräver PHPStan v2. Det är inte installerat idag, så det borde inte bli ett problem.
- [ ] **Steg 3:** Flytta till v4:s katalogstruktur. Kör `php artisan filament:upgrade-directory-structure-to-v4 --dry-run`, granska och kör sedan utan `--dry-run`. Det är vårt beslut, eftersom fas 3–5 lägger till nya resurser och strukturen inte ska vara blandad. Uppdatera importerna i `PageResourceTest`.
- [ ] **Steg 4: Tailwind 4.1.** Följ Filaments v4-guide för eget tema. `theme.css` importerar Filaments v4-tema och använder `@source` i stället för `@config`. Lägg till `@tailwindcss/vite` i `vite.config.js` och ta bort `postcss.config.js`, `autoprefixer` och temats `tailwind.config.js`. `app.css` blir `@import "tailwindcss";`. Ta bort `@tailwindcss/forms` och `@tailwindcss/typography` om inget kvarvarande använder dem.
- [ ] **Steg 5:** Läs Filaments v4-guide för det som skriptet inte hanterar och kontrollera de kvarvarande blocken och `*Section`-klasserna. Till exempel ärver `ContentBlocksSection` från `Section`, som har flyttat namnrymd.
- [ ] **Steg 6:** `npm run build`. Förväntat: bygget går igenom och manifestet har `resources/css/filament/admin/theme.css`. Kör sedan `php artisan optimize:clear && ./vendor/bin/pest tests/Feature/Admin`. Förväntat: alla gröna.
- [ ] **Steg 7:** `composer remove filament/upgrade --dev`. Kör testerna igen.
- [ ] **Steg 8: Commit:** `Upgrade to Filament 4 and Tailwind 4`.

### Uppgift 5: Filament 4 → 5 och Livewire 4

**Files:** `composer.json`, `composer.lock`, `app/Filament/**`, Blade-vyer med `wire:`-attribut.

- [ ] **Steg 1:** `composer why-not filament/filament ^5.0`, samma för de två Spatie-pluginen med `^5.0`, och `composer why-not livewire/livewire ^4.0`. Förväntat: inga blockerare. Om ett plugin saknas för v5, stoppa och fråga Patrik.
- [ ] **Steg 2:**
```bash
composer require filament/upgrade:"^5.0" -W --dev
vendor/bin/filament-v5
# kör de composer-kommandon som skriptet skriver ut
```
- [ ] **Steg 3: Livewire 4.** `config/livewire.php` finns inte i repot, så de omdöpta konfignycklarna påverkar inte. Leta efter `wire:model` på element som omsluter andra fält (`grep -rn "wire:model" resources app`) och lägg till `.deep` där det behövs. Läs [Livewire 4 upgrade guide](https://livewire.laravel.com/docs/4.x/upgrading) för resten.
- [ ] **Steg 4:** `npm run build && php artisan optimize:clear && ./vendor/bin/pest tests/Feature/Admin`. Förväntat: bygget går igenom och testerna är gröna.
- [ ] **Steg 5:** `composer remove filament/upgrade --dev`. Kör testerna igen.
- [ ] **Steg 6: Commit:** `Upgrade to Filament 5 and Livewire 4`.

### Uppgift 6: Laravel 12 → 13 och Pest 4

**Files:** `composer.json`, `composer.lock`, `tests/`.

- [ ] **Steg 1:** `composer why-not laravel/framework ^13.0` och `composer why-not pestphp/pest ^4.0`. Förväntat: inga blockerare.
- [ ] **Steg 2:** I `composer.json`: `laravel/framework ^13.0`, `laravel/tinker ^3.0`, `pestphp/pest ^4.0`, `pestphp/pest-plugin-laravel ^4.0`, och `phpunit/phpunit ^12.0` om det står direkt. Kör `composer update -W`.
- [ ] **Steg 3:** Läs [13.x upgrade guide](https://laravel.com/docs/13.x/upgrade) och åtgärda det som berör appen.
- [ ] **Steg 4:** `php artisan optimize:clear && ./vendor/bin/pest`. Förväntat: alla gröna.
- [ ] **Steg 5: Commit:** `Upgrade to Laravel 13 and Pest 4`.

### Uppgift 7: Städa dokumentationen

**Files:** `CLAUDE.md`, `README.md`.

- [ ] **Steg 1:** Skriv om `CLAUDE.md` så att den stämmer: Laravel 13, Filament 5, Livewire 4, Tailwind 4, ingen Vue, ingen SSR, `npm run build` utan SSR, och testkommandona. Ta bort avsnitten om Vue-block och `BlockRenderer.vue`. Skriv att block i fortsättningen renderas med Blade (fas 2) och hänvisa till specen.
- [ ] **Steg 2:** Rätta motsvarande stycken i `README.md`.
- [ ] **Steg 3: Commit:** `Update docs for Laravel 13 / Filament 5 stack`.

### Uppgift 8: Slutkontroll mot MySQL

- [ ] **Steg 1:** Kör `php artisan migrate:fresh` mot den lokala MySQL-databasen (`.env`). Förväntat: alla migrationer går igenom. Fråga Patrik först, eftersom kommandot tömmer den lokala databasen.
- [ ] **Steg 2:** `php artisan make:filament-user`, logga in på `/admin`, öppna Sidor och Inställningar, skapa en sida med ett block och spara. Förväntat: inget fel, och temat har stil.
- [ ] **Steg 3:** `composer show | grep -E "laravel/framework|filament/filament|livewire/livewire|pestphp/pest "` och `npm ls tailwindcss`. Förväntat: 13.x, 5.x, 4.x, 4.x och 4.x. Visa raderna för Patrik.
