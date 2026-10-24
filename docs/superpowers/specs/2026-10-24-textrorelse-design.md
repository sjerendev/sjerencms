# Textrörelse: mikroanimation per textfält

Datum: 2026-10-24. Status: utkast, väntar på Patriks godkännande.

## Avsikt

Patrik ska fylla sajten med innehåll och arbeta med mikroanimationer medan
han gör det. Idag kan han inte få en text att röra sig. Heron har till
exempel `intro_one` och `intro_two`, men de står still och det går inte att
ändra i admin.

Han vill kunna slå på en rörelse för de flesta texter, en text i taget, och
ställa in den i Filament utan att någon behöver röra koden.

## Mål

1. De flesta textfält i blocken med ny design får en rörelseinställning i
   admin. Fälten räknas upp under "Vilka fält".
2. Rörelsen är **av** som standard. En text som inte har slagits på står
   still, precis som idag.
3. När Patrik slår på rörelsen för en text glider den **upp underifrån och
   tonar in** när den kommer in i bild. Det är standardinställningen.
4. Rörelsen spelas **en gång** och sedan står texten kvar. Den tonar inte
   ut och spelas inte igen när man scrollar tillbaka.
5. Inställningarna per text:
   - **Riktning:** underifrån (standard), uppifrån, från vänster eller från
     höger.
   - **Avstånd:** hur långt bort texten startar. Riktningen bestämmer åt
     vilket håll. Patrik föreslog ett tal med tecken (negativt från vänster
     och uppifrån, positivt underifrån och från höger). Här ersätts det av
     riktning plus avstånd, som är samma sak uppdelad på två fält (mitt
     beslut).
   - **Delning:** hela stycket (standard), per rad, per ord eller per
     bokstav.
   - **Stagger:** tiden mellan raderna, orden eller bokstäverna. Fältet
     syns bara när texten delas.
   - **Varaktighet:** hur lång tid intoningen tar.
   - **Fördröjning:** hur länge rörelsen väntar efter att texten kommit in
     i bild. Med den kan intro 2 vänta på intro 1, fast de är två fält.
   - **Easing:** mjuk (standard), skarp eller studsig.
   - **Mask:** av (standard) eller på. Med mask glider varje del fram
     bakom en osynlig kant, så att den syns först när den passerar kanten,
     som när ett tidningsblad vänds. Masken följer delningen: vid "per rad"
     har varje rad sin egen kant.
6. Samma rörelse fungerar efter `wire:navigate`, både när man kommer till
   en sida och när man går tillbaka.

## Icke-mål

Patrik har valt bort följande:

- Utgångshastighet och att texten spelas igen när den kommer tillbaka.
  Rörelsen spelas en gång.
- Oskärpa som klarnar.
- En startpunkt som går att ställa in, alltså var på skärmen rörelsen
  börjar.

Mitt beslut, eftersom det lätt blir för mycket:

- Rotation och skalning.
- Rörelse som scrollen styr direkt (scrub).
- Sparade stilar eller en lista med förval att välja från.
- En global standard som slår på rörelsen för alla texter.
- Rörelse för bilder.

Ingår inte heller:

- De gamla blocken utan ny design (CallToAction, FullText, ImageText,
  StickyCarousel, Ticker och StaggerText). De har ingen vy och visas inte.
- StaggerText-blocket ändras inte.
- Text som kommer från projekten och inte från blockets fält: kund,
  rubrik och tjänster i registret och de utvalda casen. Den står still i
  första versionen (Patriks svar).

## Anti-mål

Det här vore misslyckanden även om allt tekniskt fungerar:

- **En text blir aldrig synlig.** Kraschar skriptet, laddas det inte eller
  missar observatören texten, så ska texten ändå synas efter en kort stund.
  Texten får aldrig fastna osynlig.
- **Texten blinkar.** En text högst upp på sidan får inte först visas
  färdig, försvinna och sedan animeras in.
- **Sidan hoppar.** Raderna och orden får inte byta plats eller ändra
  sidans höjd när texten delas upp eller när animationen är klar.
- **Skärmläsare läser bokstav för bokstav.** Den delade texten ska läsas
  som en vanlig mening.
- **Rörelser läcker vid sidbyte.** `<html data-motion-active>` ska gå
  tillbaka till samma värde som före besöket.
- **Sajten ser ut som en mall** där allt flyger in. Designen är "Redaktion
  på ljust papper" (`sjeren-2026/DESIGN.md`). Därför är rörelsen av som
  standard och stäms per text.

## Begränsningar

- **Reducerad rörelse** (`prefers-reduced-motion: reduce`): texten visas
  direkt, utan rörelse och utan att delas upp.
- **Motorn** är GSAP, som redan finns (3.14.2). SplitText ingår i
  `gsap`-paketet sedan 3.13 och är gratis. Inga nya beroenden.
- **Livscykeln** går via `defineMotion` i `resources/js/motion.js`. Det som
  startas städas upp i den cleanup som `init` returnerar: delningen backas,
  ScrollTrigger och tweens dödas.
- **Rader beror på bredden.** Delning per rad måste räknas om när
  fönstret ändrar bredd, men bara innan texten har spelats. Efter det står
  texten kvar som vanlig text.
- **Texten kommer från fälten oförändrad.** Komponenten escapar och
  skriver ut texten precis som vyerna gör idag (radbrytningar, `e()`). Den
  hittar inte på någon text.
- **Admin är på engelska** (`.claude/rules/site.md`). Etiketterna i
  rörelsepanelen blir engelska, till exempel "Motion", "Direction" och
  "Split".
- **Sparade sidor fortsätter fungera.** En sida utan rörelsedata sparas
  och visas som idag. Ingen migration av innehållet behövs.
