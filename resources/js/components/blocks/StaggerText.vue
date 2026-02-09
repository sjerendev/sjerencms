<template>
    <section
        ref="sectionRef"
        class="flex"
        :class="[block.section_class, alignmentClass]"
        :style="{
            backgroundColor: block.background_color || '#F3F4F6',
            minHeight: block.section_height || 'auto'
        }"
    >
        <div
            class="container mx-auto"
            :style="{
                paddingTop: block.padding_y || '6rem',
                paddingBottom: block.padding_y || '6rem',
                paddingLeft: block.padding_x || '1.5rem',
                paddingRight: block.padding_x || '1.5rem'
            }"
        >
            <div class="stagger-text-wrapper">
                <h2
                    v-if="block.text_row_1"
                    ref="row1Ref"
                    :class="block.text_class"
                    :style="{
                        color: block.color_row_1 || '#9CA3AF',
                        fontSize: block.font_size || 'clamp(2.5rem, 5vw, 4.5rem)'
                    }"
                >
                    <span
                        v-for="(item, index) in row1Chars"
                        :key="'row1-' + index"
                        class="stagger-char inline-block"
                        :class="[{ 'whitespace': item.char === ' ' }, item.bold ? (block.bold_class || 'font-bold') : '']"
                    >{{ item.char === ' ' ? '\u00A0' : item.char }}</span>
                </h2>
                <h2
                    v-if="block.text_row_2"
                    ref="row2Ref"
                    :class="block.text_class"
                    :style="{
                        color: block.color_row_2 || '#111827',
                        fontSize: block.font_size || 'clamp(2.5rem, 5vw, 4.5rem)'
                    }"
                >
                    <span
                        v-for="(item, index) in row2Chars"
                        :key="'row2-' + index"
                        class="stagger-char inline-block"
                        :class="[{ 'whitespace': item.char === ' ' }, item.bold ? (block.bold_class || 'font-bold') : '']"
                    >{{ item.char === ' ' ? '\u00A0' : item.char }}</span>
                </h2>
                <h2
                    v-if="block.text_row_3"
                    ref="row3Ref"
                    :class="block.text_class"
                    :style="{
                        color: block.color_row_3 || '#111827',
                        fontSize: block.font_size || 'clamp(2.5rem, 5vw, 4.5rem)'
                    }"
                >
                    <span
                        v-for="(item, index) in row3Chars"
                        :key="'row3-' + index"
                        class="stagger-char inline-block"
                        :class="[{ 'whitespace': item.char === ' ' }, item.bold ? (block.bold_class || 'font-bold') : '']"
                    >{{ item.char === ' ' ? '\u00A0' : item.char }}</span>
                </h2>
                <h2
                    v-if="block.text_row_4"
                    ref="row4Ref"
                    :class="block.text_class"
                    :style="{
                        color: block.color_row_4 || '#111827',
                        fontSize: block.font_size || 'clamp(2.5rem, 5vw, 4.5rem)'
                    }"
                >
                    <span
                        v-for="(item, index) in row4Chars"
                        :key="'row4-' + index"
                        class="stagger-char inline-block"
                        :class="[{ 'whitespace': item.char === ' ' }, item.bold ? (block.bold_class || 'font-bold') : '']"
                    >{{ item.char === ' ' ? '\u00A0' : item.char }}</span>
                </h2>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
    block: {
        type: Object,
        required: true
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
})

const sectionRef = ref(null)
const row1Ref = ref(null)
const row2Ref = ref(null)
const row3Ref = ref(null)
const row4Ref = ref(null)

// Parse text with markdown bold markers into an array of { char, bold }.
// Supports both **bold** and __bold__, including escaped markers (\*\*text\*\*).
const parseTextWithBold = text => {
    if (!text) return []

    const input = String(text)
        .replace(/\\\*/g, '*')
        .replace(/\\_/g, '_')

    const result = []
    let cursor = 0
    let isBold = false

    const pushChars = segment => {
        for (const char of segment) {
            result.push({ char, bold: isBold })
        }
    }

    const hasClosingMarker = (source, marker, fromIndex) =>
        source.indexOf(marker, fromIndex) !== -1

    while (cursor < input.length) {
        const twoChars = input.slice(cursor, cursor + 2)
        const isBoldMarker = twoChars === '**' || twoChars === '__'

        if (!isBoldMarker) {
            pushChars(input[cursor])
            cursor += 1
            continue
        }

        const marker = twoChars
        const nextIndex = cursor + 2

        // Preserve unmatched markers as literal text.
        if (!isBold && !hasClosingMarker(input, marker, nextIndex)) {
            pushChars(marker)
            cursor += 2
            continue
        }

        isBold = !isBold
        cursor += 2
    }

    return result
}

const row1Chars = computed(() => parseTextWithBold(props.block.text_row_1))
const row2Chars = computed(() => parseTextWithBold(props.block.text_row_2))
const row3Chars = computed(() => parseTextWithBold(props.block.text_row_3))
const row4Chars = computed(() => parseTextWithBold(props.block.text_row_4))

const alignmentClass = computed(() => {
    const align = props.block.vertical_align || 'start'
    return {
        'start': 'items-start',
        'center': 'items-center',
        'end': 'items-end'
    }[align] || 'items-start'
})

const staggerSpeed = computed(() => parseFloat(props.block.stagger_speed) || 0.03)
const animationType = computed(() => props.block.animation_type || 'scroll_into_view')

let ctx = null

onMounted(() => {
    if (typeof window === 'undefined') return

    ctx = gsap.context(() => {
        const row1Elements = row1Ref.value?.querySelectorAll('.stagger-char') || []
        const row2Elements = row2Ref.value?.querySelectorAll('.stagger-char') || []
        const row3Elements = row3Ref.value?.querySelectorAll('.stagger-char') || []
        const row4Elements = row4Ref.value?.querySelectorAll('.stagger-char') || []

        const allChars = [...row1Elements, ...row2Elements, ...row3Elements, ...row4Elements]

        if (!allChars.length) return

        // Set initial state
        gsap.set(allChars, {
            opacity: 0,
            y: 20
        })

        if (animationType.value === 'scroll_scrub') {
            // Scroll scrub mode - animation follows scroll position
            gsap.to(allChars, {
                opacity: 1,
                y: 0,
                stagger: staggerSpeed.value,
                ease: 'none',
                scrollTrigger: {
                    trigger: sectionRef.value,
                    start: 'top 80%',
                    end: 'center center',
                    scrub: 1,
                }
            })
        } else {
            // Scroll into view mode - triggers once
            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: sectionRef.value,
                    start: 'top 80%',
                    once: true
                }
            })

            const rows = [row1Elements, row2Elements, row3Elements, row4Elements]

            rows.forEach((rowElements, index) => {
                if (rowElements.length) {
                    tl.to(rowElements, {
                        opacity: 1,
                        y: 0,
                        duration: 0.4,
                        stagger: staggerSpeed.value,
                        ease: 'power2.out'
                    }, index === 0 ? 0 : '-=0.3')
                }
            })
        }
    }, sectionRef.value)
})

onUnmounted(() => {
    ctx?.revert()
})
</script>

<style scoped>
.stagger-char {
    will-change: opacity, transform;
}

.whitespace {
    width: 0.3em;
}
</style>
