<template>
	<section :class="block.section_class">
    <div class="container py-12 mx-auto">
        <Carousel :items-to-show="1" :wrap-around="true">
            <Slide v-for="(image, index) in block.carousel_images" :key="index">
                <img :src="`/storage/${image}`" class="w-full h-auto" alt="">
            </Slide>

            <template #addons>
                <Navigation />
                <Pagination />
            </template>
        </Carousel>
    </div>
	</section>
</template>

<script setup>
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import 'vue3-carousel/dist/carousel.css'

defineProps({
    block: {
        type: Object,
        required: true
    }
})
</script>
