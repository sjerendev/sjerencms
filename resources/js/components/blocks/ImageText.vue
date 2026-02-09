<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container px-6 py-12 mx-auto md:px-8 lg:px-12">
            <div class="flex flex-col items-center md:flex-row"
                :class="{ 'md:flex-row-reverse': block.reverse_layout }">
                <!-- Text column - takes full width if no image -->
                <div data-reveal-item class="flex order-1 items-center px-2 w-full" 
                     :class="{
                        'md:w-1/2': hasImage,
                        'md:w-full': !hasImage,
                        'lg:pl-6': !block.reverse_layout && hasImage,
                        'lg:pr-6': block.reverse_layout && hasImage,
                        'md:order-1': !block.reverse_layout || !hasImage,
                        'md:order-2': block.reverse_layout && hasImage
                     }">
                    <div ref="contentRef" class="py-4 w-full image-caption-sync-caption">
                        <div v-if="isFirstBlock">
                            <div v-html="parsedContent" class="mb-4"></div>
                        </div>
                        <div v-else>
                            <div v-html="parsedContent" class="mb-4 content-visibility-auto"></div>
                        </div>
                        <div v-if="block.button1_text || block.button2_text" class="block gap-4 mt-10 md:flex">
										<MagneticElement :strength="0.3" :scale="true" :rotate="true">
											<a v-if="block.button1_text" :href="block.button1_url"
                                class="block items-center px-4 py-2 mb-4 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-one md:mb-0">
                                {{ block.button1_text }}
                            </a>
										</MagneticElement>
										<MagneticElement :strength="0.3" :scale="true" :rotate="true">
                            <a v-if="block.button2_text" :href="block.button2_url"
                                class="block items-center px-4 py-2 font-semibold text-center text-white rounded-xl transition-colors md:inline-flex button-two">
                                {{ block.button2_text }}
                            </a>
											</MagneticElement>
                        </div>
                    </div>
                </div>
                <!-- Image column - only shown if image exists -->
                <div v-if="hasImage" data-reveal-item class="order-2 mb-8 w-full md:w-1/2 md:mb-0" :class="{
                    'lg:pr-6': !block.reverse_layout,
                    'lg:pl-6': block.reverse_layout,
                    'md:order-2': !block.reverse_layout,
                    'md:order-1': block.reverse_layout
                }">
                    <div class="relative z-0 aspect-ratio-box" :style="{ paddingBottom: aspectRatio + '%' }">
                        <img :src="`/storage/${block.image}`"
                             ref="imageRef"
                             :fetchpriority="isFirstBlock ? 'low' : 'auto'"
                             :loading="isFirstBlock ? 'lazy' : 'lazy'"
                             class="object-cover absolute inset-0 w-full h-full image-caption-sync-image"
                             :alt="block.image_alt || ''"
                             @load="onImageLoad" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { marked } from "marked";
import { computed, onMounted, ref } from "vue";
import DOMPurify from "dompurify";
import MagneticElement from "../common/MagneticElement.vue";
import { useInViewReveal } from "@/js/composables/useInViewReveal.js";
import { useImageCaptionSync } from "@/js/composables/useImageCaptionSync.js";

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
});

const aspectRatio = ref(56.25); // 16:9 default
const sectionRef = ref(null);
const contentRef = ref(null);
const imageRef = ref(null);
const { observe } = useInViewReveal({
    itemSelector: "[data-reveal-item]",
    once: true,
    stagger: 100,
});

const onImageLoad = (event) => {
    if (event.target) {
        const img = event.target;
        aspectRatio.value = (img.naturalHeight / img.naturalWidth) * 100;
    }
};

const hasImage = computed(() => {
    return props.block.image && props.block.image.length > 0;
});

const parsedContent = computed(() => {
    if (!props.block.text) return "";
    const htmlContent = marked.parse(props.block.text);
    return DOMPurify.sanitize(htmlContent);
});

const parseBoolean = (value, fallback = false) => {
    if (typeof value === "boolean") return value;
    if (typeof value === "number") return value === 1;
    if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off"].includes(normalized)) return false;
    }
    return fallback;
};

const parseUnitInterval = (value, fallback) => {
    const parsed = Number.parseFloat(value);
    if (!Number.isFinite(parsed)) return fallback;
    return Math.min(1, Math.max(0, parsed));
};

const imageCaptionSyncEnabled = computed(() => {
    const value =
        props.block.image_caption_sync ??
        props.block.enable_image_caption_sync ??
        props.block.scroll_image_sync ??
        true;

    return parseBoolean(value, true);
});

const imageCaptionSyncStart = computed(() =>
    parseUnitInterval(
        props.block.image_caption_sync_start ??
            props.block.image_sync_start,
        0.08
    )
);

const imageCaptionSyncEnd = computed(() =>
    parseUnitInterval(
        props.block.image_caption_sync_end ??
            props.block.image_sync_end,
        0.72
    )
);

useImageCaptionSync({
    sectionRef,
    imageRefs: [imageRef],
    captionRefs: [contentRef],
    enabled: imageCaptionSyncEnabled,
    start: imageCaptionSyncStart,
    end: imageCaptionSyncEnd,
    imageTranslateY: 26,
    imageScaleFrom: 1.06,
    imageScaleTo: 1,
    captionTranslateY: 16,
    captionBlurFrom: 3.5,
});

onMounted(() => {
    observe(sectionRef);
});
</script>

<style scoped>
.aspect-ratio-box {
    position: relative;
    width: 100%;
}

.aspect-ratio-box img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.button-one {
    background-color: #33FFD3;
    border: 1px solid #33FFD3;
    color: #111820;
    transition: all 0.3s ease-in-out;

    &:hover {
        background-color: #fff;
        border: 1px solid #fff;
        color: #111820;
        transition: all 0.3s ease-in-out;
    }
}

.button-two {
    background-color: transparent;
    color: #33FFD3;
    border: 1px solid #33FFD3;
    transition: all 0.3s ease-in-out;

    &:hover {
        background-color: #fff;
        border: 1px solid #fff;
        color: #111820;
        transition: all 0.3s ease-in-out;
    }
}

.content-visibility-auto {
    content-visibility: auto;
    contain-intrinsic-size: 0 500px;
}

/* Fix mobile width issues */
@media (max-width: 768px) {
    .container {
        width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
