# Shop

Cart → checkout → Stripe hosted Checkout → webhook → Tack and Mina köp.
Prices are whole kronor (int) in the database; to Stripe they go as
`amount * 100` in `sek`. No Stripe.js and no public key: the buyer is
redirected to Stripe.

## Flow

1. **Cart.** `App\Shop\Cart` keeps the session `cart`
   (`'track:12' => [type, id, amount]`), no prices. Every `lines()` re-reads
   the database, drops what is no longer purchasable and clamps the amount
   to `min_price`..`Cart::MAX_AMOUNT` (100 000); a non-number becomes the
   minimum. The `CartDrawer` Livewire component sits on a `<dialog>` in the
   layout; `resources/js/cart.js` bridges the player's `cart:add` to it
   (`cart-add` → `cart-open`, `cart-updated`).
2. **Checkout.** `CheckoutForm` takes email + consent (`consented_at`),
   re-reads the cart, refuses a total under `Cart::MIN_TOTAL` (3 kr), creates
   an unpaid `Order` + `OrderItem`s (title and amount are snapshots), pushes
   the id on session `orders` and redirects to
   `StripeCheckout::start()`. `payload()` is built from the order alone and
   carries `client_reference_id`, `metadata.order_id`, the order's own locale
   in the URLs and `automatic_tax` from `STRIPE_TAX`.
3. **Webhook.** `StripeWebhook` is the only place an order becomes paid.
4. **Tack.** `ShowThanks` + `OrderStatusPanel` poll until paid, then show the
   downloads and `receipt_url` (Stripe's own HTML receipt, via
   `StripeCheckout::receiptUrl`).
5. **Mina köp.** `ShowAccount`, behind `auth`, lists the user's paid orders.

## Routes

Outside `{locale}`: `stripe.webhook` (POST `stripe/webhook`, no CSRF) and
`download` (`download/{item}`, linked from mail). In `{locale}`:
`shop.checkout` (`kassa`), `shop.thanks` (`kassa/tack/{order}`),
`shop.login` (`logga-in`), `shop.login.confirm` (`logga-in/{user}`, GET and
POST), `shop.logout` (POST `logga-ut`) and `shop.account` (`mina-kop`).

## Sessions

- `cart`: described above. `ShowThanks` clears it.
- `orders`: ids this browser started. With it, the paying browser sees
  Tack and can download without logging in. Tack never logs the buyer in
  (the address may be someone else's).
- Logout calls `session()->invalidate()`: it drops `orders` too, and the
  cart with it. Do not change it to `forget('auth')`.

## Correctness rules (do not simplify away)

- Price and total are computed on the server; the browser's amounts are
  only a request.
- **Webhook:** verified with `Webhook::constructEvent` (an empty
  `STRIPE_WEBHOOK_SECRET` aborts 500). Handles
  `checkout.session.completed` (only `payment_status=paid`),
  `checkout.session.async_payment_succeeded` (PayPal and other delayed
  methods) and `charge.refunded`. The session must match the order
  (`stripe_session_id`, `amount_total === total * 100`, `sek`), otherwise the
  order stays unpaid and a warning is logged. The unpaid→paid change is one
  conditional `UPDATE`, so a repeated event does nothing. Answers 200 for
  every handled outcome; a failed commit rolls back and answers 500 so
  Stripe retries. Receipt URL and mail failures are `report()`ed, never
  retried, and the order stays paid.
- The buyer's `User` is found case-insensitively by email or created with
  `name = email` and a random password. An `is_admin` account gets no login
  link.
- **Login links** (`App\Shop\LoginLinks`): signed, expiring, one active link
  per user (a new link replaces `login_nonce`, stored hashed). GET on
  `shop.login.confirm` only shows a button, POST consumes the nonce
  atomically and logs in, so a mail scanner cannot burn the link.
  `LoginForm` answers the same for unknown, admin and rate-limited
  addresses. Admins can never log in by link (`ConfirmLogin`).
- **Downloads:** `DownloadPurchase` needs a paid order and the owner
  (`auth()->id() === order.user_id`) or an order id in session `orders`
  (403 otherwise). A missing file is 404 and does not count. The counter is
  one atomic `UPDATE ... WHERE download_count < limit`, so it never passes
  the limit. WAV and ZIP live on the private `music` disk and only go out
  through this route.
- Refunded orders drop out of Mina köp, cannot download, and get 404 on
  Tack.
- Releases and tracks that have been sold cannot be deleted, only
  unpublished. Buy buttons show only when a WAV exists
  (`isPurchasable()`).
- `bootstrap/app.php` calls `trustHosts()`: login links use the request
  host, so only `APP_URL` and its subdomains are accepted. Production
  `APP_URL` must be the apex host; a wrong one answers 400 to every request.
- `users.is_admin` + `User::canAccessPanel()` decide `/admin`. Buyers never
  get in.

## Album ZIPs

`music:build-zips` (scheduled every minute, `withoutOverlapping(30)`) builds
a release's ZIP when all its tracks have a WAV and the ZIP is missing or its
`fingerprint` custom property (hash of the WAVs) differs. Saving a release
builds nothing; the next run does. Until then an album row says "förbereds".
The ZIP is the `zip` media collection on the `music` disk.

## Settings

`GeneralSettings::$download_limit` (default 5) and `$login_link_minutes`
(default 15), edited under "Shop" on the settings page. Code reads them at
use time, never as constants.

## Environment

- `STRIPE_SECRET`: `sk_test_…` locally, `sk_live_…` only in production.
- `STRIPE_WEBHOOK_SECRET`: `whsec_…`; locally the one `stripe listen` prints.
- `STRIPE_TAX=true`: turns on `automatic_tax`; needs a registered origin
  address in Stripe (default off, see `TODO-INNEHALL.md`).
- Mail: `MAIL_MAILER=log` locally; the receipt (`OrderPaid`, with a login
  link) and `LoginLink` then end up in `storage/logs/laravel.log`.

## Local work

- `stripe listen --forward-to http://sjerencms.test/stripe/webhook` in its
  own window.
- Test card `4242 4242 4242 4242`, any future expiry, CVC `123`.
- The scheduler: `php artisan schedule:run` by hand or `schedule:work`;
  production needs a cron entry for `schedule:run` (phase 6).
- Local orders are real rows in the local database. Never wipe it.

## Tests

Stripe is never called. `StripeCheckout` is mocked; webhook tests sign real
payloads and go through `Webhook::constructEvent`. Tests need WAVs on the
`music` disk (`giveWavs()`, a global helper defined in `tests/Feature/Music/ReleasePageTest.php`) before anything is
purchasable.
