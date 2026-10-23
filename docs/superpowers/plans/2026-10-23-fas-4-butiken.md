# Fas 4: butiken, implementationsplan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Mål:** Man kan köpa spår och hela släpp på sajten:
- korg i en låda från höger,
- kassa med Stripe Checkout,
- tacksida med nedladdningar,
- inloggning med länk och Mina köp med "N av M kvar".

Bara Patrik kommer in i `/admin`.

**Arkitektur:**
- Korgen ligger i sessionen och hanteras av `App\Shop\Cart`. Webbläsaren
  skickar bara typ, id och belopp, och priset räknas om från databasen
  varje gång.
- Lådan är en Livewire-komponent i layouten, byggd på ett `<dialog>`.
  `resources/js/cart.js` kopplar fas 3:s `cart:add` till Livewire.
- Kassan skapar en obetald `Order` och skickar köparen till Stripes hostade
  Checkout.
- Bara webhooken gör ordern betald. Den skapar också köparen som `User` och
  skickar kvittot med en inloggningslänk.
- Nedladdningar strömmas från disken `music`. Räknaren ökar atomiskt.
- Ett schemalagt kommando bygger släppens ZIP-filer i förväg.

**Tech stack:** Laravel 13, Livewire 4 (klassbaserade komponenter i
`app/Livewire`), Filament 5, stripe/stripe-php 16.6 (`StripeClient`,
`Webhook::constructEvent`), spatie/laravel-medialibrary 11,
spatie/laravel-settings, PHP `ZipArchive`, Pest 4 och agent-browser.

**Spec:** `docs/superpowers/specs/2026-10-23-sjeren-pa-laravel-design.md`.
Läs:
- avsnittet "Fas 4",
- "Datamodell för musik och butik",
- Begränsningar: Butikens korrekthet, Webbhotellet, och Innehåll och copy,
- beslut 2, 3, 5 och 10,
- de öppna frågorna 1, 3, 4 och 5.

Fas 3:s överlämning står i `.claude/rules/music.md`:
- händelsen `cart:add` med `{type, id, amount}`,
- disken `music`,
- `Release::playlist()`.

## Avvikelser och beslut (Patrik kontrollerar)

De öppna frågorna 1, 3, 4 och 5 är inte besvarade. Planen bygger så att
svaren blir inställningar eller text, inte ombyggen.

1. **Moms (öppen fråga 1).** Planen räknar ingen moms själv och har inget
   eget landsteg. Stripe Checkout frågar efter land, och webhooken sparar det
   på ordern. Sessionen skickar `tax_behavior: inclusive`, och
   `automatic_tax` slås på med `STRIPE_TAX=true`. Raderna "Moms ingår." och
   "Varav moms" visas inte förrän svaret finns, eftersom de är fel om Patrik
   inte är momsregistrerad. Allt detta skrivs i `TODO-INNEHALL.md`.
2. **PayPal (öppen fråga 5)** slås på i Stripes dashboard. Det kräver ingen
   kod, eftersom sessionen inte anger `payment_method_types`. Webhooken
   hanterar `checkout.session.async_payment_succeeded` (beslut 10).
3. **Hostad Checkout, inte inbäddad** (beslut 2 lämnar valet till byggaren).
   - Ingen Stripe.js och ingen publik nyckel behövs.
   - Ritningens steg "Betalning" behåller sin text "Du skickas vidare och
     betalar där…", men Kort/PayPal väljs hos Stripe.
   - Knappen heter "Betala N kr", utan "med kort".
4. **"Kvitto (PDF) ↓" blir "Kvitto ↗"** och länkar till Stripes eget kvitto
   (beslut 3). Stripes kvitto är en HTML-sida, inte en PDF.
5. **Nedladdningsräknaren är kolumnen `download_count` på `order_items`**
   och ingen egen modell `Download`. Det är en rad per köprad och inget
   annat. Ordernumret är `id` med fem siffror (`00042`) och har ingen egen
   kolumn.
6. **ZIP-filerna byggs av ett kommando varje minut** (`music:build-zips`).
   Ett fingeravtryck av spårens WAV-filer avgör om en ZIP saknas eller är
   gammal. Att spara ett släpp gör därför ingenting; nästa körning bygger.
   Tills ZIP-filen finns visar albumet "förbereds".
7. **Tacksidan loggar inte in köparen.** Adressen kan vara någon annans,
   och en inloggning skulle då visa den personens köp. Webbläsaren som
   betalade ser den orderns nedladdningar, eftersom order-id:t ligger i
   sessionen. Allt annat går via länken i mejlet.
8. **Konton med `is_admin` kan inte logga in med länk.** Annars blir
   tillgång till mejlen samma sak som tillgång till `/admin`.
9. **Belopp justeras i stället för att avvisas.**
   - Ett belopp under minimipriset höjs till minimipriset. Över
     100 000 kr sänks det till 100 000.
   - Något som inte är ett tal blir minimipriset.
   - En summa under 3 kr (Stripes minsta belopp i SEK) går inte till
     betalning.
10. **Sålda släpp och spår kan inte raderas.** Avpublicera dem i stället.
    Annars försvinner WAV-filen för den som betalat.
11. **Inställningarna ligger i `GeneralSettings`** och syns i den befintliga
    inställningssidan. Standardvärdena är 5 nedladdningar och 15 minuter,
    från ritningarnas `[5]` och `[15]`. Patrik sätter de riktiga.
12. **En aktiv inloggningslänk per köpare.** En ny länk gör den förra
    ogiltig, också länken i kvittot.
13. **Köpknappar visas bara när WAV finns** (för ett album: på varje spår).
    Efter seedern syns alltså inga köpknappar lokalt förrän en WAV laddats
    upp.
14. **Samtycket till ångerrätten (öppen fråga 3)** är en obligatorisk ruta
    med platshållartext. Tiden sparas i `orders.consented_at`.
15. **Mejl (öppen fråga 4):** Laravels mailer. Tjänst och avsändare sätts i
    `.env` i fas 6. Lokalt används `MAIL_MAILER=log`.

## Patrik gör (före Task 9)

Agenten kommer inte åt `.env`. Lägg in Stripes **testnycklar** själv:

```
STRIPE_SECRET=sk_test_…
STRIPE_WEBHOOK_SECRET=whsec_…   # skrivs ut av `stripe listen`
MAIL_MAILER=log
```

Kör sedan i ett eget fönster:
`stripe listen --forward-to http://sjerencms.test/stripe/webhook`.

Saknas nycklarna kör Task 9 kontrollen i webbläsaren fram till
Stripe-sidan, och resten täcks av testerna.

## Globala begränsningar

- Samma som i fas 3:
  - Kör aldrig `migrate:fresh`, `migrate:refresh`, `migrate:reset`,
    `db:wipe` eller `db:seed --force`.
  - `php artisan migrate` mot den lokala databasen är tillåtet.
  - Kör `php artisan config:clear` som eget kommando före den första
    testkörningen.
  - Kör `npm run build` före testerna.
  - Filer tas bort med `git rm`.
  - Commit görs på `main` med uttryckliga sökvägar. Pusha inte.
- **Butikens korrekthet** (spec, får inte förenklas bort):
  - Pris och summa räknas på servern.
  - Bara webhooken gör en order betald, och den verifieras med signatur.
  - Samma händelse två gånger ger ingen dubbel effekt.
  - Gränserna läses från inställningarna.
  - Köpare kommer aldrig in i `/admin`.
- **WAV och ZIP ligger bara på disken `music`** och får aldrig en publik URL.
  De skickas bara genom routen `download`.
- **Pengar:** i hela kronor (int) i databasen. Till Stripe går de som
  `amount * 100` i `sek`.
- **Ingen påhittad copy:**
  - Text tas ur ritningarna `Korg`, `Kassa`, `Tack`, `LoggaIn` och
    `MinaKop`.
  - Text som saknas blir `[platshållare]` och skrivs upp i
    `TODO-INNEHALL.md`.
  - På engelska översätts korta etiketter rakt. Meningar blir
    `[EN] <svensk text>`, som i fas 3.
