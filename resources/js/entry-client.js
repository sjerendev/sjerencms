import { createApp } from 'vue'
import { createHead } from '@vueuse/head'
import { createPinia } from 'pinia'
import App from './App.vue'
import { createRouter } from './router'
import { setupRouteViewTransitions } from './composables/usePageTransitions'

const app = createApp(App)
const head = createHead()
const pinia = createPinia()
const router = createRouter('client')

setupRouteViewTransitions(router)

app.use(router)
app.use(head)
app.use(pinia)

// wait until router is ready before mounting to ensure hydration match
router.isReady().then(() => {
    app.mount('#app')
})
