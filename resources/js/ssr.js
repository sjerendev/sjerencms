import { createApp } from './entry-server'
import { renderToString } from 'vue/server-renderer'
import { renderHeadToString } from '@vueuse/head'

export default async (context) => {
    const { app, router, head } = createApp()

    // set the router to the desired URL before rendering
    router.push(context.url)
    await router.isReady()

    // passing SSR context object which will be available in the page component
    const html = await renderToString(app, context)
    const { headTags } = await renderHeadToString(head)

    return {
        html,
        head: headTags,
        state: context.state
    }
}