- **Ritningar:** `docs/design/musik/{Korg,Kassa,Tack,LoggaIn,MinaKop}.dc.html`.
  - Mått kopieras från ritningarna. Mörkt läge är samma markup med sajtens
    mörka tokens.
  - Inga nya tokens behövs. `--scrim` blir `bg-ink/50`, och i mörkt läge
    `bg-black/60`.
- **Inga Stripe-anrop i testerna.** `App\Shop\StripeCheckout` byts mot en
  mock. Webhooktesterna signerar riktiga payloads och går genom
  `Webhook::constructEvent`.
- **Kontroller i webbläsaren:**
  - Kör `agent-browser` mot `http://sjerencms.test`, med ett
    `eval`-värde till varje skärmbild.
  - Skärmbilder sparas i `.superpowers/sdd/2026-10-23-fas-4-butiken/evidence/`.
- Kod, tester och commit-meddelanden skrivs på engelska.

## Granskningsfokus

1. **Manipulerat belopp eller vara**, vid tillägg i korgen och i kassan.
   Gäller belopp under minimipriset, belopp som inte är heltal, negativa
   belopp, enorma belopp, okänd typ, ett id som inte finns, ett opublicerat
   släpp, ett spår utan WAV, och en vara som avpubliceras medan den ligger i
   korgen. Summan blir aldrig under minimipriserna och kassan kraschar
   aldrig. Testas i Task 3 och 4.
2. **Webhooken kommer två gånger, i fel ordning eller med fel belopp.**
   Det ska ge en betald order, en användare och ett kvitto. Ett felaktigt
   `amount_total` betalar aldrig. Testas i Task 6.
3. **Nedladdning av fel person,** på en obetald eller återbetald order,
   efter att gränsen nåtts, eller för ett album utan ZIP. Svaret är 403 eller
   404, och räknaren går aldrig över gränsen och ökar inte när filen saknas.
   Testas i Task 7.
4. **Inloggningslänken öppnas av en länkskanner i mejlprogrammet,**
   återanvänds, har gått ut eller är ändrad. En GET loggar aldrig in och
   förbrukar inte länken. En okänd adress får samma svar som en känd.
   Testas i Task 5.
5. **Mejlet går inte att skicka när webhooken körs.** Ordern blir ändå
   betald, och Stripe får 200 så att samma händelse inte kommer igen.
   Testas i Task 6.

---

### Task 1: Behörighet och inställningar

**Files:**
- Create: `database/migrations/2026_10_26_000000_add_shop_columns_to_users_table.php`
- Create: `database/settings/2026_10_26_000000_add_shop_settings.php`
- Modify: `app/Models/User.php`, `database/factories/UserFactory.php`
- Modify: `app/Settings/GeneralSettings.php`, `app/Filament/Pages/ManageSettings.php`
- Modify: every test in `tests/Feature/Admin/` that logs in with `User::factory()->create()`:
  - `AdminAccessTest` (1)
  - `PageBlocksTest` (1)
  - `PageResourceTest` (1)
  - `ReleaseResourceTest` (6)
  - `ResourceIndexTest` (2)
  - `SettingsTest` (2)
- Test: `tests/Feature/Admin/AdminAccessTest.php`, `tests/Feature/Admin/SettingsTest.php`

**Interfaces:**
- Produces:
  - `users.is_admin` (bool, default false) and `users.login_nonce`
    (nullable string, 64), both hidden from serialization.
  - `User::canAccessPanel()` returns `$this->is_admin`.
  - The factory state `User::factory()->admin()`.
  - `GeneralSettings::$download_limit` (int, 5) and
    `GeneralSettings::$login_link_minutes` (int, 15).

- [ ] **Step 1: Write the failing tests**

```php
it('refuses a buyer at /admin', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

it('lets an admin in', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
});
```

In `SettingsTest`:
- `download_limit` and `login_link_minutes` read back as 5 and 15.
- Saving 3 and 30 through the Filament page persists them.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Admin`
Expected: FAIL. The buyer gets 200, `admin()` is undefined, and the
settings properties are missing.

- [ ] **Step 3: Implement**
- **Users migration:** adds the two columns, then runs
  `DB::table('users')->update(['is_admin' => true])`. Every existing row is
  Patrik's admin account, and there are no buyers yet. `down()` drops both
  columns.
- **`User`:** cast `is_admin` to boolean, add `login_nonce` to `$hidden`,
  and add `orders(): HasMany`. Task 2 creates the `Order` model, so add the
  relation there if you prefer.
- **Settings migration:** `general.download_limit` = 5 and
  `general.login_link_minutes` = 15.
- **`ManageSettings`:** add a section "Shop" with two
  `TextInput::make(...)->integer()->minValue(1)->required()`, with helper
  texts "Downloads per purchase" and "Minutes a login link is valid".
- **Admin tests:** replace every admin login in the files listed above with
  `->admin()`.

- [ ] **Step 4: Run the admin tests, then the whole suite**

Run: `./vendor/bin/pest tests/Feature/Admin`, then `./vendor/bin/pest`
Expected: PASS, with the suite at 70 + the new tests.

- [ ] **Step 5: Migrate locally and check that Patrik still gets in**

Run: `php artisan migrate`, then
`php artisan tinker --execute="echo App\Models\User::where('is_admin', true)->count();"`
Expected: ≥ 1.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_10_26_000000_add_shop_columns_to_users_table.php database/settings/2026_10_26_000000_add_shop_settings.php app/Models/User.php database/factories/UserFactory.php app/Settings/GeneralSettings.php app/Filament/Pages/ManageSettings.php tests/Feature/Admin
git commit -m "Keep buyers out of the admin and add the shop settings"
```

---

### Task 2: Ordrar och vad som går att köpa

**Files:**
- Create: `database/migrations/2026_10_26_000001_create_orders_table.php`, `…_000002_create_order_items_table.php`
- Create: `app/Enums/OrderStatus.php`, `app/Models/Order.php`, `app/Models/OrderItem.php`
- Create: `database/factories/OrderFactory.php`, `database/factories/OrderItemFactory.php`
- Create: `app/Filament/Resources/Orders/` (list and view pages only)
- Modify: `app/Models/Release.php`, `app/Models/Track.php`, `database/factories/TrackFactory.php`
- Modify: the delete actions in `app/Filament/Resources/Releases/` (the edit page header, the table, and `TracksRelationManager`)
- Test: `tests/Feature/Shop/OrderModelTest.php`, `tests/Feature/Admin/ResourceIndexTest.php`

**Interfaces:**
- Consumes: `User::orders()` from Task 1.
- Produces:
  - **`OrderStatus`**: a string enum with `Unpaid = 'unpaid'`,
    `Paid = 'paid'` and `Refunded = 'refunded'`.
  - **`orders`**:
    - `id`, `user_id` (nullable FK, `nullOnDelete`), `email`, `total`
      (unsigned int), `currency` (char 3, default `SEK`), `country`
      (nullable char 2), `status` (default `unpaid`, indexed) and `locale`
      (char 2).
    - `stripe_session_id` (nullable, unique), `stripe_payment_intent`
      (nullable, indexed) and `receipt_url` (nullable text).
    - `consented_at`, `paid_at` (nullable) and timestamps.
  - **`order_items`**: `id`, `order_id` (FK, cascade), `morphs('purchasable')`,
    `title`, `amount` (unsigned int), `download_count` (unsigned int,
    default 0) and timestamps.
  - **`Order`**:
    - casts: status → `OrderStatus`, plus the dates;
    - `user()`, `items()`;
    - `number(): string` → `sprintf('%05d', $this->id)`;
    - scope `paid()`.
  - **`OrderItem`**: `order()` and `purchasable(): MorphTo`. It stores class
    names. There is no morph map, because a map would change
    `Release::getMorphClass()` and orphan the existing `media.model_type`
    rows.
  - **`Release::isPurchasable(): bool`**: published, at least one track, and
    every track has a `wav`.
  - **`Track::isPurchasable(): bool`**: the release is published and the
    track has a `wav`.
  - **`isSold(): bool`** on both models. A release counts as sold if it, or
    any of its tracks, has an order item.
  - **`sales(): MorphMany`** on both models, pointing at `OrderItem`.
  - **Track playlist entry**: each track in `Release::playlist()` also has
    `buyable` (bool) = `Track::isPurchasable()`.
  - **`Track::factory()->withWav()`**: attaches a fake `master.wav`. The test
    must call `Storage::fake('music')` first.

