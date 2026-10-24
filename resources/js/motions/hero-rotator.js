import gsap from 'gsap';
import { defineMotion } from '../motion';

/** Seconds a phrase stands still before the next one rolls up. */
const HOLD = 2.2;

// Port of HeroRotator.tsx. The phrase rolls up letter by letter and the next
// one comes from below in the same move, like a split-flap board. Two whole
// rows lie on top of each other instead of letter pairs, so every phrase keeps
// its own kerning. The server renders phrase 0 (data-out) and phrase 1
// (data-in, waiting below the edge); reduced motion leaves phrase 0 standing.
defineMotion('hero-rotator', (el) => {
    const phrases = JSON.parse(el.dataset.phrases || '[]');
    const label = el.parentElement.querySelector('.sr-only');
    const [outRow, inRow] = el.children;
    const mm = gsap.matchMedia();

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        // One phrase has nothing to roll to.
        if (phrases.length < 2) return;

        let index = 0;
        let tl;

        const letters = (row, text, mark) => {
            row.replaceChildren(
                ...[...text].map((char) => {
                    const span = document.createElement('span');
                    span.setAttribute(mark, '');
                    span.className = 'inline-block whitespace-pre';
                    span.textContent = char;
                    return span;
                }),
            );
        };

        const run = () => {
            const out = gsap.utils.toArray(outRow.children);
            const into = gsap.utils.toArray(inRow.children);
            // The distance is the box height: the incoming row stands just below the edge.
            const travel = el.offsetHeight;

            gsap.set(into, { y: travel });

            tl = gsap.timeline({
                delay: HOLD,
                onComplete: () => {
                    index = (index + 1) % phrases.length;
                    letters(outRow, phrases[index], 'data-out');
                    letters(inRow, phrases[(index + 1) % phrases.length], 'data-in');
                    label.textContent = phrases[index];
                    run();
                },
            });

            tl.to(out, { y: -travel, duration: 0.62, ease: 'power3.inOut', stagger: 0.03 }, 0);
            tl.to(into, { y: 0, duration: 0.62, ease: 'power3.inOut', stagger: 0.03 }, 0);
        };

        run();

        // Each lap makes a new timeline outside the matchMedia context, so kill the current one by hand.
        return () => tl.kill();
    });

    return () => mm.revert();
});
