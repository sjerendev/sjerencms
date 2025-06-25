<template>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image gallery -->
            <div class="flex flex-col">
                <!-- Main image -->
                <div class="aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg overflow-hidden">
                    <img v-if="currentImage" :src="currentImage" :alt="product?.name"
                        class="w-full h-full object-center object-cover">
                    <div v-else class="flex items-center justify-center h-full">
                        <svg class="h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                </div>

                <!-- Image selector -->
                <div v-if="hasMultipleImages" class="mt-4 grid grid-cols-4 gap-4">
                    <button v-for="(image, index) in allImages" :key="index" @click="setCurrentImage(index)"
                        class="relative aspect-w-1 aspect-h-1 rounded-md overflow-hidden"
                        :class="{ 'ring-2 ring-primary-500': currentImageIndex === index }">
                        <img :src="image" :alt="`${product?.name} - Image ${index + 1}`"
                            class="w-full h-full object-center object-cover">
                        <div class="absolute inset-0 ring-1 ring-inset ring-black ring-opacity-10" aria-hidden="true" />
                    </button>
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ product?.name }}</h1>

                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <p class="text-3xl text-gray-900">{{ formatPrice(product?.price) }}</p>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-700" v-html="product?.description"></div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="ml-2 text-sm text-gray-500">Instant digital delivery</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="mt-10 flex">
                        <button type="button" @click="addToCart"
                            class="w-full bg-primary-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            :disabled="isInCart">
                            {{ isInCart ? 'Added to cart' : 'Add to cart' }}
                        </button>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="text-sm text-gray-500">
                        <h4 class="font-medium text-gray-900">Digital Product Details:</h4>
                        <ul class="list-disc pl-5 mt-2">
                            <li>Instant download after purchase</li>
                            <li>Download link valid for 30 days</li>
                            <li>Maximum 3 downloads per purchase</li>
                            <li>Secure delivery via encrypted link</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../../stores/cartStore'
import axios from 'axios'

const props = defineProps({
    slug: {
        type: String,
        required: true
    }
})

const cart = useCartStore()
const product = ref(null)
const currentImageIndex = ref(0)

// Fetch product data when component mounts
const fetchProduct = async () => {
    try {
        const response = await axios.get(route('api.products.show', props.slug))
        product.value = response.data
        // Reset current image index when product changes
        currentImageIndex.value = 0
    } catch (error) {
        console.error('Error fetching product:', error)
    }
}

fetchProduct()

const allImages = computed(() => {
    if (!product.value) return []
    const images = []
    if (product.value.featured_image_url) {
        images.push(product.value.featured_image_url)
    }
    if (product.value.product_images_urls) {
        images.push(...product.value.product_images_urls)
    }
    return images
})

const hasMultipleImages = computed(() => allImages.value.length > 1)

const currentImage = computed(() => allImages.value[currentImageIndex.value] || null)

const setCurrentImage = (index) => {
    currentImageIndex.value = index
}

const isInCart = computed(() => {
    return cart.items.some(item => item.id === product.value?.id)
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('sv-SE', {
        style: 'currency',
        currency: 'SEK'
    }).format(price)
}

const addToCart = () => {
    if (product.value && !isInCart.value) {
        cart.addItem(product.value)
    }
}
</script>