- [ ] **Step 1: Write the failing tests** (`OrderModelTest`, `Storage::fake('music')` in `beforeEach`)

```php
it('sells a release only when it is published and every track has a WAV', function () {
    $release = Release::factory()->has(Track::factory()->withWav()->count(2))->create();
    expect($release->isPurchasable())->toBeTrue();

    Track::factory()->for($release)->create(['position' => 3]); // no WAV
    expect($release->fresh()->isPurchasable())->toBeFalse();
});

it('does not sell an unpublished release, an empty release, or a track without a WAV', function () { /* three expectations */ });

it('pads the order number', fn () => expect(Order::factory()->create(['id' => 42])->number())->toBe('00042'));

it('refuses to delete a sold release or track', function () {
    $track = Track::factory()->withWav()->create();
    OrderItem::factory()->for(Order::factory())->create(['purchasable_type' => Track::class, 'purchasable_id' => $track->id]);

    $track->release->delete();

    expect(Release::find($track->release_id))->not->toBeNull()
        ->and(Track::find($track->id))->not->toBeNull();
});

it('marks tracks without a WAV as not buyable in the playlist', function () { /* playlist()['tracks'][0]['buyable'] === false */ });
```

In `ResourceIndexTest`, add `/admin/orders` to the list that should return
200 for an admin.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/OrderModelTest.php tests/Feature/Admin/ResourceIndexTest.php`
Expected: FAIL, because the classes and the route are missing.

- [ ] **Step 3: Implement**
- **Delete guard on the models.** In `Release::booted()`, `deleting` returns
  `false` when `isSold()` is true, before the tracks are deleted.
  `Track::booted()` gets the same guard. This covers bulk deletes too.
- **Delete actions in Filament.** Hide the single-record delete actions with
  `->hidden(fn ($record) => $record->isSold())`.
- **`OrderResource`:**
  - Table columns: number, email, total with suffix " kr", status as a
    badge, and `paid_at`. Sorted by `created_at` desc.
  - The view page lists the items: title, amount and `download_count`.
  - No create, edit or delete.
  - Navigation group "Shop".

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop/OrderModelTest.php tests/Feature/Admin`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Migrate locally**

Run: `php artisan migrate`
Expected: two migrations run.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_10_26_000001_create_orders_table.php database/migrations/2026_10_26_000002_create_order_items_table.php app/Enums app/Models database/factories app/Filament/Resources tests/Feature/Shop/OrderModelTest.php tests/Feature/Admin/ResourceIndexTest.php
git commit -m "Add orders and decide what can be bought"
```

---

### Task 3: Korgen

**Files:**
- Create: `app/Shop/Cart.php`, `app/Shop/CartLine.php`
- Create: `app/Livewire/CartDrawer.php`, `resources/views/livewire/cart-drawer.blade.php` (`php artisan make:livewire CartDrawer --class`)
- Create: `resources/js/cart.js`, `lang/sv/shop.php`, `lang/en/shop.php`
- Modify:
  - `resources/js/app.js` (`import './cart'`)
  - `resources/js/player.js` (hide the bar's buy button when `!track.buyable`)
  - `resources/views/components/layouts/site.blade.php` (`<livewire:cart-drawer />` after `<main>`, outside `@persist`)
  - `resources/views/components/site-header.blade.php`
  - `resources/views/music/show.blade.php` (buy buttons only when `isPurchasable()`)
  - `lang/{sv,en}/nav.php`
- Test: `tests/Feature/Shop/CartTest.php`, `tests/Feature/Shop/CartDrawerTest.php`

**Interfaces:**
- Consumes:
  - `isPurchasable()`, `buyable` and `withWav()` from Task 2.
  - `cart:add {type, id, amount}` from `player.js`.
- Produces:
  - **Session `cart`**: `['track:12' => ['type' => 'track', 'id' => 12, 'amount' => 20], …]`.
  - **`Cart`** (resolve with `app(Cart::class)`, no state of its own):
    - Constants: `TYPES = ['release' => Release::class, 'track' => Track::class]`,
      `MAX_AMOUNT = 100000`, `MIN_TOTAL = 3`.
    - `add(string $type, mixed $id, mixed $amount): bool` is false for an
      unknown type, a missing id, or an item that cannot be bought. It
      stores `clamp((int) $amount, minimum, MAX_AMOUNT)`, and the same key
      replaces the earlier amount.
    - `setAmount(string $key, mixed $amount): void` clamps the same way.
    - `remove(string $key): void` and `clear(): void`.
    - `lines(): Collection<int, CartLine>` resolves the items from the
      database, eager-loads `media` and `tracks.media`, and drops anything
      that cannot be bought (from the session too).
    - `total(): int` and `count(): int`.
  - **`CartLine`** (readonly): `string $key`, `Release|Track $item`,
    `int $amount`; `minimum(): int` and `type(): string` (`'track'` or
    `'release'`).
  - **`CartDrawer`**:
    - `#[On('cart-add')] add(string $type, mixed $id, mixed $amount)`
      dispatches `cart-open`.
    - `updateAmount(string $key, mixed $amount)` and `remove(string $key)`.
    - Every action dispatches `cart-updated {count}`.
  - **DOM contract**:
    - `[data-cart-open]` buttons open the drawer.
    - `[data-cart-count]` spans show the count.
    - `dialog[data-cart-dialog]` is the drawer.

- [ ] **Step 1: Write the failing tests** (`CartTest`)

```php
beforeEach(fn () => Storage::fake('music'));

it('adds a track at the amount the buyer chose', function () {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);
    expect(app(Cart::class)->add('track', $track->id, 35))->toBeTrue()
        ->and(app(Cart::class)->total())->toBe(35);
});

it('raises amounts below the minimum and caps huge ones', function ($amount, $expected) {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);
    app(Cart::class)->add('track', $track->id, $amount);
    expect(app(Cart::class)->total())->toBe($expected);
})->with([[5, 20], [-50, 20], ['abc', 20], [20.9, 20], [999999999, 100000]]);

it('ignores what cannot be bought', function ($type, $id) {
    expect(app(Cart::class)->add($type, $id, 100))->toBeFalse()
        ->and(app(Cart::class)->count())->toBe(0);
})->with([
    'unknown type' => fn () => ['user', 1],
    'missing id' => fn () => ['track', 999],
    'no WAV' => fn () => ['track', Track::factory()->create()->id],
    'unpublished' => fn () => ['release', Release::factory()->has(Track::factory()->withWav())->create(['is_published' => false])->id],
]);

it('replaces the amount when the same item is added again', function () { /* add 30, add 50 → total 50, count 1 */ });

it('drops a line whose release was unpublished after it was added', function () { /* add, update is_published false → lines() empty, session cart empty */ });
```

`CartDrawerTest`, using `Livewire::test(CartDrawer::class)`:
- **Add:** `->dispatch('cart-add', type: 'track', id: $id, amount: 25)`
  shows the title and "25 kr" and dispatches `cart-open` and
  `cart-updated`.
- **Amount below the minimum:** `updateAmount` with a value below the
  minimum shows the minimum.
