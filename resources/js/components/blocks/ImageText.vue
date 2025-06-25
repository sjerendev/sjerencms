<template>
    <section :class="block.section_class">
        <div class="container py-12 px-6 md:px-8 lg:px-12 mx-auto">
            <div class="flex flex-col md:flex-row items-center"
                :class="{ 'md:flex-row-reverse': block.reverse_layout }">
                <div class="flex items-center w-full px-2 md:w-1/2 order-1" :class="{
                    'lg:pl-6': !block.reverse_layout,
                    'lg:pr-6': block.reverse_layout,
                    'md:order-1': !block.reverse_layout,
                    'md:order-2': block.reverse_layout
                }">
                    <div class="py-4 w-full">
                        <div v-if="isFirstBlock">
                            <div v-html="parsedContent" class="mb-4"></div>
                        </div>
                        <div v-else>
                            <div v-html="parsedContent" class="mb-4 content-visibility-auto"></div>
                        </div>
                        <div v-if="block.button1_text || block.button2_text" class="block md:flex gap-4 mt-10">
                            <a v-if="block.button1_text" :href="block.button1_url"
                                class="block md:inline-flex text-center items-center px-4 py-2 font-semibold text-white transition-colors rounded-xl button-one mb-4 md:mb-0">
                                {{ block.button1_text }}
                            </a>
                            <a v-if="block.button2_text" :href="block.button2_url"
                                class="block md:inline-flex text-center items-center px-4 py-2 font-semibold text-white transition-colors rounded-xl button-two">
                                {{ block.button2_text }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 mb-8 md:mb-0 order-2" :class="{
                    'lg:pr-6': !block.reverse_layout,
                    'lg:pl-6': block.reverse_layout,
                    'md:order-2': !block.reverse_layout,
                    'md:order-1': block.reverse_layout
                }">
                    <div class="aspect-ratio-box relative z-0" :style="{ paddingBottom: aspectRatio + '%' }">
                        <img :src="`/storage/${block.image}`" 
                             :fetchpriority="isFirstBlock ? 'low' : 'auto'"
                             :loading="isFirstBlock ? 'lazy' : 'lazy'"
                             class="absolute inset-0 w-full h-full object-cover" 
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
import { computed, ref } from "vue";
import DOMPurify from "dompurify";

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

const onImageLoad = (event) => {
    if (event.target) {
        const img = event.target;
        aspectRatio.value = (img.naturalHeight / img.naturalWidth) * 100;
    }
};

const parsedContent = computed(() => {
    if (!props.block.text) return "";
    const htmlContent = marked.parse(props.block.text);
    return DOMPurify.sanitize(htmlContent);
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
    background-color: #18f2b2;
    border: 1px solid #18f2b2;
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
    color: #18f2b2;
    border: 1px solid #18f2b2;
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
