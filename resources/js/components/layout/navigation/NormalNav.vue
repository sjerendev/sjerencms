<template>
    <nav class="px-8 py-4 transition-all duration-300 z-50" :class="{
        'fixed w-full top-0': isSticky && hasScrolled,
        'relative': !isSticky || !hasScrolled
    }" :style="{
        backgroundColor: 'var(--background)',
        boxShadow: isSticky ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
        transform: (isSticky && hasScrolled) ? 'translateY(0)' :
            (!isSticky && hasScrolled) ? 'translateY(-100%)' : 'none'
    }">
        <!-- Desktop Navigation -->
        <div class="items-center justify-between hidden md:flex">
            <router-link to="/" class="flex items-center">
                <img v-if="settings?.logo" :src="settings.logo" alt="Logo" class="h-8">
                <span v-else class="text-xl font-bold">{{ settings?.site_name }}</span>
            </router-link>

            <div class="flex space-x-8 items-center">
                <div v-for="item in navigation" :key="item.id" class="relative group">
                    <router-link 
                        v-if="!item.children" 
                        :to="item.slug === 'blogg' ? { name: 'blog' } : { name: 'page', params: { slug: item.slug } }"
                        class="text-[var(--text-color)] hover:opacity-70 transition-opacity"
                    >
                        {{ item.title }}
                    </router-link>
                    <div v-else class="relative inline-block group">
                        <button
                            class="flex items-center text-[var(--text-color)] hover:opacity-70 transition-opacity space-x-1">
                            <span>{{ item.title }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute -bottom-2 left-0 right-0 h-2 bg-transparent"></div>
                        <div
                            class="absolute left-0 z-10 invisible w-48 py-2 mt-2 bg-[var(--background)] rounded-md shadow-lg group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200">
                            <router-link 
                                v-for="child in item.children" 
                                :key="child.id"
                                :to="child.slug === 'blogg' ? { name: 'blog' } : { name: 'page', params: { slug: child.slug } }"
                                class="block px-4 py-2 text-[var(--text-color)] hover:opacity-70 transition-opacity"
                            >
                                {{ child.title }}
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle
                <button 
                    @click="darkModeStore.toggleDarkMode()"
                    class="p-2 rounded-full transition-colors"
                    :class="darkModeStore.isDark ? 'bg-white' : 'bg-[var(--text-color)]'"
                    :aria-label="darkModeStore.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <svg
                        v-if="!darkModeStore.isDark"
                        class="w-5 h-5 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                        />
                    </svg>
                    <svg 
                        v-else
                        class="w-5 h-5 text-[#111820]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                        />
                    </svg>
                </button>
                --->
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden">
            <div class="flex items-center justify-between">
                <router-link to="/" class="flex items-center">
                    <img v-if="settings?.logo" :src="settings.logo" alt="Logo" class="h-8">
                    <span v-else class="text-xl font-bold">{{ settings?.site_name }}</span>
                </router-link>

                <button @click="isOpen = !isOpen" class="text-[var(--text-color)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div v-if="isOpen"
                class="fixed inset-x-0 top-[64px] bottom-0 bg-[var(--background)] z-50 overflow-y-auto border-t border-gray-200">
                <div class="flex flex-col items-center py-8 space-y-6">
                    <template v-for="item in navigation" :key="item.id">
                        <router-link v-if="!item.children" :to="{ name: 'page', params: { slug: item.slug } }"
                            class="text-[var(--text-color)] text-xl hover:opacity-70 transition-opacity"
                            @click="isOpen = false">
                            {{ item.title }}
                        </router-link>

                        <div v-else class="flex flex-col items-center space-y-4">
                            <span class="text-[var(--text-color)] text-lg">{{ item.title }}</span>
                            <div class="flex flex-col items-center space-y-4">
                                <router-link v-for="child in item.children" :key="child.id"
                                    :to="{ name: 'page', params: { slug: child.slug } }"
                                    class="text-[var(--text-color)] text-base hover:opacity-70 transition-opacity"
                                    @click="isOpen = false">
                                    {{ child.title }}
                                </router-link>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import { useDarkModeStore } from '../../../stores/darkMode'

const props = defineProps({
    navigation: {
        type: Array,
        required: true
    },
    settings: {
        type: Object,
        required: true
    }
})

const darkModeStore = useDarkModeStore()
const isOpen = ref(false)
const menuContainer = ref(null)
const isSticky = ref(false)
const hasScrolled = ref(false)
const lastScrollTop = ref(0)
const scrollThreshold = 5

const handleScroll = () => {
    const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop

    // Mark that we've scrolled
    hasScrolled.value = currentScrollTop > 0

    // Only react to significant scroll amounts
    if (Math.abs(currentScrollTop - lastScrollTop.value) <= scrollThreshold) {
        return
    }

    // When scrolling up and not at the top, make it sticky
    if (currentScrollTop < lastScrollTop.value && currentScrollTop > 0) {
        isSticky.value = true
    }
    // When scrolling down or at the top, remove sticky
    else {
        isSticky.value = false
    }

    lastScrollTop.value = currentScrollTop
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})

function toggleMenu(open) {
    if (open) {
        gsap.fromTo(menuContainer.value,
            {
                scaleY: 0,
                transformOrigin: 'top'
            },
            {
                scaleY: 1,
                duration: 0.5,
                ease: 'power2.out'
            }
        )
    } else {
        gsap.to(menuContainer.value, {
            scaleY: 0,
            duration: 0.5,
            ease: 'power2.in',
            transformOrigin: 'top'
        })
    }
}

watch(isOpen, (newValue) => {
    toggleMenu(newValue)
})
</script>