- **Remove:** `remove` gives count 0 and shows `shop.empty`.
- **Release page with a WAV:** it has
  `data-cart-add="track" data-id="{id}"`.
- **Release page without a WAV:** it has no `data-cart-add` at all.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/CartTest.php tests/Feature/Shop/CartDrawerTest.php`
Expected: FAIL with "Class App\Shop\Cart not found".

- [ ] **Step 3: Implement**
- **The drawer** follows `Korg.dc.html`:
  - A native `<dialog data-cart-dialog wire:ignore.self>` gives focus
    handling, Escape and the backdrop for free. `wire:ignore.self` keeps the
    `open` attribute when Livewire morphs the dialog.
  - Width 540 px and padding `0 44px` from lg. Full width and 20 px padding
    below lg. Backdrop `bg-ink/50`.
  - Each line is a grid `96px minmax(0,1fr) 120px`: the cover, then the
    title with its meta and "Ta bort", then the amount input
    (`wire:change="updateAmount('{key}', $event.target.value)"`).
  - Below the lines: Summa, "Till kassan" (left out when the cart is empty)
    and "Fortsätt lyssna". "Fortsätt lyssna" is a `<form method="dialog">`
    button, so it closes the dialog without JS.
  - Leave out "Moms ingår." (decision 1).
- **`cart.js`:**
  - Forwards `document`'s `cart:add` to
    `Livewire.dispatch('cart-add', e.detail)`.
  - `Livewire.on('cart-open')` calls
    `document.querySelector('[data-cart-dialog]')?.showModal()`.
  - `Livewire.on('cart-updated', ({count}) => …)` writes the count into
    every `[data-cart-count]`.
  - A click on `[data-cart-open]` calls `showModal()`.
- **Header:**
  - From lg, `<button data-cart-open class="lbl">` goes in the nav after
    Musik and reads `Korg · <span data-cart-count>{{ app(Cart::class)->count() }}</span>`.
  - Below lg, the same button sits next to the menu button, outside
    `<details>`, so the cart is always one tap away.
- **Copy:**

  | Key | Swedish |
  |---|---|
  | `shop.cart` | 'Korg' |
  | `shop.remove` | 'Ta bort' |
  | `shop.sum` | 'Summa' |
  | `shop.toCheckout` | 'Till kassan' |
  | `shop.keepListening` | 'Fortsätt lyssna' |
  | `shop.trackMeta` | 'Spår · WAV · minst :min kr' |
  | `shop.albumMeta` | 'Album · :count spår · WAV · minst :min kr' |
  | `shop.tracks` | trans_choice ':count spår' |
  | `shop.albums` | trans_choice ':count album' |
  | `shop.empty` | '[Korgen är tom]' |

  - The heading is `Korg · {tracks}, {albums}`, leaving out a part that is
    zero.
  - English: 'Cart', 'Remove', 'Total', 'To checkout', 'Keep listening',
    'Track · WAV · at least :min kr', and so on. `shop.empty` is
    `'[EN] [Korgen är tom]'`.

- [ ] **Step 4: Run the tests, build, then run the suite**

Run: `npm run build`, then `./vendor/bin/pest tests/Feature/Shop`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Check in the browser** (one `agent-browser` call per check)

Give track 1 on `s-d-l-past-voyages` a WAV:
`php artisan tinker --execute="App\Models\Track::whereHas('release', fn(\$q)=>\$q->where('slug','s-d-l-past-voyages'))->where('position',1)->first()->addMedia('<workspace>/fixture.wav')->preservingOriginal()->toMediaCollection('wav');"`.
Copy `fixture.wav` from fas 3's workspace into this workspace first.

Then check, on `/se/musik/s-d-l-past-voyages`:
1. Clicking the track's buy button opens the drawer. Check with
   `eval document.querySelector('[data-cart-dialog]').open` → `true`. The
   header reads "Korg · 1".
2. The player bar's buy button gives the same result, and it is hidden for
   track 2, which has no WAV.
3. Typing 5 in the drawer and blurring shows 20.
4. Escape closes the drawer. A `wire:navigate` to `/se/projekt` keeps
   "Korg · 1" and the music keeps playing.
5. Dark mode and 390 px width: screenshots of the drawer, with no
   horizontal scroll.

- [ ] **Step 6: Commit**

```bash
git add app/Shop app/Livewire/CartDrawer.php resources/views/livewire/cart-drawer.blade.php resources/js lang resources/views/components resources/views/music/show.blade.php tests/Feature/Shop
git commit -m "Add the cart drawer"
```

---

### Task 4: Kassan

**Files:**
- Create: `app/Shop/StripeCheckout.php`, `app/Livewire/CheckoutForm.php`, `resources/views/livewire/checkout-form.blade.php`
- Create: `app/Http/Controllers/ShowCheckout.php`, `resources/views/shop/checkout.blade.php`
- Modify:
  - `config/services.php`
  - `routes/web.php` (before `{slug}`)
  - `app/Http/Middleware/SetLocaleFromPrefix.php` (add `alternates()`)
  - `lang/{sv,en}/shop.php`
- Test: `tests/Feature/Shop/CheckoutTest.php`

**Interfaces:**
- Consumes: `Cart::lines()`, `Cart::total()` and `Cart::MIN_TOTAL` from
  Task 3, and `Order`/`OrderItem` from Task 2.
- Produces:
  - **Config:** `services.stripe.secret`, `services.stripe.webhook_secret`
    and `services.stripe.tax` (bool, `env('STRIPE_TAX', false)`).
  - **`StripeCheckout`:**
    - `payload(Order $order): array` is pure and testable.
    - `start(Order $order): string` creates the session, saves
      `stripe_session_id` and returns the URL.
    - `receiptUrl(string $paymentIntent): ?string` retrieves the payment
      intent with `expand: ['latest_charge']` and returns
      `latest_charge.receipt_url`.
  - **`SetLocaleFromPrefix::alternates(string $path): array<string, string>`**
    maps each language to `url("/{prefix}/{path}")`.
  - **`SetLocaleFromPrefix::prefix(string $language): string`** maps `sv` to
    `se` and `en` to `en`.
    - `orders.locale` stores the language (`app()->getLocale()`), but the
      URLs use the prefix.
    - Every route built outside a request in the locale group must pass
      `['locale' => SetLocaleFromPrefix::prefix($order->locale)]`, because
      `URL::defaults` is not set there. That covers the Stripe URLs and the
      webhook's login link.
  - **Route `shop.checkout`:** `/{locale}/kassa`.
  - **Session `orders`:** the list of order ids this browser has started.

- [ ] **Step 1: Write the failing tests**

```php
beforeEach(function () {
    Storage::fake('music');
    $this->track = Track::factory()->withWav()->create(['min_price' => 20, 'title' => 'Deep Down']);
});

it('sends an empty cart back to the music', fn () => $this->get('/se/kassa')->assertRedirect('/se/musik'));

it('creates an unpaid order and sends the buyer to Stripe', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('start')->once()->andReturn('https://checkout.stripe.com/c/pay/test');
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)
        ->set('email', ' Name@Example.se ')->set('consent', true)
        ->call('pay')
        ->assertRedirect('https://checkout.stripe.com/c/pay/test');

    $order = Order::sole();
    expect($order->status)->toBe(OrderStatus::Unpaid)
        ->and($order->email)->toBe('name@example.se')
        ->and($order->total)->toBe(35)
        ->and($order->locale)->toBe('sv')
        ->and($order->consented_at)->not->toBeNull()
        ->and($order->items->sole()->only('title', 'amount'))->toBe(['title' => 'Deep Down', 'amount' => 35])
        ->and(session('orders'))->toBe([$order->id]);
});

it('requires a valid email and consent', function () { /* both errors, Order::count() === 0 */ });

it('charges only what can still be bought', function () { /* two lines, unpublish one release before pay → total = remaining line */ });

it('refuses a total under Stripe minimum', function () { /* track min_price 0, amount 0 → error on pay, no order */ });

