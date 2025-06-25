import { createSSRApp } from 'vue'
import { createHead, renderHeadToString } from '@vueuse/head'
import { createPinia } from 'pinia'
import App from './App.vue'
import { createRouter } from './router'
import renderer from '@vue/server-renderer'

export async function render(url) {
    const app = createSSRApp(App)
    const head = createHead()
    const pinia = createPinia()
    const router = createRouter('server')
    
    app.use(router)
    app.use(head)
    app.use(pinia)
    
    // Set the router to the desired URL before rendering
    await router.push(url)
    await router.isReady()
    
    // Render the app
    const ctx = {}
    const html = await renderer.renderToString(app, ctx)
    const { headTags } = await renderHeadToString(head)
    
    return {
        html,
        head: headTags,
        state: null // You can pass initial state here if needed
    }
}
