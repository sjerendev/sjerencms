<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container px-6 py-12 mx-auto 2xl:px-0">
            <div
                class="mb-8 text-center"
                v-if="block.heading || block.subheading"
            >
                <h2 class="mb-4 text-3xl font-bold image-caption-sync-caption" v-if="block.heading">
                    {{ block.heading }}
                </h2>
                <p class="text-gray-600 image-caption-sync-caption" v-if="block.subheading">
                    {{ block.subheading }}
                </p>
            </div>
            <div
                v-if="block.section_description"
                v-html="getMarkdownContent(block.section_description)"
                class="mx-auto mb-12 max-w-3xl text-center text-gray-500 image-caption-sync-caption"
            ></div>

            <!-- Portfolio Grid -->
            <div class="portfolio-grid">
                <div
                    v-for="(item, index) in block.portfolio_items"
                    :key="index"
                    class="portfolio-item"
                    @mouseenter="handleMouseEnter($event, item)"
                    @mouseleave="handleMouseLeave"
                    @mousemove="handleMouseMove"
                >
                    <a
                        :href="item.website_url"
                        :target="item.website_url !== '#' ? '_blank' : '_self'"
                        class="flex flex-row justify-between items-center portfolio-link"
                    >
                        <h3 class="portfolio-title image-caption-sync-caption">{{ item.name }}</h3>
                        <div
                            v-html="getMarkdownContent(item.description)"
                            class="portfolio-description image-caption-sync-caption"
                        ></div>
                    </a>
                </div>
            </div>

            <!-- Floating Preview Image -->
            <div
                ref="previewImage"
                class="portfolio-preview"
                v-show="hoveredItem"
            >
                <img
                    v-if="hoveredItem"
                    :src="`/storage/${hoveredItem.preview_image}`"
                    :alt="hoveredItem.name"
                    class="object-cover object-top preview-img image-caption-sync-image"
                />
            </div>
        </div>
        <div class="container mx-auto">
            <div class="portfolio-after">
                <p class="image-caption-sync-caption">
                    Detta är bara en bråkdel av vad jag gjort de senaste 15+
                    åren på olika byråer...
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
import { marked } from "marked";
import DOMPurify from "dompurify";
import { gsap } from "gsap";
import { useImageCaptionSync } from "@/js/composables/useImageCaptionSync.js";
import { useMotionPreferences } from "@/js/composables/useMotionPreferences.js";

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
});

const sectionRef = ref(null);
const previewImage = ref(null);
const hoveredItem = ref(null);
let hideTimeout = null;
let lastMoveTime = 0;
let lastMoveX = 0;
let lastMoveY = 0;
let moveXTo = null;
let moveYTo = null;
let rotateXTo = null;
let rotateYTo = null;
let scaleTo = null;
let opacityTo = null;

const { prefersReducedMotion } = useMotionPreferences();

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const getMarkdownContent = (text) => {
    if (!text) return "";
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

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
        0.12
    )
);

const imageCaptionSyncEnd = computed(() =>
    parseUnitInterval(
        props.block.image_caption_sync_end ??
            props.block.image_sync_end,
        0.78
    )
);

const { updateProgress: updateImageCaptionSync } = useImageCaptionSync({
    sectionRef,
    imageSelector: ".portfolio-preview .image-caption-sync-image",
    captionSelector: ".image-caption-sync-caption",
    enabled: imageCaptionSyncEnabled,
    start: imageCaptionSyncStart,
    end: imageCaptionSyncEnd,
    imageTranslateY: 34,
    imageScaleFrom: 1.08,
    imageScaleTo: 1,
    captionTranslateY: 18,
    captionBlurFrom: 4,
});

const updatePreviewFollow = (event) => {
    if (!previewImage.value || !moveXTo || !moveYTo) return;

    const now = performance.now();
    const dt = Math.max(16, now - lastMoveTime);
    const velocityX = (event.clientX - lastMoveX) / dt;
    const velocityY = (event.clientY - lastMoveY) / dt;
    const tiltY = clamp(velocityX * 70, -10, 10);
    const tiltX = clamp(velocityY * -70, -10, 10);

    moveXTo(event.clientX + 20);
    moveYTo(event.clientY - 100);

    if (prefersReducedMotion.value) {
        rotateXTo?.(0);
        rotateYTo?.(0);
    } else {
        rotateXTo?.(tiltX);
        rotateYTo?.(tiltY);
    }

    lastMoveTime = now;
    lastMoveX = event.clientX;
    lastMoveY = event.clientY;
};

