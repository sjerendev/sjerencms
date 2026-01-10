<template>
  <div class="language-switcher">
    <button 
      @click="switchTo('sv')"
      :class="{
        'active': currentLanguage === 'sv',
        'inactive': currentLanguage !== 'sv'
      }"
      class="language-btn"
    >
      Svenska
    </button>
    <span class="separator">|</span>
    <button 
      @click="switchTo('en')"
      :class="{
        'active': currentLanguage === 'en',
        'inactive': currentLanguage !== 'en'
      }"
      class="language-btn"
    >
      English
    </button>
  </div>
</template>

<script setup>
import { useLanguage } from '../composables/useLanguage'

const { currentLanguage, switchLanguage } = useLanguage()

const switchTo = (lang) => {
  switchLanguage(lang)
}
</script>

<style scoped>
.language-switcher {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.language-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  transition: all 0.2s ease;
  font-size: inherit;
  font-family: inherit;
}

.language-btn.active {
  font-weight: 600;
  color: #1f2937;
}

.language-btn.inactive {
  color: #6b7280;
  opacity: 0.7;
}

.language-btn:hover {
  background-color: #f3f4f6;
  opacity: 1;
}

.separator {
  color: #d1d5db;
  font-weight: 300;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .language-btn.active {
    color: #f9fafb;
  }
  
  .language-btn.inactive {
    color: #9ca3af;
  }
  
  .language-btn:hover {
    background-color: #374151;
  }
  
  .separator {
    color: #4b5563;
  }
}
</style>