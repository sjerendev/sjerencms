<template>
    <div>
        <NormalNav v-if="type === 'normal'"
            :navigation="menuItems"
            :settings="settings"
        />
        <HamburgerNav v-else
            :navigation="menuItems"
            :settings="settings"
            :show-contact="showContact"
            :show-social="showSocial"
            :contact-info="contactInfo"
            :facebook="facebook"
            :instagram="instagram"
            :linkedin="linkedin"
        />
    </div>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue'
import NormalNav from './navigation/NormalNav.vue'
import HamburgerNav from './navigation/HamburgerNav.vue'

const menuItems = ref([])

onMounted(async () => {
    try {
        const response = await fetch('/api/navigation')
        const pages = await response.json()
        
        // Transform the flat pages array into a hierarchical structure
        const topLevelPages = pages.filter(page => !page.parent_id)
        menuItems.value = topLevelPages.map(page => {
            const children = pages.filter(child => child.parent_id === page.id)
            return {
                ...page,
                isOpen: false,
                children: children.length > 0 ? children : undefined
            }
        })
    } catch (error) {
        console.error('Failed to fetch navigation:', error)
    }
})

const props = defineProps({
    type: {
        type: String,
        default: 'normal'
    },
    settings: {
        type: Object,
        required: true
    },
    showContact: {
        type: Boolean,
        default: false
    },
    showSocial: {
        type: Boolean,
        default: false
    },
    contactInfo: {
        type: String,
        default: ''
    },
    facebook: {
        type: String,
        default: ''
    },
    instagram: {
        type: String,
        default: ''
    },
    linkedin: {
        type: String,
        default: ''
    }
})
</script>
