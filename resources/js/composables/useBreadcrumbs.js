import { computed } from 'vue'
import { useRoute } from 'vue-router'

export function useBreadcrumbs(pageData) {
    const route = useRoute()
    
    const breadcrumbs = computed(() => {
        const segments = route.path.split('/').filter(Boolean)
        const items = []
        let currentPath = ''

        // Always add home
        items.push({
            '@type': 'ListItem',
            position: 1,
            name: 'Hem',
            item: '/'
        })

        // Add intermediate paths
        segments.forEach((segment, index) => {
            currentPath += `/${segment}`
            const position = index + 2 // +2 because home is 1

            // Special case for blog
            if (segment === 'blogg') {
                items.push({
                    '@type': 'ListItem',
                    position,
                    name: 'Blogg',
                    item: currentPath
                })
            }
            // Add current page
            else if (index === segments.length - 1 && pageData.value) {
                items.push({
                    '@type': 'ListItem',
                    position,
                    name: pageData.value.title,
                    item: currentPath
                })
            }
        })

        return items
    })

    // Add breadcrumb structured data
    const addBreadcrumbSchema = () => {
        if (typeof window === 'undefined') return

        const script = document.createElement('script')
        script.type = 'application/ld+json'
        script.text = JSON.stringify({
            '@context': 'https://schema.org',
            '@type': 'BreadcrumbList',
            itemListElement: breadcrumbs.value
        })
        document.head.appendChild(script)
    }

    return {
        breadcrumbs,
        addBreadcrumbSchema
    }
}
