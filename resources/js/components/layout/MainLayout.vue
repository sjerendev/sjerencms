<template>
    <div :id="pageId" :class="pageClass">
        <Navigation
            :type="settings.navigation_type"
            :settings="settings"
            :show-contact="settings.show_contact_in_menu"
            :show-social="settings.show_social_in_menu"
            :contact-info="settings.menu_contact_info"
            :facebook="settings.facebook_url"
            :instagram="settings.instagram_url"
            :linkedin="settings.linkedin_url"
        />
        <main>
            <slot></slot>
        </main>
        <Footer />
        <LanguageSwitcher />
    </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Navigation from './Navigation.vue'
import Footer from './Footer.vue'
import LanguageSwitcher from '../LanguageSwitcher.vue'

const route = useRoute()
const settings = ref({
    navigation_type: 'normal',
    show_contact_in_menu: false,
    show_social_in_menu: false,
    menu_contact_info: '',
    facebook_url: '',
    instagram_url: '',
    linkedin_url: '',
    logo: '',
    site_name: 'Site Name',
    head_code_snippets: '',
    footer_code_snippets: ''
})

const pageId = computed(() => `page-${route.params.slug || 'home'}`)
const pageClass = computed(() => `page-${route.params.slug || 'home'}`)

onMounted(async () => {
    try {
        const response = await fetch('/api/settings')
        settings.value = await response.json()
    } catch (error) {
        console.error('Error fetching settings:', error)
    }
})
</script>
