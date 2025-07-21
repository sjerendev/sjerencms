<template>
    <section :class="block.section_class">
        <div :class="block.container_type + ' mx-auto'">
            <div
                :class="[
                    'flex py-12 items-start content-block headline-text',
                    block.container_type === 'container-fluid' ? 'px-12' : '',
                ]"
            >
                <div class="w-3/12">
                    <component
                        :is="headlineType"
                        class="text-4xl font-bold leading-[1]"
                        :class="{ 'high-priority': isFirstBlock }"
                    >
                        {{ headline }}
                    </component>
                </div>
                <div
                    class="w-9/12 prose max-w-none leading-[1] text-lg content-visibility-auto"
                    v-html="parsedContent"
                ></div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { defineProps } from "vue";
import { marked } from "marked";
import { computed } from "vue";
import DOMPurify from "dompurify";

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
    isFirstBlock: {
        type: Boolean,
        default: false,
    },
});

const headlineType = computed(() => props.block.headline_type || "h2");
const headline = computed(() => props.block.headline || "");

const parsedContent = computed(() => {
    if (!props.block.text) return "";
    const htmlContent = marked.parse(props.block.text);
    return DOMPurify.sanitize(htmlContent);
});
</script>

<style scoped>
.content-visibility-auto {
    content-visibility: auto;
    contain-intrinsic-size: 0 300px;
}

.high-priority {
    content-visibility: visible;
}
</style>
