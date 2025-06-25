<template>
    <div>
        <div class="container px-4 py-12 mx-auto">
            <h1 class="mb-8 text-4xl font-bold">My Downloads</h1>
            <div v-if="downloads.length === 0" class="text-center py-12">
                <h3 class="mt-2 text-sm font-medium text-gray-900">No downloads available</h3>
                <p class="mt-1 text-sm text-gray-500">Your purchased downloads will appear here.</p>
                <div class="mt-6">
                    <router-link :to="{ name: 'shop' }" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        Browse Products
                    </router-link>
                </div>
            </div>
            <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    <li v-for="download in downloads" :key="download.id" class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ download.orderItem.product.name }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Downloads remaining: {{ 3 - download.download_count }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Expires: {{ formatDate(download.expires_at) }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <a v-if="canDownload(download)" 
                                   :href="downloadUrl(download.download_token)"
                                   class="font-medium text-primary-600 hover:text-primary-500">
                                    Download
                                </a>
                                <span v-else class="text-gray-400">
                                    {{ isExpired(download) ? 'Expired' : 'No downloads remaining' }}
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const downloads = ref([])

onMounted(async () => {
    try {
        const response = await axios.get('/api/downloads')
        downloads.value = response.data
    } catch (error) {
        console.error('Error fetching downloads:', error)
    }
})

const formatDate = (date) => {
    if (!date) return 'Never'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const canDownload = (download) => {
    return download.download_count < 3 && 
           (!download.expires_at || new Date(download.expires_at) > new Date())
}

const isExpired = (download) => {
    return download.expires_at && new Date(download.expires_at) <= new Date()
}

const downloadUrl = (token) => {
    return `/shop/download/${token}`
}
</script>
