# sjeren.se på Laravel: portfolio, musik och butik i en app

Datum: 2026-10-23. Status: utkast, väntar på Patriks godkännande.

## Avsikt

Patrik vill sälja sin musik direkt på sjeren.se. Idag ligger den på
Bandcamp (<https://patriksjeren.bandcamp.com/music>). Köp ska gå genom hans
egen sajt, med varukorg, kassa och kundkonto, och han vill själv kunna lägga
in nya släpp i en admin.

Portfolion (sjeren-2026, Next 16) måste vara statiskt exporterad för att
ligga på webbhotellet. En butik med konton och betalningar kräver en
server. Därför byggs allt om i **en** app: sjerencms, med Laravel, Livewire
och Filament, i den nya designen. Patrik bygger i Laravel och Livewire varje
dag, så det är hans hemmaplan.

sjerencms nuvarande Vue-frontend med SSR är inte live någonstans. Den kan
ersättas helt. kalibr (`~/Code/kalibr`) är en separat kopia och berörs inte.

## Mål

1. sjerencms körs på senaste versionerna: Laravel 13, Filament 5, Livewire 4
   och Tailwind 4. Det här görs först, innan något nytt byggs.
2. Hela portfolion från sjeren-2026 finns i Laravel-appen: startsidan,
   /projekt och /om-mig. Utseende, innehåll, ljust och mörkt läge och
   rörelserna är desamma.
3. Det finns en musikflik: alla släpp i ett rutnät, en sida per släpp, och
   en spelare som fortsätter spela när man byter sida.
4. Man kan köpa hela släpp **och** enskilda spår. Det går via korg och kassa
   med Stripe.
5. En köpare kan logga in med en länk i mejlet och ladda ner sina köp igen,
   ett begränsat antal gånger. Nedladdningarna är WAV.
6. Patrik lägger in släpp, spår, ljudfiler och projekt i Filament.
7. Appen körs på hostups delade webbhotell utan någon Node-process i drift.

## Icke-mål

- Bandcamp läggs inte ner och synkas inte. Det lever kvar parallellt.
- Ingen blogg. Post och Category från sjerencms följer inte med.
- Prenumerationer, fysiska varor, rabattkoder och presentkort ingår inte.
- Inga lösenord för köpare. De loggar bara in med länk.
- kalibr ändras inte.
- Ingen import av gammalt innehåll från sjerencms-databasen
  (`rechl_kalibr.sql`, `storage.zip`). Databasen börjar om från noll.

## Anti-mål

Det här vore misslyckanden även om allt tekniskt fungerar:

- **Någon kan ladda ner WAV utan att ha betalat.** Det gäller till exempel
  om priset kan ändras i webbläsaren, eller om en order blir betald utan att
  Stripe bekräftat det.
- **Sajten ser ut som en butiksmall.** Designen är "Redaktion på ljust
  papper" i `sjeren-2026/DESIGN.md`. Butiken ska kännas som en del av
  portfolion.
- **Påhittad copy.** Text som inte kommer från Patrik får inte smyga in
  (se Begränsningar).
- **Gamla länkar slutar fungera** när sajten byter teknik.
- **Musiken avbryts** när besökaren byter sida.

## Källor

Byggaren läser de här i stället för att gissa mått eller text.

| Vad | Var |
|---|---|
| Designsystemet: färger, typografi, layout, rörelse | `~/Code/sjeren-2026/DESIGN.md` |
| Tokens som CSS-variabler, ljust och mörkt | `~/Code/sjeren-2026/src/app/globals.css` |
| Portfolions ritningar | `~/Code/sjeren-2026/design/artboards/*.dc.html` (Main, Mobil, Mork, OmMig, Projekt, Register1920) |
| Portfolions komponenter, som referens för porten | `~/Code/sjeren-2026/src/components/*.tsx` |
| Innehåll: projekt och om mig | `~/Code/sjeren-2026/src/data/projects.ts`, `about.ts` |
| UI-text, svenska och engelska | `~/Code/sjeren-2026/messages/se.json`, `en.json` |
| Utkast och platshållare i texten | `~/Code/sjeren-2026/TODO-INNEHALL.md` |
| Omdirigeringar och cacheregler i drift | `~/Code/sjeren-2026/public/.htaccess` |
| Hostup och hur sajten publiceras idag | `~/Code/sjeren-2026/DEPLOY.md` |
| Musikens ritningar, ljus och mörk variant av varje skärm | `docs/design/musik/*.dc.html` (index: `canvas.json`) |
| Samma ritningar visuellt | <https://claude.ai/artifact/XM79Vkr14cBC8kpZcSxWLy> |
| De 22 släppen hämtade från Bandcamp | `database/seeders/data/music.json`, omslag i `database/seeders/data/covers/` |

Filerna i sjeren-2026 har ocommittade ändringar av Patrik. De ska läsas,
inte ändras.

`*.dc.html` är ritningar med mallsyntax (`{{...}}`, `<sc-for>`, `<sc-if>`).
Bilder pekar på `/_blob/...` och visas inte lokalt. Använd länken till
ritningarna för att se dem.

Ritningarna under "Bortvalda" (`Main.dc.html`, `Tidslinje.dc.html`) byggs
inte. Musiklistan ska vara variant B: omslag i 4 kolumner, nyast först,
**ingen** indelning efter år.

## Patriks beslut

1. Allt i en app: Laravel, Livewire och Filament i sjerencms, där Vue-delen
   ersätts helt.
2. Uppgraderingen görs först: Laravel 13, Filament 5, Livewire 4.
3. Listvy B med omslag i 4 kolumner, nyast först, utan år.
4. Både släpp och enskilda spår går att köpa.
5. Köpen görs på sajten, inte på Bandcamp. Betalning via Stripe,
   "kanske PayPal" (se Öppna frågor).
6. Köpare får ett konto och kan ladda ner igen ett begränsat antal gånger.
7. Nedladdningar är WAV, förlyssningar är MP3.
8. Patrik har en egen admin där han lägger in släpp.
9. Varje skärm finns i ljust och mörkt läge. Sajten har redan ett
   lägesreglage i headern.
10. Blocken i sjerencms återanvänds och får ny design där det går.
11. Hostup kör redan PHP 8.5.

## Begränsningar

**Webbhotellet (hostup: LiteSpeed, cPanel, SSH, PHP 8.5)**
- Ingen Node-process i drift. Vite bygger tillgångarna innan de laddas upp.
- Ingen kö-worker som körs hela tiden. Använd `sync`-kön eller en
  cron-körd `schedule:run`. Inget får kräva en process som lever mellan
  anrop.
- Dokumentroten ska peka på `public/`. Allt annat ligger utanför webbroten.
- WAV-filer ligger utanför webbroten. De skickas bara via en route som
  kräver inloggning eller en signerad länk, och de strömmas utan att hela
  filen läses in i minnet.
- Hur mycket diskutrymme som finns för WAV är inte kontrollerat (se Öppna
  frågor). Lagringen går via Laravels filesystem-disk, så att den kan
  flyttas till exempelvis Cloudflare R2 utan att koden ändras.

**Innehåll och copy**
- Hitta inte på copy. UI-text flyttas en till en från `messages/*.json`.
  Text som behövs men inte finns blir en tydlig platshållare och loggas i en
  `TODO-INNEHALL.md` i sjerencms.
- Musikritningarna innehåller platshållare. Siffror inom hakparentes, som
  `[5]` nedladdningar och `[15]` minuter, är inte beslutade värden. De blir
  inställningar.
- WeUnite-uppdraget nämns i text men får aldrig illustreras. Se
  `sjeren-2026/STATUS.md`.
- Svenska är standardspråk och engelska kommer i andra hand. Båda språken
  ska fungera på varje sida, också i butiken.

**Adresser**
- Alla adresser som finns på den nuvarande sajten ska fortsätta fungera:
  `/se/`, `/en/`, `/se/om-mig/`, `/en/om-mig/`, `/se/projekt/`,
  `/en/projekt/` med flera. Omdirigeringarna i
  `sjeren-2026/public/.htaccess` följer med, till exempel `/en/about-me/` →
  `/en/om-mig/`.

**Butikens korrekthet** (får inte förenklas bort)
- Pris och summa räknas alltid på servern. Webbläsaren skickar bara vad som
  ska köpas och vilket belopp köparen själv valt. Servern kontrollerar att
  beloppet inte är under minimipriset.
- En order blir betald **bara** när Stripes webhook bekräftat det.
  Webhooken verifieras med signatur och tål att samma händelse kommer flera
  gånger.
- Antalet nedladdningar och hur länge en inloggningslänk gäller är
  inställningar i Filament, inte hårdkodade värden. Idag är gränsen
  hårdkodad till 3 i `Download::hasReachedLimit()`.
- En köprad kan peka på ett släpp eller ett spår (polymorf koppling).
- Köpare kommer aldrig åt `/admin`. Filaments `canAccessPanel` släpper bara
  in Patrik.

**Arbetssätt i repot**
- Arbetskopian har ändringar som inte är Patriks att tappa bort: en köad
  borttagning av `.claude/settings.local.json`, samt ändrade `.gitignore`
  och `resources/css/app.css`. Committa med uttryckliga sökvägar så att de
  inte följer med av misstag. Fråga Patrik vad som ska hända med dem innan
  fas 0.
- Committa och pusha bara när Patrik ber om det. Det gäller före
  `AGENTS.md`, som säger att varje pass ska avslutas med push.
- Repot använder beads (`bd`) för ärenden. Uppgifterna i planerna kan läggas
  in där.

## Faser

Specen är en helhet. Varje fas får en egen plan, och fas 0 och 1 planeras
först. En fas är klar när dess tester är gröna och Patrik har sett
resultatet.

### Fas 0: Städa och sätt upp tester (före uppgraderingen)

Kod som ändå ska bort ska inte släpas genom tre ramverksuppgraderingar.

1. Ta bort Vue-frontenden och SSR: `resources/js/`, `node-server.js`,
   `server.js`, `SsrController`, catch-all-routen i `routes/web.php`,
   `node()`-helpern, `tailwind.config.js` och npm-beroenden som
   `vue`, `pinia`, `vue-router`, `@vue/server-renderer`, `@vueuse/head`,
   `vue3-carousel`, `@iconify/vue`, `@stripe/stripe-js` och
   `@vitejs/plugin-vue`.
2. Ta bort det som inte följer med: Post, Category, Product,
   ProductCategory, ProductAddon, Order, OrderItem och Download med deras
   Filament-resurser och migrationer, samt `ShopController` och API-routes
   som bara Vue använde. Butikens modeller byggs om i fas 4.
3. Ta bort de block som den nya designen inte använder: LogoShowcase,
   TeamMembers, PricingTables, IconCards, BlogPosts, Carousel, Gallery,
   ColumnCards, ThreeColumnCards, Video, ContactForm och FullImage.
   ColumnsLayout tas också bort om ingen kvarvarande sida behöver det.
   Behåll HeadlineText, StaggerText, Ticker, FaqAccordion, CallToAction,
   ServiceCards, StickyCarousel, ImageText och FullText.
4. Skriv Pest-smoketester som ska vara gröna efter varje steg i fas 1:
   - admin kan logga in,
   - varje Filament-resurs visar sin lista,
   - en sida med kvarvarande block kan sparas och läsas tillbaka,
   - GeneralSettings kan läsas och sparas.

### Fas 1: Uppgradera

Ett steg i taget. Smoketesterna ska vara gröna efter varje steg innan nästa
påbörjas.

- Laravel 11 → 12 enligt 12.x-guiden. PHPUnit 11. Notera att `HasUuids`
  nu ger UUIDv7.
- Filament 3 → 4 med `vendor/bin/filament-v4`. Det egna temat
  (`resources/css/filament/admin/theme.css`) kräver Tailwind 4.1. Välj om
  den nya katalogstrukturen ska användas
  (`filament:upgrade-directory-structure-to-v4`). Det är byggarens val.
- Filament 4 → 5 med `vendor/bin/filament-v5`, tillsammans med Livewire 4.
  Konfignycklar i `config/livewire.php` byter namn (`layout` →
  `component_layout`). `wire:model` på ett omslutande element kräver nu
  `.deep`.
- Laravel 12 → 13. Pest 4, PHPUnit 12 och Tinker 3.
- Spatie-pluginen för Filament (medialibrary och settings) måste finnas för
  varje version. Kontrollera med `composer why-not <paket> <version>`. Ta
  inte kompatibiliteten ur minnet.

Ordningen ovan är förslaget. Säger Composer något annat, följ Composer och
skriv upp varför.

### Fas 2: Grund i den nya designen

- Tailwind 4 i frontenden med tokens från `globals.css`. Ljust läge som
  standard. Mörkt via `[data-theme="dark"]`, valet sparat i `localStorage`
  och satt före första målning (som `ThemeScript.tsx`).
- Blade-layout med header, footer och sektionshuvud
  (`01 ─── ETIKETT`) som Blade-komponent. Ordbilden kommer från
  `Wordmark.tsx`.
- Språkprefix, routes och omdirigeringar enligt Begränsningar. UI-text i
  Laravels språkfiler.
- `wire:navigate` mellan sidor, så att spelaren i fas 3 kan ligga kvar.
- GSAP via Vite. Rörelser som redan finns i sjeren-2026 (hero, register,
  kolofon, fastnålade case) porteras och måste startas om efter
  `wire:navigate`. Lenis är inte inkopplad i sjeren-2026 och följer inte
  med.

### Fas 3: Musik, alla släpp, släppsida och spelare

- Modellerna `Release` och `Track` med Filament-resurser (se Datamodell).
- En seeder som läser in de 22 släppen från `music.json`, med omslag men
  utan ljud. Många släpp är daterade 2019-09-29. Det är troligen
  uppladdningsdatumet på Bandcamp och Patrik får rätta det i admin.
- Listan: omslag i 4 kolumner, nyast först, filter per alias. Ritning:
  `Omslag.dc.html` och `OmslagMork.dc.html`.
- Släppsidan: `Slapp`, `SlappMork`, `SlappMobil` och `SlappMobilMork`.
- Spelarlisten med `@persist`: spelar MP3-förlyssningen, scrubbern visar
  spåren i albumet, och knappen "Köp spåret" lägger spåret i korgen.

### Fas 4: Butiken

- Korgen som Livewire-komponent i en låda från höger. Korgen ligger i
  sessionen och fungerar utan inloggning. Ritning: `Korg`.
- Kassan (`Kassa`): e-post, land och betalning med Stripe Checkout.
  Ordern skapas som obetald. Webhooken `checkout.session.completed` gör den
  betald och skapar köparens konto om det inte redan finns.
- Tacksidan (`Tack`) visar ordern som "behandlas" tills webhooken kommit,
  sedan nedladdningarna.
- Inloggning med länk (`LoggaIn`): signerad länk som bara kan användas en
  gång och slutar gälla efter den tid som står i inställningarna.
- Mina köp (`MinaKop`): nedladdningar med "N av M kvar", och ett tillstånd
  för när nedladdningarna är slut.
- Mejl: kvitto med inloggningslänk, och själva inloggningslänken.

### Fas 5: Portfolions innehåll

- Projekten som modell med Filament-resurs. Samma 16 projekt används i
  utvalda case, registret och /projekt. Importera från `projects.ts`.
- Startsidan och /om-mig byggs med block. Det som saknas läggs till som
  nya block: tidslinje, "arbetar med" och registret. Nya fält får nya block
  (till exempel siffrorna i marginalen på profilen).
- Innehållet från `about.ts` och `messages/*.json` flyttas oförändrat.

### Fas 6: Byte i drift

- Driftsätt på hostup: dokumentroten mot `public/`, `composer install` via
  SSH, tillgångar byggda lokalt, cron för `schedule:run`, Stripe-webhook mot
  produktionsadressen.
- Kontrollera varje gammal adress mot den nya appen innan DNS eller
  dokumentrot byts.
- Next-sajten ligger kvar orörd tills bytet är gjort.

## Datamodell för musik och butik

Det här är formen, inte en färdig migration.

- **Release**: slug, artist (alias som text), titel, releasedatum, omslag
  (medialibrary), beskrivning, minimipris för hela släppet, publicerad
  ja/nej. Har många Track.
- **Track**: release, position, titel, längd i sekunder, minimipris,
  MP3-förlyssning, WAV-fil (privat disk).
- **Order**: köpare (User), ordernummer, summa, valuta (SEK), land, status
  (obetald, betald, återbetald), Stripe-sessionens id.
- **OrderItem**: order, `purchasable` (Release eller Track), betalt belopp.
- **Download**: köprad, antal nedladdningar. Gränsen läses från
  inställningarna.
- **User**: köpare och Patrik i samma tabell. Bara Patrik släpps in i
  panelen.
- **Settings** (Spatie): max antal nedladdningar och hur länge en
  inloggningslänk gäller.

## Mina beslut (Patrik kan ändra dem)

1. **Adresser med `/se/` och `/en/`** som i Next-sajten, inte sjerencms
   nuvarande "svenska utan prefix". Då fungerar alla länkar som redan finns.
   Nya sidor får samma svenska slug på båda språken, som `/en/om-mig/`:
   `/se/musik/`, `/se/musik/{släpp}/`, `/se/kassa/`, `/se/mina-kop/`.
2. **Stripe Checkout** i stället för den nuvarande lösningen med
   PaymentIntent. Den sköter kortuppgifter, kan erbjuda PayPal och kan räkna
   moms. Byggaren väljer inbäddad eller hostad variant efter hur nära
   ritningen `Kassa` den kommer.
3. **Kvitton** är en länk till Stripes eget kvitto. Inga egna PDF:er i
   första versionen.
4. **Alias** är en textkolumn på Release. Filtret bygger på unika värden.
   Ingen egen modell.
5. **Ett helt släpp laddas ner som en ZIP med WAV-filer.** ZIP-filen byggs i
   förväg, inte när någon laddar ner. Att spara släppet skapar bara ett
   jobb. Ett schemalagt kommando (`schedule:run` via cron) bygger de
   ZIP-filer som saknas. Om det räcker på hostup beror på öppen fråga 2.
6. **Nya modeller för butiken** i stället för att lappa de gamla. Det
   finns ingen data att ta hänsyn till.
7. **sjeren-2026 arkiveras** efter bytet. Repot raderas inte.

## Lämnat till byggaren

- Paketval för inloggning med länk, med Laravels signerade routes som
  utgångspunkt.
- Om musikskärmarna blir Livewire-komponenter eller block. Musiklistan bör
  kunna läggas på en sida som ett block.
- Hur nedladdningar strömmas (Laravels `response()->download()`/stream
  eller LiteSpeeds X-Sendfile), så länge det klarar stora filer på delat
  webbhotell.
- Exakt struktur för språkfilerna.
- Filaments katalogstruktur efter v4.
- Hur förlyssningen beter sig i detalj (hela spåret eller ett utdrag
  bestäms av vilken fil Patrik laddar upp).

## Fler beslut av mig, efter granskningen (Patrik kan ändra dem)

8. **Minimipris per spår är ett fält på Track**, och seedern sätter 20 kr.
   Det är Bandcamps minimum, kontrollerat för spåren på Past Voyages.
9. **Adresslistan för bytet** tas fram i fas 6 genom att gå igenom det som
   faktiskt ligger på sjeren.se. Omdirigeringar vars mål inte finns (till
   exempel `/en/services/` → `/en/tjanster/` och `/en/reputation/` →
   `/en/anseende/`) pekar i stället på startsidan för samma språk. Filerna
   under `/files/` (CV-PDF:erna) följer med som statiska filer i
   `public/files/`.
10. **PayPal går bara via Stripe Checkout**, aldrig som egen integration.
    Webhooken hanterar därför även betalningar som bekräftas senare
    (`checkout.session.async_payment_succeeded`).

## Öppna frågor (Patriks)

Ingen av dem blockerar fas 0–1. Svaren behövs före fas 3 (lagring) och fas
4 (butiken).

1. **Moms.** Är Patrik momsregistrerad, eller omfattas han av
   momsbefrielsen för små företag? Om han är registrerad: OSS för köpare i
   EU, Stripe Tax eller inte, och ingår momsen i minimipriserna? Svaret
   avgör om kassans landsteg finns kvar, vilka fält ordern har och vad det
   står i korgen ("Moms ingår").
2. **WAV-filernas storlek och hostups gränser.** 122 spår, cirka 14,6
   timmar, blir ungefär 9 GB i 16 bit/44,1 kHz och ungefär 15 GB i
   24 bit/48 kHz, plus ZIP-filerna. Vilken bitdjup har masterfilerna, och
   hur mycket diskutrymme, `upload_max_filesize` och `max_execution_time`
   har kontot? Svaret avgör om R2 behövs från början och hur ZIP-filerna
   byggs.
3. **Köpvillkor, ångerrätt och integritet.** Vid nedladdning behövs ett
   uttryckligt samtycke till att leveransen börjar direkt och att
   ångerrätten då upphör. En butik behöver också köpvillkor och en
   integritetspolicy. Texten måste komma från Patrik.
4. **Mejltjänst och avsändaradress** för kvitton och inloggningslänkar.
   Det kan vara hostups SMTP eller en tjänst som Postmark eller Resend.
5. **PayPal i första versionen,** ja eller nej.