it('shows an error and keeps the order unpaid when Stripe fails', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('start')->andThrow(new Stripe\Exception\ApiConnectionException('down'));
    // … assertHasErrors('pay'), order unpaid, no exception
});

it('builds the Stripe payload from the order', function () {
    $order = /* order with one item, amount 80, locale sv */;
    $payload = app(StripeCheckout::class)->payload($order);
    expect($payload['mode'])->toBe('payment')
        ->and($payload['customer_email'])->toBe($order->email)
        ->and($payload['client_reference_id'])->toBe((string) $order->id)
        ->and($payload['line_items'][0]['price_data'])->toMatchArray(['currency' => 'sek', 'unit_amount' => 8000, 'tax_behavior' => 'inclusive'])
        ->and($payload['success_url'])->toEndWith("/se/kassa/tack/{$order->id}")
        ->and($payload['locale'])->toBe('sv')
        ->and($payload['automatic_tax'])->toBe(['enabled' => false])
        ->and($payload)->not->toHaveKey('payment_method_types');
});
```

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/CheckoutTest.php`
Expected: FAIL, because the route and classes are missing.

- [ ] **Step 3: Implement**
- **`StripeCheckout`:**
  - Build `new StripeClient(config('services.stripe.secret'))` lazily, inside
    `start` and `receiptUrl`, so the container can build the class without a
    key.
  - `cancel_url` is `route('shop.checkout')`.
  - `product_data.name` is the item title.
  - Add `metadata.order_id`.
- **`CheckoutForm::pay()`:**
  1. Validate `email` (`required|email|max:255`) and `consent`
     (`accepted`).
  2. Re-read `Cart::lines()`. If it is empty, redirect to
     `shop.checkout`. If the total is below `MIN_TOTAL`, add the error
     `shop.belowMinimum`.
  3. Create the order and its items in one transaction, with the email
     trimmed and lowercased.
  4. Push the id to the session `orders`.
  5. Call `start()`. On `Stripe\Exception\ApiErrorException`, log it and
     add the error `shop.payFailed` on `pay`.
  6. Otherwise `$this->redirect($url)`, without `navigate`.
- **The page** follows `Kassa.dc.html`:
  - `h1` "Kassa", and "← Tillbaka till korgen" as a
    `<button data-cart-open>`.
  - Step 01 "E-post": the input and the help text. Task 5 adds the link
    "Har du handlat förut? Logga in".
  - Step 02 "Betalning": the redirect text and the consent checkbox.
  - The right-hand column "Din order": the lines ("Spår · {artist}" /
    "Album · {artist}"), then "Att betala", the button "Betala :amount kr"
    and "Nedladdning i WAV".
  - `ShowCheckout` redirects an empty cart to `music.index`.
- **Copy (sv):**

  | Key | Swedish |
  |---|---|
  | `shop.checkout` | 'Kassa' |
  | `shop.backToCart` | 'Tillbaka till korgen' |
  | `shop.email` | 'E-post' |
  | `shop.emailHelp` | 'Ditt konto skapas med den här adressen. Där finns dina köp, och du loggar in med en länk i stället för lösenord.' |
  | `shop.payment` | 'Betalning' |
  | `shop.paymentHelp` | 'Du skickas vidare och betalar där. Inga kortuppgifter passerar den här sajten.' |
  | `shop.yourOrder` | 'Din order' |
  | `shop.toPay` | 'Att betala' |
  | `shop.pay` | 'Betala :amount kr' |
  | `shop.wav` | 'Nedladdning i WAV' |
  | `shop.consent` | '[Samtycke: leveransen börjar direkt och ångerrätten upphör]' |
  | `shop.payFailed` | '[Betalningen kunde inte startas. Försök igen.]' |
  | `shop.belowMinimum` | '[Summan måste vara minst 3 kr]' |

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop/CheckoutTest.php`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Check in the browser**

1. `/se/kassa` with an empty cart redirects to `/se/musik`.
2. With a track in the cart, the page matches `Kassa` at 1440 px and
   390 px, in light and dark mode.
3. "Betala" without consent shows the error.
4. If `STRIPE_SECRET` is set: "Betala" lands on `checkout.stripe.com`, and
   `eval location.host` is checked.

- [ ] **Step 6: Commit**

```bash
git add app/Shop/StripeCheckout.php app/Livewire/CheckoutForm.php resources/views/livewire/checkout-form.blade.php app/Http/Controllers/ShowCheckout.php resources/views/shop config/services.php routes/web.php app/Http/Middleware/SetLocaleFromPrefix.php lang tests/Feature/Shop/CheckoutTest.php
git commit -m "Add the checkout and hand payment to Stripe Checkout"
```

---

### Task 5: Inloggning med länk

**Files:**
- Create:
  - `app/Shop/LoginLinks.php`, `app/Mail/LoginLink.php`, `resources/views/mail/login-link.blade.php` (markdown mail)
  - `app/Livewire/LoginForm.php` and its view
  - `app/Http/Controllers/ShowLogin.php`, `ConfirmLogin.php`, `Logout.php`, `ShowAccount.php`
  - `resources/views/shop/{login,login-confirm,account}.blade.php`
- Modify:
  - `routes/web.php`, `bootstrap/app.php` (`redirectGuestsTo`)
  - `site-header.blade.php`, `livewire/checkout-form.blade.php` (the "Logga in" link)
  - `lang/{sv,en}/{shop,nav}.php`
- Test: `tests/Feature/Shop/LoginLinkTest.php`

**Interfaces:**
- Consumes: `users.login_nonce` and `is_admin`, and `login_link_minutes`
  from Task 1.
- Produces:
  - **`LoginLinks::url(User $user, ?string $language = null): string`**
    creates a new 40-character nonce, stores `hash('sha256', $nonce)` on the
    user and returns
    `URL::temporarySignedRoute('shop.login.confirm', now()->addMinutes(minutes), ['locale' => SetLocaleFromPrefix::prefix($language ?? app()->getLocale()), 'user' => $user->id, 'nonce' => $nonce])`.
  - **`LoginLinks::consume(User $user, string $nonce): bool`** is atomic:
    `User::whereKey($user->id)->where('login_nonce', hash)->update(['login_nonce' => null]) === 1`.
  - **`LoginLink` mailable**: `__construct(public string $url)`.
  - **Routes** in the locale group, before `{slug}`:

    | Method | Path | Name |
    |---|---|---|
    | GET | `logga-in` | `shop.login` |
    | GET | `logga-in/{user}` | `shop.login.confirm` |
    | POST | `logga-in/{user}` | (same handler) |
    | POST | `logga-ut` | `shop.logout` |
    | GET | `mina-kop` | `shop.account` (auth) |

  - **Guests** are redirected to `shop.login`. `/admin` keeps redirecting to
    Filament's own login.

- [ ] **Step 1: Write the failing tests** (`Mail::fake()` in `beforeEach`)

```php
it('mails a login link to a known buyer', function () {
    $buyer = User::factory()->create(['email' => 'name@example.se']);
    Livewire::test(LoginForm::class)->set('email', 'Name@Example.se')->call('send')
        ->assertSee('Länken är skickad till name@example.se');
    Mail::assertSent(LoginLink::class, fn ($mail) => $mail->hasTo('name@example.se'));
});

it('answers the same and sends nothing for an unknown address or an admin', function ($email) { /* same text, Mail::assertNothingSent() */ })
    ->with(['nobody@example.se', fn () => User::factory()->admin()->create()->email]);

it('sends at most three links per address in ten minutes', function () { /* 4 sends → assertSentCount(3) */ });

it('does not log in or burn the link on GET', function () {
    $buyer = User::factory()->create();
    $url = app(LoginLinks::class)->url($buyer);
    $this->get($url)->assertOk()->assertSee('Logga in');
    $this->assertGuest();
    expect($buyer->fresh()->login_nonce)->not->toBeNull();
});

