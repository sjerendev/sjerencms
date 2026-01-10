<template>
    <footer class="footer-bg py-12 text-white">
        <div class="container px-4 mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 text-center lg:text-start">
                <div class="footer-column">
                    <h3 class="mb-4 text-xl font-bold min-h-[28px]">Kalibr.</h3>
                    <p class="text-gray-400 min-h-[96px]">Vi är en utvecklings- ai- och designbyrå i Malmö, men har hela världen som
                        vårt spelfält. Vi brinner för att skapa lösningar som gör skillnad.</p>
                </div>

                <div class="footer-column">
                    <h3 class="mb-4 text-xl font-bold min-h-[28px]">Snabblänkar</h3>
                    <ul class="space-y-2 min-h-[96px]">
                        <li v-for="item in menuItems" :key="item.id">
                            <router-link :to="getRouteForSlug(item.slug)"
                                class="text-gray-400 hover:text-white">
                                {{ item.title }}
                            </router-link>
                        </li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="mb-4 text-xl font-bold min-h-[28px]">Kontakt</h3>
                    <ul class="space-y-2 text-gray-400 min-h-[96px]">
                        <li>E-post: info@kalibr.se</li>
                        <li>Telefon: +46 (0736) 15 12 20</li>
                        <li>Adress: Simrishamnsgatan 20a<br />SE-214 23 Malmö<br />Sweden</li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="mb-4 text-xl font-bold min-h-[28px]">Follow Us</h3>
                    <div class="flex space-x-4 justify-center lg:justify-start min-h-[96px]">
                        <a v-if="settings?.facebook_url" :href="settings.facebook_url" target="_blank" rel="noopener"
                            class="text-gray-400 hover:text-white">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a v-if="settings?.instagram_url" :href="settings.instagram_url" target="_blank" rel="noopener"
                            class="text-gray-400 hover:text-white">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a v-if="settings?.linkedin_url" :href="settings.linkedin_url" target="_blank" rel="noopener"
                            class="text-gray-400 hover:text-white">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 mt-8 text-center text-gray-400 border-t border-gray-800">
                <p class="min-h-[24px]"> {{ new Date().getFullYear() }} Kalibr. Alla rättigheter reserverade.</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useLanguage } from '../../composables/useLanguage'

const { currentLanguage } = useLanguage()
const settings = ref(null)
const menuItems = ref([])

const getRouteForSlug = (slug) => {
    if (slug === 'home') {
        return currentLanguage.value === 'en' ? '/en/' : '/'
    }
    const blogSlug = currentLanguage.value === 'en' ? 'blog' : 'blogg'
    if (slug === 'blogg' || slug === 'blog') {
        return { name: currentLanguage.value === 'en' ? 'page-en' : 'page', params: { slug: blogSlug } }
    }
    return { name: currentLanguage.value === 'en' ? 'page-en' : 'page', params: { slug } }
}

onMounted(async () => {
    try {
        const [navResponse, settingsResponse] = await Promise.all([
            fetch('/api/navigation/footer'),
            fetch('/api/settings')
        ])

        const [pages, siteSettings] = await Promise.all([
            navResponse.json(),
            settingsResponse.json()
        ])

        menuItems.value = pages
        settings.value = siteSettings
    } catch (error) {
        console.error('Failed to fetch footer data:', error)
    }
})
</script>

<style scoped>
.footer-bg {
    background-color: #111820;
}

.footer-column {
    display: flex;
    flex-direction: column;
    min-height: 200px;
}

@media (max-width: 1024px) {
    .footer-column {
        min-height: 150px;
    }
}
</style>
