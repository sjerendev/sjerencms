<template>
    <MainLayout>
        <router-view v-slot="{ Component, route }">
            <Transition mode="out-in" :css="false" @enter="pageEnter" @leave="pageLeave">
                <component
                    :is="Component"
                    :key="route.fullPath"
                    class="route-view-shell"
                />
            </Transition>
        </router-view>
    </MainLayout>
    <CookieConsent />
</template>

<script setup>
import MainLayout from './components/layout/MainLayout.vue'
import CookieConsent from './components/common/CookieConsent.vue'
import { usePageTransitions } from './composables/usePageTransitions'

const { pageEnter, pageLeave } = usePageTransitions()
</script>
