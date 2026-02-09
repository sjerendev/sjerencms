import { computed, onUnmounted, watch } from 'vue'
import { useMotionPreferences } from './useMotionPreferences'

/**
 * @typedef {Object} InViewRevealOptions
 * @property {HTMLElement|import('vue').Ref<HTMLElement|null>|null} [root]
 * @property {string} [rootMargin]
 * @property {number|number[]} [threshold]
 * @property {boolean} [once]
 * @property {number} [stagger] Stagger in milliseconds.
 * @property {string|null} [itemSelector] Reveal children instead of the root target.
 * @property {string} [initialClass]
 * @property {string} [revealedClass]
 * @property {string} [hiddenAttr]
 * @property {string} [revealedAttr]
 * @property {boolean} [bypassReducedMotion]
 */

const STAGGER_INDEX_ATTR = 'data-motion-stagger-index'
const STAGGER_DELAY_VAR = '--motion-stagger-delay'

/**
 * In-view reveal helper with IntersectionObserver and reduced-motion fallback.
 * @param {InViewRevealOptions} [options]
 */
export function useInViewReveal(options = {}) {
    const {
        root = null,
        rootMargin,
        threshold,
        once,
        stagger,
        itemSelector = null,
        initialClass,
        revealedClass,
        hiddenAttr,
        revealedAttr,
        bypassReducedMotion
    } = options

    const {
        prefersReducedMotion,
        motionDefaults
    } = useMotionPreferences()

    const isClient = typeof window !== 'undefined' && typeof document !== 'undefined'
    const observedTargets = new Set()
    let observer = null

    const config = computed(() => ({
        rootMargin: rootMargin ?? motionDefaults.value.rootMargin,
        threshold: threshold ?? motionDefaults.value.threshold,
        once: once ?? motionDefaults.value.once,
        stagger: Math.max(0, stagger ?? motionDefaults.value.stagger),
        initialClass: initialClass ?? motionDefaults.value.initialClass,
        revealedClass: revealedClass ?? motionDefaults.value.revealedClass,
        hiddenAttr: hiddenAttr ?? motionDefaults.value.hiddenAttr,
        revealedAttr: revealedAttr ?? motionDefaults.value.revealedAttr,
        bypassReducedMotion: bypassReducedMotion ?? motionDefaults.value.bypassObserver
    }))

    const supportsObserver = computed(() => isClient && typeof window.IntersectionObserver !== 'undefined')

    const resolveElement = target => {
        if (!target) return null
        return Object.prototype.hasOwnProperty.call(target, 'value') ? target.value : target
    }

    const isElement = value => !!value && value.nodeType === 1

    const normalizeTargets = target => {
        const value = resolveElement(target)
        if (!value) return []

        if (isElement(value)) {
            return [value]
        }

        if (Array.isArray(value)) {
            return value.filter(isElement)
        }

        if (typeof NodeList !== 'undefined' && value instanceof NodeList) {
            return Array.from(value).filter(isElement)
        }

        if (typeof HTMLCollection !== 'undefined' && value instanceof HTMLCollection) {
            return Array.from(value).filter(isElement)
        }

        return []
    }

    const getRevealItems = target => {
        if (!itemSelector) {
            return [target]
        }

        const items = Array.from(target.querySelectorAll(itemSelector)).filter(isElement)
        return items.length ? items : [target]
    }

    const setHiddenState = target => {
        const {
            initialClass: initial,
            revealedClass: revealed,
            hiddenAttr: hidden,
            revealedAttr: shown
        } = config.value

        getRevealItems(target).forEach(item => {
            const alreadyRevealed = config.value.once && item.getAttribute(shown) === 'true'
            if (alreadyRevealed) {
                return
            }

            item.classList.add(initial)
            item.classList.remove(revealed)
            item.setAttribute(hidden, 'true')
            item.setAttribute(shown, 'false')
            item.removeAttribute(STAGGER_INDEX_ATTR)
            item.style.removeProperty(STAGGER_DELAY_VAR)
        })
    }

    const setRevealedState = target => {
        const {
            initialClass: initial,
            revealedClass: revealed,
            hiddenAttr: hidden,
            revealedAttr: shown,
            stagger: step
        } = config.value

        const canStagger = !prefersReducedMotion.value && step > 0

        getRevealItems(target).forEach((item, index) => {
            item.classList.remove(initial)
            item.classList.add(revealed)
            item.setAttribute(hidden, 'false')
            item.setAttribute(shown, 'true')

            if (canStagger) {
                item.setAttribute(STAGGER_INDEX_ATTR, String(index))
                item.style.setProperty(STAGGER_DELAY_VAR, `${Math.round(index * step)}ms`)
                return
            }

            item.removeAttribute(STAGGER_INDEX_ATTR)
            item.style.removeProperty(STAGGER_DELAY_VAR)
        })
    }

    const disconnect = () => {
        observer?.disconnect()
        observer = null
        observedTargets.clear()
    }

    const unobserve = target => {
        const targets = normalizeTargets(target)
        targets.forEach(element => {
            observer?.unobserve(element)
            observedTargets.delete(element)
        })
    }

    const reveal = target => {
        normalizeTargets(target).forEach(setRevealedState)
    }

    const shouldBypassObserver = () => prefersReducedMotion.value && config.value.bypassReducedMotion

    const handleEntries = entries => {
        entries.forEach(entry => {
            const element = entry.target

            if (entry.isIntersecting) {
                setRevealedState(element)

                if (config.value.once) {
                    unobserve(element)
                }
                return
            }

            if (!config.value.once) {
                setHiddenState(element)
            }
        })
    }

    const ensureObserver = () => {
        if (!supportsObserver.value) return null
        if (observer) return observer

        observer = new window.IntersectionObserver(handleEntries, {
            root: resolveElement(root),
            rootMargin: config.value.rootMargin,
            threshold: config.value.threshold
        })

        return observer
    }

    const observe = target => {
        const targets = normalizeTargets(target)
        if (!targets.length) return []

        targets.forEach(setHiddenState)

        if (shouldBypassObserver() || !supportsObserver.value) {
            targets.forEach(setRevealedState)
            return targets
        }

        const activeObserver = ensureObserver()
        if (!activeObserver) {
            targets.forEach(setRevealedState)
            return targets
        }

        targets.forEach(element => {
            observedTargets.add(element)
            activeObserver.observe(element)
        })

        return targets
    }

    watch(prefersReducedMotion, reduce => {
        if (!reduce || !config.value.bypassReducedMotion) return

        observedTargets.forEach(setRevealedState)
        disconnect()
    })

    onUnmounted(disconnect)

    return {
        prefersReducedMotion,
        supportsObserver,
        observe,
        unobserve,
        reveal,
        disconnect
    }
}
