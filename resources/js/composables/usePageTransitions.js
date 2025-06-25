import { gsap } from 'gsap'

export function usePageTransitions() {
    const pageEnter = (el, done) => {
        gsap.fromTo(el,
            { opacity: 0, y: 20 },
            { opacity: 1, y: 0, duration: 0.5, onComplete: done }
        )
    }

    const pageLeave = (el, done) => {
        gsap.to(el, {
            opacity: 0,
            y: -20,
            duration: 0.3,
            onComplete: done
        })
    }

    return {
        pageEnter,
        pageLeave
    }
}
