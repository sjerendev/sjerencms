// Light or dark mode. The inline script in the layout's <head> sets it before
// first paint; this file keeps it in sync with the toggle and across
// wire:navigate.

const root = document.documentElement;

function apply(theme) {
    root.dataset.theme = theme;

    document.querySelectorAll('[data-theme-pick]').forEach((button) => {
        const on = button.dataset.themePick === theme;
        button.dataset.on = String(on);
        button.setAttribute('aria-pressed', String(on));
    });
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-pick]');
    if (!button) return;

    apply(button.dataset.themePick);
    try {
        localStorage.setItem('theme', button.dataset.themePick);
    } catch {
        // Private mode: the choice lasts for this page view only.
    }
});

// Livewire's advice against a dark-mode flash: set it again in onSwap,
// before the new page is painted.
document.addEventListener('livewire:navigating', (event) => {
    const current = root.dataset.theme ?? 'light';
    event.detail.onSwap(() => apply(current));
});

document.addEventListener('livewire:navigated', () => apply(root.dataset.theme ?? 'light'));
