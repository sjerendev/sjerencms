import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useLanguage() {
    const route = useRoute()
    const router = useRouter()
    
    const currentLanguage = computed(() => {
        return route.meta?.language || 'sv'
    })
    
    const isSwedish = computed(() => currentLanguage.value === 'sv')
    const isEnglish = computed(() => currentLanguage.value === 'en')
    
    const getLocalizedPath = (path, targetLanguage = null) => {
        const lang = targetLanguage || currentLanguage.value
        
        if (lang === 'sv') {
            return path
        } else if (lang === 'en') {
            return `/en${path}`
        }
        
        return path
    }
    
    const switchLanguage = (newLang) => {
        if (newLang === currentLanguage.value) return
        
        const currentPath = route.path
        let newPath
        
        if (newLang === 'sv') {
            // Remove /en prefix for Swedish
            newPath = currentPath.replace(/^\/en/, '') || '/'
        } else if (newLang === 'en') {
            // Add /en prefix for English
            if (currentPath.startsWith('/en')) {
                newPath = currentPath
            } else {
                newPath = `/en${currentPath}`
            }
        }
        
        // Handle specific route mappings
        if (newLang === 'en') {
            newPath = newPath.replace('/blogg', '/blog')
        } else if (newLang === 'sv') {
            newPath = newPath.replace('/blog', '/blogg')
        }
        
        router.push(newPath)
    }
    
    const getLanguageLabel = (lang) => {
        return {
            'sv': 'Svenska',
            'en': 'English'
        }[lang] || lang
    }
    
    return {
        currentLanguage,
        isSwedish,
        isEnglish,
        getLocalizedPath,
        switchLanguage,
        getLanguageLabel
    }
}