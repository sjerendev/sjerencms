// The preview player. One <audio> in the persisted bar (@persist('player')),
// so it keeps playing across wire:navigate. Each page embeds its queues as
// <script type="application/json" data-music>; a click copies the release it
// plays, because that script is gone after the next navigation.
//
// Out: `player:state` {release, track, playing, time, duration} and
// `cart:add` {type, id, amount}, both on document. cart.js listens for the
// second.

const PLAY = '<path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/>';
const PAUSE = '<rect x="2" y="1" width="3.5" height="12" fill="currentColor"/><rect x="8.5" y="1" width="3.5" height="12" fill="currentColor"/>';

const html = document.documentElement;
const audio = () => document.querySelector('#player audio');
const bar = () => document.querySelector('[data-player-root]');

let queue = null;
let index = 0;

const current = () => queue?.tracks[index] ?? null;

function music() {
    try {
        return JSON.parse(document.querySelector('script[data-music]')?.textContent || '{}');
    } catch {
        return {};
    }
}

function clock(seconds) {
    const s = Math.max(0, Math.floor(seconds || 0));
    return `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`;
}

function setIcon(button, playing) {
    const svg = button.querySelector('svg');
    // Only on a change: render() runs on every timeupdate, and a shape replaced
    // between mousedown and mouseup loses the click (Safari drops it).
    if (svg && svg.dataset.playing !== String(playing)) {
        svg.innerHTML = playing ? PAUSE : PLAY;
        svg.dataset.playing = String(playing);
    }
    const label = playing ? button.dataset.labelPause : button.dataset.labelPlay;
    if (label) button.setAttribute('aria-label', label);
}

// Start `release` at track `i`, `fraction` of the way in.
function play(release, i, fraction = 0) {
    queue = structuredClone(release);
    index = i;
    load(fraction);
}

function load(fraction = 0) {
    const a = audio();
    const track = current();
    if (!a || !track) return;

    a.src = track.preview;
    if (fraction > 0) {
        a.addEventListener('loadedmetadata', () => { a.currentTime = fraction * a.duration; }, { once: true });
    }
    a.play().catch(() => {});

    bar().hidden = false;
    html.dataset.player = 'on';
    render();
}

// Stop and hide; the next play brings the bar back.
function close() {
    const a = audio();
    if (a) {
        a.pause();
        a.removeAttribute('src');
        a.load(); // Drops the preview it was loading.
    }
    queue = null;
    bar().hidden = true;
    delete html.dataset.player;
    syncPage(false, 0, 0);

    document.dispatchEvent(new CustomEvent('player:state', {
        detail: { release: null, track: null, playing: false, time: 0, duration: 0 },
    }));
}

function toggle() {
    const a = audio();
    if (!a || !current()) return;
    a.paused ? a.play().catch(() => {}) : a.pause();
}

function render() {
    const a = audio();
    const track = current();
    if (!a || !track) return;

    const playing = !a.paused;
    const time = a.currentTime || 0;
    const duration = Number.isFinite(a.duration) && a.duration > 0 ? a.duration : track.seconds;
    const percent = `${Math.min(100, (time / duration) * 100)}%`;
    const each = (selector, fn) => document.querySelectorAll(selector).forEach(fn);

    // The bar.
    each('[data-player-title]', (el) => { el.textContent = track.title; });
    each('[data-player-meta]', (el) => { el.textContent = `${queue.alias} · ${queue.title}`; });
    each('[data-player-alias]', (el) => { el.textContent = queue.alias; });
    each('[data-player-time]', (el) => { el.textContent = `${clock(time)} / ${clock(duration)}`; });
    each('[data-player-progress]', (el) => { el.style.width = percent; });
    each('[data-player-price]', (el) => { el.textContent = track.price; });
    each('[data-player-cover]', (el) => {
        if (queue.cover && el.getAttribute('src') !== queue.cover) el.src = queue.cover;
    });
    each('[data-player-toggle]', (el) => {
        el.dataset.labelPlay = bar().dataset.labelPlay;
        el.dataset.labelPause = bar().dataset.labelPause;
        setIcon(el, playing);
    });
    each('[data-player-root] [data-cart-add]', (el) => {
        el.dataset.id = track.id;
        el.dataset.amount = track.price;
        el.hidden = !track.buyable; // No WAV yet, nothing to sell.
    });

    syncPage(playing, time, duration);

    document.dispatchEvent(new CustomEvent('player:state', {
        detail: { release: queue.slug, track: track.id, playing, time, duration },
    }));
}

// The play controls and the scrubber on the current page.
function syncPage(playing, time, duration) {
    const track = current();
    const mine = (slug) => queue?.slug === slug;

    document.querySelectorAll('[data-play-release]').forEach((button) => {
        const on = mine(button.dataset.playRelease) && playing;
        button.setAttribute('aria-pressed', String(on));
        setIcon(button, on);
    });

    document.querySelectorAll('[data-play-track]').forEach((button) => {
        const isCurrent = mine(button.dataset.release) && Number(button.dataset.playTrack) === track?.id;
        button.setAttribute('aria-pressed', String(isCurrent && playing));
        setIcon(button, isCurrent && playing);
        button.closest('li')?.toggleAttribute('data-current', isCurrent);
    });

    syncTimeline(time, duration);
}