- **Pest-tester körs bara mot sqlite `:memory:`.** Inga
  databasraderande kommandon. Detsamma gäller hela repot.

## Det Patrik bestämt

- Rörelsen är av tills han slår på den per text.
- Standard när den slås på: upp underifrån och tona in.
- Spelas en gång och står sedan kvar.
- Med från min lista: mask, fördröjning och easing. Bortvalt: oskärpa och
  startpunkt.
- Han vill ha möjligheten på de flesta texterna.
- Samma rörelse på båda språken. Den ställs in en gång.
- Projektens texter i registret och de utvalda casen ingår inte i första
  versionen.

## Mina beslut (Patrik kan ändra dem)

1. **Panelen i admin.** Varje text som kan röra sig får en egen hopfälld
   panel, "Motion", direkt under fältet. Panelen är stängd när formuläret
   öppnas. Den har en strömbrytare, och resten av inställningarna syns
   först när den är på.
2. **Riktning plus avstånd** i stället för ett tal med tecken (se Mål 5).
3. **Standardvärden** när rörelsen slås på:

   | Inställning | Standard |
   |---|---|
   | Riktning | underifrån |
   | Avstånd | 24 px |
   | Delning | hela stycket |
   | Stagger | 0,06 s |
   | Varaktighet | 0,8 s |
   | Fördröjning | 0 s |
   | Easing | mjuk |
   | Mask | av |

   Easing-namnen motsvarar GSAP:s kurvor: mjuk = `power3.out`, skarp =
   `expo.out` och studsig = `back.out(1.7)`.
4. **Avståndet räknas i pixlar.** Med mask ignoreras det: delen startar
   alltid helt bakom kanten, alltså hela sin egen höjd eller bredd bort.
5. **När rörelsen startar:** en gång, när textens överkant når 85 % ned på
   skärmen. En text som redan syns när sidan laddas spelas direkt. Värdet
   är fast och inte en inställning.
6. **Repeaters.** Fält inne i en repeater, som frågorna i FAQ eller
   tidslinjens rader, får **en** inställning per fält som gäller alla rader
   i repeatern. Panelen ligger på repeatern, inte på varje rad. Varje rad
   startar för sig när den kommer in i bild, så att en lång lista inte
   spelas klart utanför skärmen.
7. **Fält som blir flera element** (ett stycke per rad i profilens
   kolumner, en rad per tjänst eller etikett, rader med `<br>` i
   sidheron) har en inställning för hela fältet. Komponenten svarar för
   varje element för sig, så att deras stil och avstånd är som idag.
   Fördröjningen gäller fältet, och staggern löper över alla element i
   fältet i ordning.
8. **Efter att texten spelats** backas delningen, så att texten är vanlig
   text igen och ser ut som innan, med samma radbrytningar, spärrning och
   färger. Under själva rörelsen får kerningen vid delning per bokstav
   skilja sig något.
9. **Synlig utan skript är ett krav.** Utan JavaScript, med `noscript`
   eller om skriptet fallerar ska texten synas. En timeout räcker inte
   ensam.
10. **Samma rörelse på båda språken** (Patriks svar). Rörelsen ställs in
    på den svenska sidan. Den engelska sidan med samma slug (startsidan
    hör ihop med startsidan) använder samma inställningar. Block paras
    ihop efter plats i ordningen och typ, så att block nummer 3 av typen
    `hero` på engelska använder inställningarna från block nummer 3 på
    svenska om det också är `hero`. Annars står den engelska texten
    still. På den engelska sidan visar panelen bara en rad om att rörelsen
    ställs in på den svenska sidan (mitt beslut om hur ihopparningen går
    till).
11. **Ingen förhandsvisning i admin.** Patrik ser en ändring genom att
    ladda om sidan på sajten.
12. **En komponent på varje sida.** En Blade-komponent skriver ut texten
   med rörelsens inställningar som data-attribut, och en ny rörelse i
   `resources/js/motions/` spelar den. Ett block kopplar in rörelsen genom
   att byta ut sin vanliga utskrift av texten mot komponenten.

## Vilka fält

Alla synliga textfält i blocken med ny design, utom:
- fält som redan har en egen rörelse: heroblockets `phrases` (rotatorn) och
  kolofonens `tools` (bandet),
- fält som inte är text: adresser (`cv_url`), sektionsnummer som bara
  styr ordningen, och klassfält.

Blocken är Hero, PageHero, HeadlineText, ServiceCards, FaqAccordion,
Profile, Timeline, Stack, FeaturedCases, Register och Colophon (utom
`tools`). Byggaren gör den exakta listan per block och visar den för
Patrik i planen.

## Lämnat till byggaren

- Hur inställningarna lagras i blockets JSON, till exempel en nyckel
  `motion` med en post per fält. Det går bra så länge sidor utan nyckeln
  fortsätter fungera.
- Hur det döljs innan rörelsen startar utan att något blinkar, och hur
  texten ändå blir synlig om skriptet fallerar (Anti-mål).
- Hur Filament-panelen byggs som en återanvändbar del som varje block
  anropar med fältets namn.
- Om sektionshuvudet (`01 ─── ETIKETT`) får rörelse via sina fält `number`
  och `label` eller lämnas utanför. Det styrs av vad som blir enklast i
  komponenten.
- Testerna. Minst: komponenten skriver ut texten oförändrad och utan
  rörelseattribut när rörelsen är av, ett block sparas och läses tillbaka
  med rörelsedata, och en kontroll i webbläsaren (agent-browser) att texten
  spelas en gång, att den syns med reducerad rörelse och att
  `data-motion-active` går tillbaka till samma värde vid sidbyte.
