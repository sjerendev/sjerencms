<template>
  <transition name="cookie-fade" appear>
    <div v-if="!hasConsent" class="fixed left-4 bottom-4 right-4 sm:left-auto sm:right-4 sm:bottom-4 sm:w-[400px] bg-[#111820] shadow-lg rounded-lg p-4 z-50">
      <div class="flex flex-col gap-4">
        <div class="text-white">
          <h4 class="text-lg font-medium mb-2">Vi värdesätter din integritet</h4>
          <p class="mb-0 text-sm text-white/90">
            Vi använder cookies för att förbättra din surfupplevelse, visa personligt anpassade annonser eller innehåll
            och analysera vår trafik. Genom att klicka på "Godkänn" samtycker du till vår användning av cookies.
          </p>
        </div>
        <div class="flex gap-2">
          <button @click="acceptCookies"
            class="flex-1 px-4 py-2 bg-white text-[#111820] rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
            Godkänn
          </button>
          <button @click="declineCookies"
            class="flex-1 px-4 py-2 border border-white/30 text-white rounded-lg hover:bg-white/10 transition-colors text-sm font-medium">
            Avböj
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const COOKIE_CONSENT_KEY = 'cookie_consent'
const hasConsent = ref(false)

const checkConsent = () => {
  const consent = localStorage.getItem(COOKIE_CONSENT_KEY)
  hasConsent.value = consent !== null
}

const acceptCookies = () => {
  localStorage.setItem(COOKIE_CONSENT_KEY, 'accepted')
  hasConsent.value = true
}

const declineCookies = () => {
  localStorage.setItem(COOKIE_CONSENT_KEY, 'declined')
  hasConsent.value = true
  window.location.href = 'https://www.google.com/'
}

onMounted(() => {
  checkConsent()
})
</script>

<style scoped>
.cookie-fade-enter-active,
.cookie-fade-leave-active {
  transition: all 0.3s ease;
}

.cookie-fade-enter-from,
.cookie-fade-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