it('logs in once on POST', function () {
    $buyer = User::factory()->create();
    $url = app(LoginLinks::class)->url($buyer);
    $this->post($url)->assertRedirect('/se/mina-kop');
    $this->assertAuthenticatedAs($buyer);

    auth()->logout();
    $this->post($url)->assertRedirect('/se/logga-in');
    $this->assertGuest();
});

it('rejects an expired, tampered or replaced link', function () {
    // expired: travel(16)->minutes(); tampered: change the nonce or user in the URL;
    // replaced: url() twice, the first one fails. Each → redirect /se/logga-in, guest.
});

it('sends guests from Mina köp to the login page', fn () => $this->get('/se/mina-kop')->assertRedirect('/se/logga-in'));

it('logs out', function () { /* actingAs buyer, post /se/logga-ut → guest */ });
```

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/LoginLinkTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement**
- **`ConfirmLogin`:**
  - GET renders the confirm page: `h1` "Mina köp" and a
    `<form method="post" action="{{ request()->fullUrl() }}">` with the
    button "Logga in". A link scanner only ever sends GET.
  - Both GET and POST check `$request->hasValidSignature()` themselves,
    instead of using the `signed` middleware, so an expired link redirects
    to `shop.login` with the flash `shop.expired`.
  - POST: when `consume()` returns false, redirect the same way. Otherwise
    `Auth::login($user, remember: true)`, regenerate the session and
    redirect to `shop.account`.
- **`LoginForm::send()`:**
  - Validate `email`, then normalize it (trim, lowercase).
  - Use `RateLimiter::attempt('login-link:'.sha1($email), 3, …, 600)`.
  - Only a user who is not an admin gets
    `Mail::to()->locale(app()->getLocale())->send(new LoginLink(...))`.
  - Always end in the "sent" state. Its view shows `shop.sent` and
    `shop.sentHelp` with `:minutes` from the settings.
- **`ShowAccount`** in this task only renders the `MinaKop` header: `h1`
  "Mina köp", `{email} · Logga ut` (a POST form). Task 8 fills in the page.
- **Header:**
  - A guest sees `nav.login` "Logga in" linking to `shop.login`.
  - A logged-in user sees `nav.account` "Mina köp" linking to
    `shop.account`, underlined when active as in `MinaKop.dc.html`.
  - On mobile both go in the `<details>` menu.
- **Copy (sv)** from `LoggaIn.dc.html`:

  | Key | Swedish |
  |---|---|
  | `shop.loginIntro` | 'Inget lösenord. Skriv adressen du handlade med, så kommer en inloggningslänk.' |
  | `shop.sendLink` | 'Skicka inloggningslänk' |
  | `shop.sent` | 'Länken är skickad till :email' |
  | `shop.sentHelp` | 'Den gäller i :minutes minuter. Syns den inte, titta i skräpposten eller skicka en ny.' |
  | `shop.boughtBefore` | 'Har du handlat förut?' |
  | `shop.logout` | 'Logga ut' |
  | `shop.expired` | '[Länken gäller inte längre. Skicka en ny.]' |
  | `nav.login` | 'Logga in' |
  | `nav.account` | 'Mina köp' |

  The mail's subject, body and button are placeholders, for example
  `[Din inloggningslänk]`.

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop/LoginLinkTest.php tests/Feature/Admin/AdminAccessTest.php`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Check in the browser**

1. Send a link from `/se/logga-in` to a buyer created in tinker. The link
   appears in `storage/logs/laravel.log` (`MAIL_MAILER=log`); skip if the
   mailer isn't `log`.
2. Opening it shows the confirm page. Clicking "Logga in" lands on
   `/se/mina-kop`, and the header shows "Mina köp".
3. Opening the same link again shows the login page with the expired notice.

- [ ] **Step 6: Commit**

```bash
git add app/Shop/LoginLinks.php app/Mail app/Livewire/LoginForm.php resources/views/livewire/login-form.blade.php app/Http/Controllers resources/views/shop resources/views/mail resources/views/components/site-header.blade.php resources/views/livewire/checkout-form.blade.php routes/web.php bootstrap/app.php lang tests/Feature/Shop/LoginLinkTest.php
git commit -m "Let buyers log in with a one-time link"
```

---

### Task 6: Webhooken och kvittot

**Files:**
- Create: `app/Http/Controllers/StripeWebhook.php`, `app/Mail/OrderPaid.php`, `resources/views/mail/order-paid.blade.php`
- Modify: `routes/web.php` (outside the locale group), `bootstrap/app.php` (`validateCsrfTokens(except: ['stripe/webhook'])`), `lang/{sv,en}/shop.php`
- Test: `tests/Feature/Shop/StripeWebhookTest.php`

**Interfaces:**
- Consumes:
  - `Order`, `OrderStatus` and `User` from Tasks 1 and 2.
  - `StripeCheckout::receiptUrl()` from Task 4.
  - `LoginLinks::url()` from Task 5.
- Produces:
  - Route `POST /stripe/webhook`, named `stripe.webhook`.
  - `OrderPaid` mailable: `__construct(public Order $order, public ?string $loginUrl)`.
  - A paid order has `paid_at`, `country`, `stripe_payment_intent`,
    `user_id` and, if Stripe returned one, `receipt_url`.

- [ ] **Step 1: Write the failing tests**

Sign with the test helper below, in the test file:

```php
function stripeEvent(string $type, array $object): TestResponse
{
    config(['services.stripe.webhook_secret' => 'whsec_test']);
    $payload = json_encode(['id' => 'evt_'.Str::random(8), 'object' => 'event', 'type' => $type, 'data' => ['object' => $object]]);
    $t = time();
    $signature = "t={$t},v1=".hash_hmac('sha256', "{$t}.{$payload}", 'whsec_test');

    return test()->call('POST', '/stripe/webhook', server: ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], content: $payload);
}

function checkoutSession(Order $order, array $overrides = []): array
{
    return array_replace_recursive([
        'id' => $order->stripe_session_id, 'object' => 'checkout.session',
        'client_reference_id' => (string) $order->id, 'payment_status' => 'paid',
        'amount_total' => $order->total * 100, 'currency' => 'sek', 'payment_intent' => 'pi_test',
        'customer_details' => ['address' => ['country' => 'SE']],
    ], $overrides);
}
```

The tests:
- `Mail::fake()`.
- `$this->mock(StripeCheckout::class)->shouldReceive('receiptUrl')->andReturn('https://pay.stripe.com/receipts/x')`.
- An unpaid order with `stripe_session_id = 'cs_test'`, `total = 100` and
  `email = 'name@example.se'`.

