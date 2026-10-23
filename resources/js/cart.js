// The cart drawer (app/Livewire/CartDrawer.php). player.js dispatches
// `cart:add` {type, id, amount} on document; it goes to the component as
// `cart-add`, which answers with `cart-open` and `cart-updated` {count}.
// [data-cart-open] opens the dialog, [data-cart-count] shows the count.

const dialog = () => document.querySelector('[data-cart-dialog]');

document.addEventListener('cart:add', (event) => window.Livewire?.dispatch('cart-add', event.detail));

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-cart-open]')) dialog()?.showModal();
});

// An open dialog would be cached with its `open` attribute and come back after
// Back as a non-modal one (no backdrop). Close it when navigation starts, and
// close one that was restored anyway.
document.addEventListener('livewire:navigate', () => dialog()?.close());
document.addEventListener('livewire:navigated', () => {
    const d = dialog();
    if (d?.open && !d.matches(':modal')) d.close();
});

// Livewire loads after the page's own scripts, so wait for it if it is not there yet.
function listen() {
    Livewire.on('cart-open', () => {
        const d = dialog();
        if (d && !d.open) d.showModal();
    });
    Livewire.on('cart-updated', ({ count }) => {
        document.querySelectorAll('[data-cart-count]').forEach((el) => { el.textContent = count; });
    });
}

if (window.Livewire) listen();
else document.addEventListener('livewire:init', listen, { once: true });
