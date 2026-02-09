<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-12 mx-auto">
            <div
                v-if="block.section_title || block.section_description"
                data-pricing-reveal
                class="text-center mb-8"
            >
                <h2 class="text-3xl font-bold mb-4" v-if="block.section_title">{{ block.section_title }}</h2>
                <div v-if="block.section_description" v-html="getMarkdownContent(block.section_description)" class="max-w-3xl mx-auto mb-12 text-center text-gray-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pricing-grid">
                <div
                    v-for="(plan, index) in plans"
                    :key="index"
                    data-pricing-reveal
                    class="bg-white rounded-lg shadow-lg p-6 transition-transform pricing-plan"
                    :class="{
                        'pricing-plan-featured': plan.is_featured,
                        'is-pricing-entered': hasPricingEntered
                    }"
                >
                    <div class="text-center">
                        <h3 class="text-xl font-bold mb-2">{{ plan.name }}</h3>
                        <div class="text-3xl font-bold mb-4 pricing-price">
                            {{ displayedPriceFor(plan, index) }}
                            <span class="text-sm text-gray-500">{{ plan.billing_period }}</span>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-6">
                        <li
                            v-for="(feature, featureIndex) in plan.features"
                            :key="featureIndex"
                            class="flex items-center pricing-feature"
                            :class="{
                                'text-gray-400': !feature.is_included,
                                'is-pricing-entered': hasPricingEntered
                            }"
                            :style="getFeatureDelayStyle(index, featureIndex)"
                        >
                            <svg v-if="feature.is_included" class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg v-else class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ feature.feature }}
                        </li>
                    </ul>
                    <div class="text-center">
                        <a
                            :href="plan.button_url"
                            :style="getButtonStyle(plan)"
                            class="inline-block w-full px-6 py-3 text-center text-white bg-blue-600 rounded-lg hover:opacity-90 transition-colors"
                        >
                            {{ plan.button_text }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { useInViewReveal } from '@/js/composables/useInViewReveal.js';
import { useMotionPreferences } from '@/js/composables/useMotionPreferences.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});
const sectionRef = ref(null);
const hasPricingEntered = ref(false);
const displayedPrices = ref({});
const rafByIndex = new Map();
let revealObserver = null;

const { prefersReducedMotion } = useMotionPreferences();

const plans = computed(() =>
    Array.isArray(props.block.pricing_tables) ? props.block.pricing_tables : []
);

const { observe } = useInViewReveal({
    itemSelector: '[data-pricing-reveal]',
    once: true,
    stagger: 70
});

const clamp = (value, min = 0, max = 1) => {
    if (Number.isNaN(value)) return min;
    return Math.min(max, Math.max(min, value));
};

const easeOutCubic = value => {
    const t = clamp(value);
    return 1 - Math.pow(1 - t, 3);
};

const parsePrice = rawPrice => {
    const raw = `${rawPrice ?? ''}`;
    const tokenMatch = raw.match(/-?\d{1,3}(?:[ .]\d{3})*(?:[.,]\d+)?|-?\d+(?:[.,]\d+)?/);
    if (!tokenMatch) return null;

    const token = tokenMatch[0];
    const tokenStart = tokenMatch.index ?? 0;
    const tokenEnd = tokenStart + token.length;
    const compactToken = token.replace(/\s+/g, '');

    let decimalSeparator = '.';
    if (compactToken.includes(',') && compactToken.includes('.')) {
        decimalSeparator =
            compactToken.lastIndexOf(',') > compactToken.lastIndexOf('.') ? ',' : '.';
    } else if (compactToken.includes(',')) {
        decimalSeparator = ',';
    }

    const thousandsSeparator = decimalSeparator === ',' ? '.' : ',';
    const normalizedNumber = compactToken
        .replace(new RegExp(`\\${thousandsSeparator}`, 'g'), '')
        .replace(decimalSeparator, '.');

    const numericValue = Number.parseFloat(normalizedNumber);
    if (!Number.isFinite(numericValue)) return null;

    const decimals = compactToken.includes(decimalSeparator)
        ? compactToken.split(decimalSeparator)[1].length
        : 0;

    return {
        raw,
        prefix: raw.slice(0, tokenStart),
        suffix: raw.slice(tokenEnd),
        value: numericValue,
        decimals,
        decimalSeparator
    };
};

const formatPrice = (parsedPrice, value) => {
    const rounded =
        parsedPrice.decimals > 0
            ? value.toFixed(parsedPrice.decimals)
            : `${Math.round(value)}`;
    const localized =
        parsedPrice.decimals > 0 && parsedPrice.decimalSeparator === ','
            ? rounded.replace('.', ',')
            : rounded;
    return `${parsedPrice.prefix}${localized}${parsedPrice.suffix}`;
};

const setFinalPrices = () => {
    const nextMap = {};
    plans.value.forEach((plan, index) => {
        nextMap[index] = `${plan.price ?? ''}`;
    });
    displayedPrices.value = nextMap;
};

const cancelCountUp = () => {
    rafByIndex.forEach(frameId => {
        cancelAnimationFrame(frameId);
    });
    rafByIndex.clear();
};

const animatePrices = () => {
    cancelCountUp();

    plans.value.forEach((plan, index) => {
        const parsed = parsePrice(plan.price);
        if (!parsed || prefersReducedMotion.value) {
            displayedPrices.value = {
                ...displayedPrices.value,
                [index]: `${plan.price ?? ''}`
            };
            return;
        }

        const duration = 900 + index * 120;
        const delay = index * 60;
        const startTime = performance.now() + delay;

        const step = now => {
            if (now < startTime) {
                rafByIndex.set(index, requestAnimationFrame(step));
                return;
            }

            const progress = clamp((now - startTime) / duration);
            const eased = easeOutCubic(progress);
            const currentValue = parsed.value * eased;

            displayedPrices.value = {
                ...displayedPrices.value,
                [index]: formatPrice(parsed, currentValue)
            };

            if (progress < 1) {
                rafByIndex.set(index, requestAnimationFrame(step));
                return;
            }

            displayedPrices.value = {
                ...displayedPrices.value,
                [index]: formatPrice(parsed, parsed.value)
            };
            rafByIndex.delete(index);
        };

        rafByIndex.set(index, requestAnimationFrame(step));
    });
};

const displayedPriceFor = (plan, index) => {
    const value = displayedPrices.value[index];
    return value ?? `${plan.price ?? ''}`;
};

const getFeatureDelayStyle = (planIndex, featureIndex) => {
    const baseDelay = planIndex * 80 + featureIndex * 45;
    return {
        '--pricing-feature-delay': `${baseDelay}ms`
    };
};

const getButtonStyle = plan => {
    if (!plan.accent_color) return {};
    return {
        backgroundColor: plan.accent_color
    };
};

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const startRevealObserver = () => {
    if (typeof window === 'undefined' || !sectionRef.value) return;

    revealObserver = new IntersectionObserver(
        entries => {
            const visible = entries.some(entry => entry.isIntersecting);
            if (!visible) return;

            hasPricingEntered.value = true;
            animatePrices();
            revealObserver?.disconnect();
            revealObserver = null;
        },
        {
            threshold: 0.25,
            rootMargin: '0px 0px -10% 0px'
        }
    );

    revealObserver.observe(sectionRef.value);
};

watch(
    () => plans.value,
    async () => {
        await nextTick();
        if (hasPricingEntered.value) {
            animatePrices();
            return;
        }
        setFinalPrices();
    },
    { deep: true }
);

watch(prefersReducedMotion, reduce => {
    if (!reduce) return;
    cancelCountUp();
    setFinalPrices();
});

onMounted(async () => {
    observe(sectionRef);
    setFinalPrices();
    await nextTick();

    if (prefersReducedMotion.value) {
        hasPricingEntered.value = true;
        return;
    }

    startRevealObserver();
});

onUnmounted(() => {
    cancelCountUp();
    revealObserver?.disconnect();
    revealObserver = null;
});
</script>

<style scoped>
.pricing-plan {
    transition: transform 300ms cubic-bezier(0.22, 1, 0.36, 1),
        box-shadow 300ms ease, border-color 300ms ease;
}

.pricing-plan:hover {
    transform: translate3d(0, -8px, 0);
}

.pricing-plan-featured {
    border: 1px solid rgb(51 255 211 / 0.6);
    box-shadow: 0 10px 28px rgb(17 24 32 / 0.14);
}

.pricing-plan-featured.is-pricing-entered {
    animation: pricingFeaturedPulse 2.8s ease-in-out infinite;
}

.pricing-price {
    font-variant-numeric: tabular-nums;
}

.pricing-feature {
    opacity: 0;
    transform: translate3d(0, 10px, 0);
    filter: blur(4px);
}

.pricing-feature.is-pricing-entered {
    opacity: 1;
    transform: translate3d(0, 0, 0);
    filter: blur(0);
    transition: opacity 360ms ease, transform 360ms cubic-bezier(0.22, 1, 0.36, 1),
        filter 360ms ease;
    transition-delay: var(--pricing-feature-delay, 0ms);
}

@keyframes pricingFeaturedPulse {
    0%,
    100% {
        box-shadow: 0 10px 28px rgb(17 24 32 / 0.16),
            0 0 0 0 rgb(51 255 211 / 0.2);
    }
    50% {
        box-shadow: 0 18px 40px rgb(17 24 32 / 0.22),
            0 0 0 12px rgb(51 255 211 / 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .pricing-plan,
    .pricing-plan:hover,
    .pricing-feature,
    .pricing-feature.is-pricing-entered {
        transition: none;
        transform: none;
        filter: none;
        opacity: 1;
    }

    .pricing-plan-featured.is-pricing-entered {
        animation: none;
    }
}
</style>
