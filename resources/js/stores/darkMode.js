import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useDarkModeStore = defineStore('darkMode', () => {
    const isDark = ref(false)

    function toggleDarkMode() {
        isDark.value = !isDark.value
        document.documentElement.classList.toggle('dark')
        localStorage.setItem('darkMode', isDark.value ? 'dark' : 'light')
    }

    function initDarkMode() {
        const savedMode = localStorage.getItem('darkMode')
        if (savedMode === 'dark') {
            isDark.value = true
            document.documentElement.classList.add('dark')
        }
    }

    return {
        isDark,
        toggleDarkMode,
        initDarkMode
    }
})
