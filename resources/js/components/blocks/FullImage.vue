<template>
	<section ref="sectionRef" :class="block.section_class">
    <div class="container py-12 mx-auto">
        <img
            ref="imageRef"
            :src="`/storage/${block.image}`"
            :fetchpriority="isFirstBlock ? 'high' : 'auto'"
            :loading="isFirstBlock ? 'eager' : 'lazy'"
            class="w-full h-auto image-caption-sync-image"
            :alt="block.image_alt || ''"
        >
    </div>
	</section>
</template>

<script setup>
import { computed, ref } from "vue";
import { useImageCaptionSync } from "@/js/composables/useImageCaptionSync.js";

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

const props = defineProps({
    block: {
        type: Object,
        required: true
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
});

const sectionRef = ref(null);
const imageRef = ref(null);

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
    enabled: imageCaptionSyncEnabled,
    start: imageCaptionSyncStart,
    end: imageCaptionSyncEnd,
    imageTranslateY: 24,
    imageScaleFrom: 1.04,
    imageScaleTo: 1,
});
</script>