```php
it('rejects a bad signature', function () { /* wrong secret → 400, order unpaid */ });

it('marks the order paid, creates the buyer and mails the receipt', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    $order = $this->order->fresh();
    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->country)->toBe('SE')
        ->and($order->stripe_payment_intent)->toBe('pi_test')
        ->and($order->receipt_url)->toBe('https://pay.stripe.com/receipts/x')
        ->and($order->user->email)->toBe('name@example.se');
    Mail::assertSent(OrderPaid::class, fn ($mail) => $mail->hasTo('name@example.se') && $mail->loginUrl !== null);
});

it('does nothing the second time', function () { /* send twice → User::count() 1, Mail::assertSentCount(1) */ });

it('waits for a delayed payment', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order, ['payment_status' => 'unpaid']));
    expect($this->order->fresh()->status)->toBe(OrderStatus::Unpaid);
    stripeEvent('checkout.session.async_payment_succeeded', checkoutSession($this->order));
    expect($this->order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('never pays an order whose amount, currency or session does not match', function ($overrides) { /* 200, unpaid, no mail */ })
    ->with([[['amount_total' => 100]], [['currency' => 'eur']], [['id' => 'cs_other']], [['client_reference_id' => '999']]]);

it('reuses an existing buyer whatever the case of the address', function () { /* User 'Name@Example.se' exists → no new user */ });

it('marks a fully refunded order refunded', function () { /* paid order with pi_test; charge.refunded {payment_intent: pi_test, refunded: true} → Refunded; refunded false → stays Paid */ });

it('stays paid and answers 200 when the mail fails', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));
    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();
    expect($this->order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('mails an admin buyer without a login link', function () { /* order email = admin's → loginUrl null */ });
```

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/StripeWebhookTest.php`
Expected: FAIL with a 404 on `/stripe/webhook`.

- [ ] **Step 3: Implement**
- **Signature:** `Webhook::constructEvent($request->getContent(), $request->header('Stripe-Signature'), config('services.stripe.webhook_secret'))`.
  `SignatureVerificationException` or `UnexpectedValueException` gives 400.
- **Events:**
  - `checkout.session.completed` with `payment_status === 'paid'`, and
    `checkout.session.async_payment_succeeded`, go to `paid($session)`.
  - `charge.refunded` with `refunded === true` sets the paid order with that
    `stripe_payment_intent` to `Refunded`.
  - Everything else gets 200.
- **`paid()`:**
  1. Find the order by `client_reference_id`. It must match
     `stripe_session_id`, `amount_total === total * 100` and
     `currency === 'sek'`. Otherwise log a warning and return 200.
  2. In one transaction, run
     `Order::whereKey()->where('status', Unpaid)->update([...])`. If no row
     changed, it is a duplicate: return 200. Otherwise `firstOrCreate` the
     user on the lowercased email with `name` = email and
     `password = Str::random(40)`, and save `user_id`.
  3. After the commit, in a `try` that logs on failure:
     - store `receipt_url`,
     - send `OrderPaid` in `order.locale`, with the login link
       (`LoginLinks::url($user, $order->locale)`) only when the user is not
       an admin. The test asserts that the link contains `/se/logga-in/`.
- **Mail:** the lines, the total, a "Ladda ner" button to the login link,
  and the receipt link. Every text is a placeholder, for example
  `[Kvitto: order :number]`.

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop/StripeWebhookTest.php`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/StripeWebhook.php app/Mail/OrderPaid.php resources/views/mail/order-paid.blade.php routes/web.php bootstrap/app.php lang tests/Feature/Shop/StripeWebhookTest.php
git commit -m "Mark orders paid from the Stripe webhook and mail the receipt"
```

---

### Task 7: Nedladdningar och ZIP-filer

**Files:**
- Create: `app/Console/Commands/BuildReleaseZips.php`, `app/Http/Controllers/DownloadPurchase.php`
- Modify:
  - `app/Models/Release.php` (the `zip` collection)
  - `app/Models/OrderItem.php`
  - `routes/console.php` (the schedule)
  - `routes/web.php` (outside the locale group)
- Test: `tests/Feature/Shop/BuildReleaseZipsTest.php`, `tests/Feature/Shop/DownloadTest.php`

**Interfaces:**
- Consumes: `OrderItem`, `OrderStatus` and the session `orders` from
  Tasks 2 and 4, and `download_limit` from Task 1.
- Produces:
  - **`zip` collection on `Release`**: single file, disk `music`, with the
    custom property `fingerprint`.
  - **`music:build-zips`**, scheduled `everyMinute()->withoutOverlapping()`.
  - **`OrderItem::file(): ?Media`**: the track's `wav`, or the release's
    `zip`.
  - **`OrderItem::downloadName(): string`**: `"{artist} - {title}.wav"` or
    `.zip`.
  - **`OrderItem::downloadsLeft(): int`**: `max(0, limit - download_count)`.
  - **Route `GET /download/{item}`**, named `download`.

- [ ] **Step 1: Write the failing tests** (`Storage::fake('music')`)

```php
it('builds a zip with numbered WAVs', function () {
    $release = Release::factory()->has(Track::factory()->withWav()->count(2)->sequence(['position' => 1, 'title' => 'One'], ['position' => 2, 'title' => 'Two/Three']))->create();
    $this->artisan('music:build-zips')->assertSuccessful();

    $zip = new ZipArchive; $zip->open($release->fresh()->getFirstMedia('zip')->getPath());
    expect([$zip->getNameIndex(0), $zip->getNameIndex(1)])->toBe(['01 One.wav', '02 Two-Three.wav']);
});

it('leaves an unchanged zip alone and rebuilds a stale one', function () { /* media id same after 2nd run; replace a wav → new media id */ });

it('skips a release with a track missing its WAV', function () { /* no zip media */ });
```

`DownloadTest`. Make a paid order with one track item and one release item,
with the user = buyer and the ZIP already built.

```php
it('streams the WAV to its owner and counts it', function () {
    $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertDownload('S.D.L. - Deep Down.wav');
    expect($this->trackItem->fresh()->download_count)->toBe(1);
});

it('lets the browser that paid download without logging in', function () { /* withSession(['orders' => [$order->id]]) → assertDownload */ });

it('refuses strangers, unpaid and refunded orders', function () { /* other user → 403; status unpaid → 403; refunded → 403; count stays 0 */ });

it('stops at the limit', function () {
    $this->trackItem->update(['download_count' => 5]); // download_limit 5
    $this->actingAs($this->buyer)->get(route('download', $this->trackItem))->assertForbidden();
    expect($this->trackItem->fresh()->download_count)->toBe(5);
});

it('answers 404 and counts nothing while the zip is missing', function () { /* delete zip media → 404, count 0 */ });
```

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/BuildReleaseZipsTest.php tests/Feature/Shop/DownloadTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement**
- **The command:**
  - Covers every release with at least one track and a `wav` on every
    track.
  - The fingerprint is `sha1` of the tracks in position order, each as
    `"{id}:{wav media id}:{position}:{title}"`.
  - On a mismatch it builds `storage_path('app/tmp/release-{id}.zip')` with
    `ZipArchive`, adding each entry with `addFile($media->getPath(), …)`
    and `setCompressionName(…, ZipArchive::CM_STORE)`. WAV doesn't
    compress, and storing is fast.
  - Entry names are `sprintf('%02d %s.wav', position, title)`, with
    `/\:*?"<>|` replaced by `-`.
  - It then runs `addMedia($tmp)->usingFileName("{slug}.zip")->withCustomProperties(['fingerprint' => …])->toMediaCollection('zip')`.
    `addMedia` moves the file.
  - It prints one line per release it built.
  - Add the comment
    `// ponytail: reads WAVs by local path; a remote disk (R2) needs a temp copy first.`
- **`DownloadPurchase`**, in this order:
  1. The order must be `Paid`, else 403.
  2. The caller must own the order (`auth()->id() === user_id`) or have its
     id in the session `orders`, else 403.
  3. `file()` must not be null, else 404.
  4. `OrderItem::whereKey()->where('download_count', '<', $limit)->increment('download_count')`
     must change one row, else 403.
  5. Return `Storage::disk($file->disk)->download($file->getPathRelativeToRoot(), $item->downloadName())`.
     It streams and never reads the whole file into memory.

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Check the schedule**

Run: `php artisan schedule:list`
Expected: `music:build-zips` runs every minute.

- [ ] **Step 6: Commit**

```bash
git add app/Console/Commands/BuildReleaseZips.php app/Http/Controllers/DownloadPurchase.php app/Models routes/console.php routes/web.php tests/Feature/Shop/BuildReleaseZipsTest.php tests/Feature/Shop/DownloadTest.php
git commit -m "Build album zips ahead and stream counted downloads"
```

---

### Task 8: Tacksidan och Mina köp

**Files:**
- Create:
  - `app/Http/Controllers/ShowThanks.php`, `resources/views/shop/thanks.blade.php`
  - `app/Livewire/OrderStatus.php` and its view
  - `resources/views/components/shop/download-row.blade.php`
- Modify: `routes/web.php`, `app/Http/Controllers/ShowAccount.php`, `resources/views/shop/account.blade.php`, `lang/{sv,en}/shop.php`
- Test: `tests/Feature/Shop/ThanksPageTest.php`, `tests/Feature/Shop/AccountPageTest.php`

