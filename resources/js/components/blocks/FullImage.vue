<template>
	<section :class="block.section_class">
    <div class="container py-12 mx-auto">
        <img
            :src="`/storage/${block.image}`"
            :fetchpriority="isFirstBlock ? 'high' : 'auto'"
            :loading="isFirstBlock ? 'eager' : 'lazy'"
            class="w-full h-auto"
            :alt="block.image_alt || ''"
        >
    </div>
	</section>
</template>

<script setup>
defineProps({
    block: {
        type: Object,
        required: true
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
})
</script>
