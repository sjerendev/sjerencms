<template>
    <section :class="block.section_class">
        <div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0">
            <h2 v-if="block.section_title" class="mb-16 text-3xl font-bold text-center min-h-[40px]">{{ block.section_title }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="(card, index) in block.cards" :key="index" class="iconCard">
                    <div class="iconCardContent">
                        <div class="iconBox">
                            <div class="icon-wrapper">
                                <Suspense>
                                    <template #default>
                                        <Icon 
                                            :icon="getIconName(card.icon)"
                                            class="text-[#19F2B2] w-6 h-6"
                                            width="24"
                                            height="24"
                                        />
                                    </template>
                                    <template #fallback>
                                        <div class="w-10 h-10 animate-pulse bg-gray-200 rounded-full"></div>
                                    </template>
                                </Suspense>
                            </div>
                        </div>
                        <div class="iconCardText">
                            <h3 class="text-xl font-bold mb-4 min-h-[28px]">{{ card.title }}</h3>
                            <p class="text-gray-600 mb-4 min-h-[72px]">{{ card.text }}</p>
                            <a v-if="card.link_url" :href="card.link_url" class="iconCardLink">Läs mer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

const getIconName = (iconName) => {
    if (!iconName) return '';
    if (iconName.startsWith('ph:')) return iconName;
    return `ph:${iconName}`;
};
</script>

<style scoped>
.iconCard {
    padding: 2rem;
    height: 100%;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.iconCard:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.iconCardContent {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.iconBox {
    margin-bottom: 1.5rem;
    min-height: 40px;
    display: flex;
    align-items: center;
}

.icon-wrapper {
    width: 48px;
    height: 48px;
    min-width: 48px;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #111820;
    border-radius: 9999px;
}

.icon-wrapper :deep(svg) {
    width: 24px !important;
    height: 24px !important;
    flex-shrink: 0;
}

.iconCardText {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.iconCardLink {
    color: #111820;
    font-weight: 600;
    margin-top: auto;
    display: inline-block;
    text-decoration: none;
    transition: color 0.2s;
}

.iconCardLink:hover {
    text-decoration: underline;
}
</style>
