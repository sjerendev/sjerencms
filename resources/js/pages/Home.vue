<template>
	<div>
		<div v-if="page?.hero_background_type" class="relative flex items-center justify-center w-full hero-section"
			:style="{
				height: page.hero_height || '50svh',
				backgroundColor: page.hero_background_color,
				color: page.hero_text_color
			}">
			<img v-if="page?.hero_image" :src="`/storage/${page.hero_image}`"
				class="absolute inset-0 object-cover w-full h-full"
				:style="{ opacity: page.hero_background_type === 'both' ? 0.5 : 1 }">
			<div class="relative z-10 px-12 py-12 mx-auto container-fluid">
				<h1 class="text-4xl font-bold">{{ page.hero_headline || page.title }}</h1>
				<p v-if="page.hero_subheadline" class="mt-4 text-xl">{{ page.hero_subheadline }}</p>
				<p v-if="page.hero_paragraph" class="mt-4">{{ page.hero_paragraph }}</p>
				<a v-if="page.hero_cta_text && page.hero_cta_url" :href="page.hero_cta_url"
					class="inline-block px-6 py-3 mt-6 text-white rounded-lg bg-primary-500">
					{{ page.hero_cta_text }}
				</a>
			</div>
		</div>


		<!-- Content Blocks -->
		<div v-if="page?.content">
			<BlockRenderer v-for="(block, index) in page.content" :key="index" :block="block" />
		</div>
	</div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import BlockRenderer from '../components/BlockRenderer.vue'
import { useSeo } from '../composables/useSeo'

const page = ref(null)

useSeo(page, {
    title: 'Kalibr Kreativ Studio',
    description: 'Kalibr. är en Webb, Design & AI-byrå med bas i Malmö men har kunder i hela Sverige.',
    type: 'website'
})

onMounted(async () => {
    const response = await fetch('/api/pages/home')
    page.value = await response.json()
    console.log('Page data:', page.value)
})
</script>
