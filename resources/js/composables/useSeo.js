import { useHead } from '@vueuse/head'
import { ref } from 'vue'

export function useSeo(pageData, defaults = {}) {
    const isClient = typeof window !== 'undefined'
    const origin = isClient ? window.location.origin : ''
    const currentUrl = isClient ? window.location.href : ''

    useHead(() => ({
        title: pageData.value?.meta_title || pageData.value?.title || defaults.title,
        meta: [
            {
                name: 'description',
                content: pageData.value?.meta_description || defaults.description
            },
            // OpenGraph
            {
                property: 'og:title',
                content: pageData.value?.meta_title || pageData.value?.title || defaults.title
            },
            {
                property: 'og:description',
                content: pageData.value?.meta_description || defaults.description
            },
            {
                property: 'og:image',
                content: pageData.value?.hero_image ? `${origin}/storage/${pageData.value.hero_image}` : defaults.image ? `${origin}${defaults.image}` : null
            },
            {
                property: 'og:type',
                content: defaults.type || 'website'
            },
            // Twitter
            {
                name: 'twitter:card',
                content: 'summary_large_image'
            },
            {
                name: 'twitter:title',
                content: pageData.value?.meta_title || pageData.value?.title || defaults.title
            },
            {
                name: 'twitter:description',
                content: pageData.value?.meta_description || defaults.description
            },
            {
                name: 'twitter:image',
                content: pageData.value?.hero_image ? `${origin}/storage/${pageData.value.hero_image}` : defaults.image ? `${origin}${defaults.image}` : null
            }
        ].filter(meta => meta.content), // Remove meta tags with null content
        link: [
            {
                rel: 'canonical',
                href: defaults.canonical || currentUrl || null
            }
        ].filter(link => link.href)
    }))
}
