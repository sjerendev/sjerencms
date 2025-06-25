<template>
    <MainLayout>
        <router-view></router-view>
    </MainLayout>
    <CookieConsent />
</template>

<script setup>
import MainLayout from './components/layout/MainLayout.vue'
import CookieConsent from './components/common/CookieConsent.vue'
</script>
