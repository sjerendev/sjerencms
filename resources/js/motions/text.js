import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import { defineMotion } from '../motion';

gsap.registerPlugin(ScrollTrigger, SplitText);

/** The admin's names for GSAP's curves. */
const EASES = { soft: 'power3.out', sharp: 'expo.out', bouncy: 'back.out(1.7)' };

/** Where the text comes from: the axis and which way along it. */
const FROM = { bottom: ['y', 1], top: ['y', -1], left: ['x', -1], right: ['x', 1] };

const STYLES = 'opacity,visibility,transform';

// A text with data-text-motion (TextMotion::normalize) plays once when its top
// reaches 85% down the window, then is plain text again. app.css keeps it
// hidden until it is armed here, and shows it after 2 s if that never happens.
// It is split only when it starts: split text carries aria-hidden pieces, so
// until then it stays whole for screen readers and is hidden with opacity alone.
defineMotion('text', (el) => {
    const motion = JSON.parse(el.dataset.textMotion);
    const pieces = [...el.querySelectorAll('[data-motion-piece]')];
    const targets = pieces.length ? pieces : [el];

    // Going back, Livewire may show the page as it was left: start clean.
    delete el.dataset.textMotionArmed;
    gsap.set(targets, { clearProps: STYLES });

    // Reduced motion, or the 2 s failsafe already showed it because the bundle came late:
    // leave it standing, a second entrance would flash.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || getComputedStyle(el).visibility === 'visible') {
        el.dataset.textMotionArmed = '';
        return;
    }

    // The whole text behind a mask still needs its lines: they slide together, each behind its own edge.
    const split = motion.split === 'none' ? (motion.mask ? 'lines' : null) : motion.split;
    const [axis, sign] = FROM[motion.from];
    let splitText = null;
    let tween = null;

    // A mask clip reaches 0.25em past the line (app.css .tm-mask) and Å's ring sits above the
    // line box, so a vertical start sits 200% away: at 150% the ring peeked under the edge.
    const start = motion.mask
        ? { [`${axis}Percent`]: (axis === 'y' ? 200 : 100) * sign }
        : { opacity: 0, [axis]: motion.distance * sign };

    const play = (items) => {
        tween = gsap.from(items, {
            ...start,
            duration: motion.duration,
            delay: motion.delay,
            ease: EASES[motion.ease],
            stagger: motion.split === 'none' ? 0 : motion.stagger,
            onComplete: () => {
                // Plain text again: same markup, line breaks and kerning as before it moved.
                splitText?.revert();
                splitText = null;
                gsap.set(targets, { clearProps: STYLES });
            },
        });

        return tween;
    };

    let started = false;
    const begin = () => {
        if (started) return;
        started = true;
        gsap.set(targets, { clearProps: 'opacity' });

        if (split) {
            // autoSplit redoes the lines when the width changes or fonts arrive while it moves.
            splitText = SplitText.create(targets, {
                type: split === 'chars' ? 'words,chars' : split,
                // The pieces and their masks get the class tm / tm-mask, styled in app.css.
                linesClass: 'tm',
                wordsClass: 'tm',
                charsClass: 'tm',
                mask: motion.mask ? split : undefined,
                autoSplit: split === 'lines',
                onSplit: (self) => play(self[split]),
            });
        } else {
            play(targets);
        }
    };

    gsap.set(targets, { opacity: 0 });
    el.dataset.textMotionArmed = '';

    // onLeave too: a page reloaded below the text starts past it.
    const trigger = ScrollTrigger.create({ trigger: targets[0], start: 'top 85%', once: true, onEnter: begin, onLeave: begin });

    return () => {
        trigger.kill();
        tween?.kill();
        splitText?.revert();
        gsap.set(targets, { clearProps: STYLES });
    };
});
