// Lifecycle for page motions under wire:navigate. An element with
// data-motion="<name>" gets the init registered for that name every time a
// page arrives, and the cleanup it returns runs before the next page swaps in.
//
// livewire:navigated also fires on the first page load, so there is no
// DOMContentLoaded handler: with both, every motion would start twice.
//
// <html data-motion-active> holds how many motions run right now. Leaving
// a page must bring it back to 0, otherwise a ticker or scroll listener leaked.

const motions = new Map();
let cleanups = [];

export function defineMotion(name, init) {
    motions.set(name, init);
}

function report() {
    document.documentElement.dataset.motionActive = String(cleanups.length);
}

function start() {
    stop();

    document.querySelectorAll('[data-motion]').forEach((el) => {
        const init = motions.get(el.dataset.motion);
        if (!init) return;

        cleanups.push(init(el) ?? (() => {}));
    });

    report();
}

function stop() {
    cleanups.forEach((cleanup) => cleanup());
    cleanups = [];
    report();
}

document.addEventListener('livewire:navigating', stop);
document.addEventListener('livewire:navigated', start);