const WAVE = '<svg width="100%" height="22" viewBox="0 0 200 22" preserveAspectRatio="none" aria-hidden="true"><path d="M0 11 Q 6 1 12 11 T 24 11 T 36 11 T 48 11 T 60 11 T 72 11 T 84 11 T 96 11 T 108 11 T 120 11 T 132 11 T 144 11 T 156 11 T 168 11 T 180 11 T 192 11 T 200 11" fill="none" stroke="var(--bar-sound)" stroke-width="1.6"/></svg>';

// The bar's timeline: one segment per playable track of the queue, width =
// duration, the title above it. Built once per queue (the bar is persisted),
// then only the progress moves.
function syncTimeline(time, duration) {
    const box = document.querySelector('[data-player-timeline]');
    if (!box) return;

    const key = queue ? `${queue.slug}:${queue.tracks.map((t) => t.id).join(',')}` : '';
    if (box.dataset.key !== key) {
        box.dataset.key = key;
        box.replaceChildren(...(queue?.tracks ?? []).map((t) => {
            const segment = document.createElement('div');
            segment.dataset.scrubTrack = t.id;
            segment.style.flex = `${t.seconds} 1 0`;
            segment.className = 'flex min-w-0 cursor-pointer flex-col gap-[4px]';
            segment.innerHTML = `<span data-scrub-title class="lbl truncate text-[rgba(237,236,234,0.62)]"></span>
                <span class="relative flex h-[22px] items-center">
                    <span class="block h-px w-full bg-[rgba(237,236,234,0.3)]"></span>
                    <span data-scrub-progress style="width: 0%" class="absolute inset-y-0 left-0 flex items-center overflow-hidden bg-bar">${WAVE}</span>
                </span>`;
            segment.querySelector('[data-scrub-title]').textContent = t.title;
            return segment;
        }));
    }

    const track = current();
    box.querySelectorAll('[data-scrub-track]').forEach((segment) => {
        const isCurrent = Number(segment.dataset.scrubTrack) === track?.id;
        segment.querySelector('[data-scrub-title]').classList.toggle('text-bar-sound', isCurrent);
        segment.querySelector('[data-scrub-progress]').style.width = isCurrent ? `${Math.min(100, (time / duration) * 100)}%` : '0%';
    });
}

document.addEventListener('click', (event) => {
    const target = event.target;

    const release = target.closest('[data-play-release]');
    if (release) {
        const data = music()[release.dataset.playRelease];
        if (!data) return;
        mineToggleOr(data.slug, null, () => play(data, 0));
        return;
    }

    const trackButton = target.closest('[data-play-track]');
    if (trackButton) {
        const data = music()[trackButton.dataset.release];
        const i = data?.tracks.findIndex((t) => t.id === Number(trackButton.dataset.playTrack)) ?? -1;
        if (i < 0) return;
        mineToggleOr(data.slug, i, () => play(data, i));
        return;
    }

    const segment = target.closest('[data-scrub-track]');
    if (segment) {
        const data = queue; // The segments are the bar's, built from the queue.
        const i = data?.tracks.findIndex((t) => t.id === Number(segment.dataset.scrubTrack)) ?? -1;
        if (i < 0) return;
        const rect = segment.getBoundingClientRect();
        const fraction = Math.min(1, Math.max(0, (event.clientX - rect.left) / rect.width));
        const a = audio();
        if (queue?.slug === data.slug && index === i && Number.isFinite(a.duration)) {
            a.currentTime = fraction * a.duration;
            a.play().catch(() => {});
        } else {
            play(data, i, fraction);
        }
        return;
    }

    if (target.closest('[data-player-toggle]')) {
        toggle();
        return;
    }

    if (target.closest('[data-player-close]')) {
        close();
        return;
    }

    const buy = target.closest('[data-cart-add]');
    if (buy) {
        // The album form: the amount is what the buyer typed. The server checks it.
        const input = buy.closest('form')?.querySelector('[data-amount-input]');
        const amount = Number(input ? input.value : buy.dataset.amount);
        document.dispatchEvent(new CustomEvent('cart:add', {
            detail: { type: buy.dataset.cartAdd, id: Number(buy.dataset.id), amount },
        }));
    }
});

// Pause/resume when the click is on what is already playing (the release, or
// that exact track); otherwise start something new.
function mineToggleOr(slug, i, start) {
    if (queue?.slug === slug && current() && (i === null || i === index)) {
        toggle();
    } else {
        start();
    }
}

// Media events do not bubble; capture them on document.
['play', 'pause', 'timeupdate', 'loadedmetadata'].forEach((type) => {
    document.addEventListener(type, (event) => {
        if (event.target === audio()) render();
    }, true);
});

document.addEventListener('ended', (event) => {
    if (event.target !== audio() || !queue) return;
    if (index < queue.tracks.length - 1) {
        index += 1;
        load();
    } else {
        render(); // The queue is done: the bar stays, showing play.
    }
}, true);

// Livewire strips attributes from <html> on swap (as with the theme).
document.addEventListener('livewire:navigating', (event) => {
    const on = html.dataset.player;
    event.detail.onSwap(() => { if (on) html.dataset.player = on; });
});

document.addEventListener('livewire:navigated', () => {
    if (queue) render();
});
