# Innehåll som saknas

Text och design som behövs men inte kommer från Patrik än. Inget här får
fyllas i med påhittad copy.

- 404-sidan: bara svensk text och ingen design, samma som i sjeren-2026
  (`TODO-INNEHALL.md` där).
- Musik: rubriken på /en/musik är platshållare (`[EN] …` i
  `lang/en/music.php`).
- Musik: engelska etiketter översatta av Claude, Patrik granskar
  (`lang/en/music.php`).
- Musik: svensk text tagen ur ritningarna, Patrik granskar
  (`lang/sv/music.php`).
- Korgen: `[Korgen är tom]` är platshållare (`lang/{sv,en}/shop.php`, ritningen
  Korg visar ingen tom korg). Engelska etiketter översatta av Claude, Patrik
  granskar. "Moms ingår." står inte i korgen förrän momsfrågan är besvarad.
- Kassan: `[Samtycke: …]`, `[Betalningen kunde inte startas…]` och
  `[Summan måste vara minst 3 kr]` är platshållare enligt planen. Jag har
  också lagt `[Du måste godkänna för att gå vidare]` och `[Ange en giltig
  e-postadress]` som platshållare för valideringsfelen, eftersom ritningen
  inte visar några fel (`lang/{sv,en}/shop.php`). Engelska etiketter
  översatta av Claude, Patrik granskar. "Varav moms" och ritningens steg
  "Land" saknas tills momsfrågan är besvarad.
- Inloggning: `[Länken gäller inte längre. Skicka en ny.]` och mejlets ämne,
  text och knapp (`[Din inloggningslänk]`, `[Tryck på knappen…]`,
  `[Logga in]`) är platshållare (`lang/{sv,en}/shop.php`). Ritningen LoggaIn
  visar varken felet eller mejlet. Engelska etiketter översatta av Claude,
  Patrik granskar.
- Kvittomejlet: ämne, text, knapp och länk (`[Kvitto: order :number]`,
  `[Tack för ditt köp. Här är ditt kvitto.]`, `[Ladda ner]`, `[Kvitto ↗]`) är
  platshållare (`lang/{sv,en}/shop.php`). Ritningen Tack visar inte mejlet.
  Engelska är `[EN] …` med svensk text, som inloggningsmejlet.
- Tack och Mina köp: `[Betalningen behandlas]`, `[Förbereds]` (album utan zip)
  och `[Inga köp än]` är platshållare (`lang/{sv,en}/shop.php`); ritningarna
  visar inga av tillstånden. "Spår", "Album" och "köpt :date" är ur
  MinaKop-ritningen. Engelska etiketter översatta av Claude, meningarna är
  `[EN] …` med svensk text.
- Butiken: villkor och integritetspolicy finns inte (öppen fråga 3); samtycket
  i kassan är platshållare tills de finns och länkar ingenstans.
- Butiken: "Moms ingår." och "Varav moms" visas inte förrän momsfrågan
  (öppen fråga 1) är besvarad; `STRIPE_TAX` är av tills Stripe har en
  registrerad adress.
- Butiken: nedladdningsgräns (5) och länkens giltighet (15 min) är
  ritningarnas `[5]` och `[15]`; Patrik sätter de riktiga under Shop i
  inställningarna i Filament.
