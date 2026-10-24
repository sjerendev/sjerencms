import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { defineMotion } from '../motion';

gsap.registerPlugin(ScrollTrigger);

/**
 * Cap on how far you scroll while the case is pinned, as a share of the
 * window height. Below the cap the distance follows the image one to one, so
 * the motion feels as fast as the scroll; only a longer image moves faster.
 */
const MAX_SCROLL = 1.8;

// Port of CaseImage.tsx. The whole case is pinned, not just the frame, and the
// screenshot scrolls inside the frame.
defineMotion('case-pin', (el) => {
    const frame = el.querySelector('[data-case-frame]');
    const image = el.querySelector('[data-case-image]');
    const mm = gsap.matchMedia();

    // No pin for whoever asked for less motion: the image stands still on its top.
    mm.add('(prefers-reduced-motion: no-preference)', () => {
        const travel = () => Math.max(0, image.offsetHeight - frame.offsetHeight);

        // An image that fits the frame has nothing to scroll, so no pin.
        if (travel() < 40) return;

        const trigger = ScrollTrigger.create({
            trigger: el,
            start: 'top top',
            end: () => '+=' + Math.min(travel(), window.innerHeight * MAX_SCROLL),
            pin: true,
            pinSpacing: true,
            anticipatePin: 1,
            scrub: 0.5,
            invalidateOnRefresh: true,
            animation: gsap.fromTo(image, { y: 0 }, { y: () => -travel(), ease: 'none' }),
        });

        // The player bar shortens the case (app.css), so the pin is measured again.
        // player.js sets data-player on every play; refresh only when it changes.
        const html = document.documentElement;
        const observer = new MutationObserver((records) => {
            if (records.some((record) => record.oldValue !== html.getAttribute('data-player'))) {
                ScrollTrigger.refresh();
            }
        });
        observer.observe(html, { attributeFilter: ['data-player'], attributeOldValue: true });

        // kill() reverts the pin and takes the .pin-spacer with it.
        return () => {
            observer.disconnect();
            trigger.kill();
        };
    });

    return () => mm.revert();
});
