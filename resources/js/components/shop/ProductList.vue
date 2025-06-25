<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Category Filter -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Digital Products</h2>
                <select v-model="selectedCategory" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
            <div v-for="product in filteredProducts" :key="product.id" class="group relative">
                <div class="aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg overflow-hidden">
                    <img 
                        v-if="product.featured_image_url"
                        :src="product.featured_image_url" 
                        :alt="product.name"
                        class="w-full h-full object-center object-cover group-hover:opacity-75 transition-opacity"
                    >
                    <div v-else class="flex items-center justify-center h-full">
                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm text-gray-700">
                            <router-link :to="{ name: 'shop.product', params: { slug: product.slug }}" class="hover:text-primary-600">
                                <span aria-hidden="true" class="absolute inset-0"></span>
                                {{ product.name }}
                            </router-link>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ product.category.name }}</p>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ formatPrice(product.price) }}</p>
                </div>
                <button 
                    @click="addToCart(product)"
                    class="mt-4 w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition-colors"
                >
                    Add to Cart
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredProducts.length === 0" class="text-center py-12">
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Try changing your filter or check back later for new products.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '../../stores/cartStore'
import axios from 'axios'

const cart = useCartStore()
const products = ref([])
const categories = ref([])
const selectedCategory = ref('')

async function fetchData() {
    try {
        const [productsResponse, categoriesResponse] = await Promise.all([
            axios.get('/api/products'),
            axios.get('/api/categories')
        ])
        products.value = productsResponse.data
        categories.value = categoriesResponse.data
    } catch (error) {
        console.error('Error fetching data:', error)
    }
}

onMounted(fetchData)

const filteredProducts = computed(() => {
    if (!selectedCategory.value) return products.value
    return products.value.filter(product => product.category_id === selectedCategory.value)
})

function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price)
}

function addToCart(product) {
    cart.addItem(product)
}
</script>
