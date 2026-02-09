import { computed, onMounted, onUnmounted, ref } from 'vue'

/**
 * @typedef {Object} MotionDefaults
 * @property {number} duration
 * @property {number} delay
 * @property {number} stagger
 * @property {string} rootMargin
 * @property {number|number[]} threshold
 * @property {boolean} once
 * @property {string} initialClass
 * @property {string} revealedClass
 * @property {string} hiddenAttr
 * @property {string} revealedAttr
 * @property {boolean} bypassObserver
 */

const DEFAULTS = Object.freeze({
    duration: 0.6,
    delay: 0,
    stagger: 80,
    rootMargin: '0px 0px -10% 0px',
    threshold: 0.15,
    once: true,
    initialClass: 'motion-reveal--initial',
    revealedClass: 'motion-reveal--visible',
    hiddenAttr: 'data-motion-hidden',
    revealedAttr: 'data-motion-revealed',
    bypassObserver: true
})

/**
 * Motion accessibility preferences and shared safe defaults.
 * @param {Object} [options]
 * @param {boolean} [options.ssrReducedMotion=true] Initial reduced-motion value for SSR.
 * @param {Partial<MotionDefaults>} [options.defaults] Override default motion tokens.
 */
export function useMotionPreferences(options = {}) {
    const {
        ssrReducedMotion = true,
        defaults = {}
    } = options

    const prefersReducedMotion = ref(ssrReducedMotion)
    let mediaQuery = null
    let removeListener = null

    const baseDefaults = {
        ...DEFAULTS,
        ...defaults
    }

    const motionDefaults = computed(() => ({
        ...baseDefaults,
        duration: prefersReducedMotion.value ? 0 : baseDefaults.duration,
        delay: prefersReducedMotion.value ? 0 : baseDefaults.delay,
        stagger: prefersReducedMotion.value ? 0 : baseDefaults.stagger,
        bypassObserver: prefersReducedMotion.value ? true : baseDefaults.bypassObserver
    }))

    const shouldAnimate = computed(() => !prefersReducedMotion.value)

    const setupMediaQuery = () => {
        if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') {
            return
        }

        mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')
        prefersReducedMotion.value = mediaQuery.matches

        const handleChange = event => {
            prefersReducedMotion.value = event.matches
        }

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleChange)
            removeListener = () => mediaQuery?.removeEventListener('change', handleChange)
            return
        }

        if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleChange)
            removeListener = () => mediaQuery?.removeListener(handleChange)
        }
    }

    onMounted(setupMediaQuery)
    onUnmounted(() => {
        removeListener?.()
        removeListener = null
        mediaQuery = null
    })

    return {
        prefersReducedMotion,
        shouldAnimate,
        motionDefaults
    }
}
