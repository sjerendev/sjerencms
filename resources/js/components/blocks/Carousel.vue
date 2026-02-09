<template>
	<section ref="sectionRef" :class="block.section_class">
    <div class="container py-12 mx-auto">
        <div data-reveal-item>
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
    </div>
	</section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import { useInViewReveal } from '@/js/composables/useInViewReveal.js'
import 'vue3-carousel/dist/carousel.css'

defineProps({
    block: {
        type: Object,
        required: true
    }
})

const sectionRef = ref(null)
const { observe } = useInViewReveal({
    itemSelector: '[data-reveal-item]',
    once: true,
    stagger: 0
})

onMounted(() => {
    observe(sectionRef)
})
</script>