const handleMouseEnter = (event, item) => {
    // Clear any pending hide timeout
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }

    const wasVisible = hoveredItem.value !== null;
    hoveredItem.value = item;
    lastMoveTime = performance.now();
    lastMoveX = event.clientX;
    lastMoveY = event.clientY;

    if (!wasVisible) {
        // Set initial position
        gsap.set(previewImage.value, {
            x: event.clientX + 20,
            y: event.clientY - 100,
            scale: 0,
            opacity: 0,
            rotateX: 0,
            rotateY: 0,
        });

        // Animate in
        scaleTo?.(1);
        opacityTo?.(1);
    } else {
        updatePreviewFollow(event);
    }

    nextTick(() => {
        updateImageCaptionSync();
    });
};

const handleMouseLeave = () => {
    // Add a small delay before hiding to allow for quick transitions between items
    hideTimeout = setTimeout(() => {
        rotateXTo?.(0);
        rotateYTo?.(0);
        scaleTo?.(0);
        opacityTo?.(0);
        gsap.delayedCall(prefersReducedMotion.value ? 0 : 0.18, () => {
            hoveredItem.value = null;
            updateImageCaptionSync();
        });
        hideTimeout = null;
    }, 50); // 50ms delay
};

const handleMouseMove = (event) => {
    if (!hoveredItem.value) return;
    updatePreviewFollow(event);
};

onMounted(() => {
    // Initialize GSAP
    gsap.set(previewImage.value, {
        scale: 0,
        opacity: 0,
        rotateX: 0,
        rotateY: 0,
        transformPerspective: 1200,
        transformOrigin: "center center",
    });
    const movementDuration = prefersReducedMotion.value ? 0 : 0.36;
    const rotationDuration = prefersReducedMotion.value ? 0 : 0.42;
    const revealDuration = prefersReducedMotion.value ? 0 : 0.28;
    moveXTo = gsap.quickTo(previewImage.value, "x", {
        duration: movementDuration,
        ease: "power3.out",
    });
    moveYTo = gsap.quickTo(previewImage.value, "y", {
        duration: movementDuration,
        ease: "power3.out",
    });
    rotateXTo = gsap.quickTo(previewImage.value, "rotateX", {
        duration: rotationDuration,
        ease: "power3.out",
    });
    rotateYTo = gsap.quickTo(previewImage.value, "rotateY", {
        duration: rotationDuration,
        ease: "power3.out",
    });
    scaleTo = gsap.quickTo(previewImage.value, "scale", {
        duration: revealDuration,
        ease: "power2.out",
    });
    opacityTo = gsap.quickTo(previewImage.value, "opacity", {
        duration: revealDuration,
        ease: "power2.out",
    });

    updateImageCaptionSync();
});

onUnmounted(() => {
    // Clean up any running animations and timeouts
    gsap.killTweensOf(previewImage.value);
    if (hideTimeout) {
        clearTimeout(hideTimeout);
    }
});
</script>

<style scoped>
.container-fluid {
    width: 100%;
    max-width: none;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}

.portfolio-item {
    border-bottom: 1px solid var(--primary-color);
    transition: background-color 0.3s ease;
}

.portfolio-link {
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.portfolio-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #111827;
    transition: color 0.3s ease;
}

.portfolio-description {
    font-size: 1.125rem;
    color: #6b7280;
    max-width: 600px;
}

.portfolio-preview {
    position: fixed;
    top: 0;
    left: 0;
    pointer-events: none;
    z-index: 1000;
    width: 360px;
    height: 260px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1),
        0 10px 10px -5px rgb(0 0 0 / 0.04);
    transform-style: preserve-3d;
}

.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .portfolio-title {
        font-size: 1.875rem;
    }

    .portfolio-description {
        font-size: 1rem;
    }

    .portfolio-link {
        padding: 1.5rem;
    }

    .portfolio-preview {
        display: none; /* Hide preview on mobile */
    }
}

@media (max-width: 640px) {
    .portfolio-title {
        font-size: 1.5rem;
    }

    .portfolio-link {
        padding: 1rem;
    }
}
</style>