**Interfaces:**
- Consumes:
  - `OrderItem::file()`, `downloadsLeft()` and the route `download` from
    Task 7.
  - The session `orders` from Task 4, and `shop.account` from Task 5.
  - `Cart::clear()` from Task 3.
- Produces:
  - **Route `shop.thanks`**: `/{locale}/kassa/tack/{order}`, before
    `{slug}`.
  - **`<x-shop.download-row :item />`**, one row per order item, with one of
    three states:
    - **ready**: "N av M kvar" and "Ladda ner WAV ↓";
    - **preparing**: the album's ZIP is missing;
    - **exhausted**: "Slut på nedladdningar" and a "Skriv till mig"
      `mailto:` to `GeneralSettings::$email`.

- [ ] **Step 1: Write the failing tests**

```php
it('hides an order from browsers that did not start it', fn () => $this->get("/se/kassa/tack/{$this->order->id}")->assertNotFound());

it('shows a processing state and polls until the webhook comes', function () {
    $this->withSession(['orders' => [$this->order->id]])->get("/se/kassa/tack/{$this->order->id}")
        ->assertSee(__('shop.processing'))->assertSee('wire:poll', false);
});

it('shows the downloads once paid and empties the cart', function () {
    // paid order with one track item; session has cart + orders
    // → assertSee('Tack'), '5 av 5 kvar', route('download', $item); session('cart') empty
});

it('shows a preparing album without a link', function () { /* release item, no zip → __('shop.preparing'), no route('download', $item) */ });
```

`AccountPageTest`:
- **Own items only:** a buyer sees their own paid items and not another
  buyer's.
- **Exhausted:** an item at the limit shows "Slut på nedladdningar" and a
  `mailto:`.
- **Receipts:** each paid order has a row with its number, the date
  (`j M Y` in the locale), the titles joined with ", ", the total, and a
  link to `receipt_url`, with no link when it is null.
- **Refunded:** a refunded order is not shown.
- **Nothing bought:** a buyer with no purchases sees `shop.nothing`.

- [ ] **Step 2: Run them and watch them fail**

Run: `./vendor/bin/pest tests/Feature/Shop/ThanksPageTest.php tests/Feature/Shop/AccountPageTest.php`
Expected: FAIL.

- [ ] **Step 3: Implement**
- **`ShowThanks`:**
  - `abort_unless` the order id is in the session `orders`, or
    `auth()->id()` owns the order, with 404.
  - Call `app(Cart::class)->clear()`.
  - Render `<livewire:order-status :order="$order" />`.
- **`OrderStatus`** has `#[Locked] public Order $order`:
  - Unpaid: the root has `wire:poll.2s` and shows `shop.processing`.
  - Paid: the layout of `Tack.dc.html`, with no poll.
- **Mina köp** follows `MinaKop.dc.html`:
  - 01 Nedladdningar, with `WAV · :limit gånger per köp` and the rows
    newest first.
  - 02 Kvitton, as a table.
  - The receipt link reads "Kvitto ↗" (decision 4).
- **Copy (sv):**

  | Key | Swedish |
  |---|---|
  | `shop.thanks` | 'Tack' |
  | `shop.thanksText` | 'Kvittot och en inloggningslänk ligger i mejlet till :email. Varje köp kan laddas ner :limit gånger, här eller från Mina köp.' |
  | `shop.download` | 'Ladda ner' |
  | `shop.orderLine` | 'Order :number · :total kr' |
  | `shop.kindTrack` | '1 spår · WAV' |
  | `shop.kindAlbum` | ':count spår · WAV i en zip' |
  | `shop.left` | ':left av :limit kvar' |
  | `shop.downloadWav` | 'Ladda ner WAV' |
  | `shop.help` | 'Fungerar inte länken? Svara på mejlet så löser jag det.' |
  | `shop.backToMusic` | 'Tillbaka till musiken' |
  | `shop.downloads` | 'Nedladdningar' |
  | `shop.perPurchase` | 'WAV · :limit gånger per köp' |
  | `shop.exhausted` | 'Slut på nedladdningar' |
  | `shop.writeMe` | 'Skriv till mig' |
  | `shop.receipts` | 'Kvitton' |
  | `shop.order` | 'Order' |
  | `shop.date` | 'Datum' |
  | `shop.contents` | 'Innehåll' |
  | `shop.amount` | 'Belopp' |
  | `shop.receipt` | 'Kvitto' |

  These are placeholders:

  | Key | Swedish |
  |---|---|
  | `shop.processing` | '[Betalningen behandlas]' |
  | `shop.preparing` | '[Förbereds]' |
  | `shop.nothing` | '[Inga köp än]' |

- [ ] **Step 4: Run the tests, then the suite**

Run: `./vendor/bin/pest tests/Feature/Shop`, then `./vendor/bin/pest`
Expected: PASS.

- [ ] **Step 5: Check in the browser**

Mark an order paid in tinker and put its id in the session by starting
the checkout in the browser.

1. The Tack page goes from processing to paid without a reload.
2. The download returns a file (check `eval` on the link's `href` and the
   HTTP status), and the row then shows "4 av 5 kvar".
3. Mina köp, logged in with a link, in light and dark mode at 1440 px and
   390 px: the exhausted state, and no horizontal scroll.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/ShowThanks.php app/Http/Controllers/ShowAccount.php app/Livewire/OrderStatus.php resources/views/livewire/order-status.blade.php resources/views/shop resources/views/components/shop routes/web.php lang tests/Feature/Shop/ThanksPageTest.php tests/Feature/Shop/AccountPageTest.php
git commit -m "Add the thank-you page and Mina köp"
```

---

### Task 9: Dokumentation och hela köpet i webbläsaren

**Files:**
- Create: `.claude/rules/shop.md`, added with `git add -f` as in fas 3
- Modify: `CLAUDE.md` (one pointer line under "Public Site and Music"), `.claude/rules/music.md` (`cart:add` is now consumed by `cart.js`), `TODO-INNEHALL.md`

- [ ] **Step 1: Write `shop.md`**

About 60 lines, covering:
- **Flow:** cart → checkout → Stripe → webhook → Tack and Mina köp.
- **Routes and route names.**
- **Sessions:** `cart` and `orders`.
- **Correctness rules:** decisions 7–10 above, and the Granskningsfokus.
- **Settings.**
- **Environment:** `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`, `STRIPE_TAX`.
- **Local work:** `stripe listen`, and `music:build-zips` via
  `schedule:run`, which needs cron in fas 6.
- **Tests:** Stripe is never called in tests.

- [ ] **Step 2: Log the content in `TODO-INNEHALL.md`**

One line per group:
- the shop placeholders in `lang/sv/shop.php`;
- the English placeholders;
- the mail texts;
- the consent text, and that terms and a privacy policy are missing
  (öppen fråga 3);
- "Moms ingår" and "Varav moms", which wait for öppen fråga 1;
- the values 5 and 15, which Patrik sets in Filament.

- [ ] **Step 3: Run the whole purchase in the browser**

This needs Patrik's keys; see "Patrik gör".
1. Add a track to the cart, go to the checkout, and pay with test card
   `4242 4242 4242 4242`.
2. Back on Tack, the page shows processing, then the download.
3. In the log: the receipt with the login link.
4. Mina köp through that link shows "4 av 5 kvar" after one download.

Without the keys, record a `Ruling:` in the ledger and stop at the Stripe
page.

- [ ] **Step 4: Run the suite and check git**

Run: `./vendor/bin/pest`, then `git status`
Expected: PASS, and nothing but these files is modified.

- [ ] **Step 5: Commit**

```bash
git add -f .claude/rules/shop.md .claude/rules/music.md
git add CLAUDE.md TODO-INNEHALL.md
git commit -m "Document the shop"
```
