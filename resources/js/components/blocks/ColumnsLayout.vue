<template>
    <section
        :class="block.section_class"
        :style="{
            backgroundColor: block.background_color || 'transparent',
            paddingTop: block.padding_y || '4rem',
            paddingBottom: block.padding_y || '4rem',
            paddingLeft: block.padding_x || '1.5rem',
            paddingRight: block.padding_x || '1.5rem'
        }"
    >
        <div class="container mx-auto">
            <div
                class="columns-grid"
                :style="{
                    '--columns-mobile': mobileColumns,
                    '--columns-tablet': tabletColumns,
                    '--columns-desktop': desktopColumns,
                    '--gap': block.gap || '2rem',
                    '--align': alignItems
                }"
            >
                <div v-if="block.column_1?.length" class="column">
                    <BlockRenderer
                        v-for="(nestedBlock, index) in block.column_1"
                        :key="'col1-' + index"
                        :block="nestedBlock"
                    />
                </div>
                <div v-if="block.column_2?.length" class="column">
                    <BlockRenderer
                        v-for="(nestedBlock, index) in block.column_2"
                        :key="'col2-' + index"
                        :block="nestedBlock"
                    />
                </div>
                <div v-if="block.column_3?.length && showColumn3" class="column">
                    <BlockRenderer
                        v-for="(nestedBlock, index) in block.column_3"
                        :key="'col3-' + index"
                        :block="nestedBlock"
                    />
                </div>
                <div v-if="block.column_4?.length && showColumn4" class="column">
                    <BlockRenderer
                        v-for="(nestedBlock, index) in block.column_4"
                        :key="'col4-' + index"
                        :block="nestedBlock"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import BlockRenderer from '../BlockRenderer.vue'

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
})

const columnCount = computed(() => parseInt(props.block.columns) || 2)
const showColumn3 = computed(() => columnCount.value >= 3)
const showColumn4 = computed(() => columnCount.value >= 4)

// Convert ratio to grid-template-columns (fr units)
const ratioToGridColumns = (ratio, maxCols) => {
    const parts = ratio.split('-').map(Number).slice(0, maxCols)
    if (parts.length === 0) return '1fr'
    return parts.map(n => `${n}fr`).join(' ')
}

// Generate equal columns for responsive breakpoints
const generateEqualColumns = (count) => {
    return Array(count).fill('1fr').join(' ')
}

const mobileColumns = computed(() => {
    const cols = parseInt(props.block.columns_mobile) || 1
    return generateEqualColumns(cols)
})

const tabletColumns = computed(() => {
    const cols = parseInt(props.block.columns_tablet) || 2
    // If tablet has fewer columns than desktop, use equal widths
    const desktopCols = columnCount.value
    if (cols < desktopCols) {
        return generateEqualColumns(cols)
    }
    // If same as desktop, use the ratio
    return ratioToGridColumns(props.block.column_ratio || '1-1', cols)
})

const desktopColumns = computed(() => {
    const ratio = props.block.column_ratio || '1-1'
    return ratioToGridColumns(ratio, columnCount.value)
})

const alignItems = computed(() => {
    const align = props.block.vertical_align || 'start'
    return {
        'start': 'start',
        'center': 'center',
        'end': 'end',
        'stretch': 'stretch'
    }[align] || 'start'
})
</script>

<style scoped>
.columns-grid {
    display: grid;
    gap: var(--gap);
    align-items: var(--align);
    grid-template-columns: var(--columns-mobile);
}

/* Tablet: 768px+ */
@media (min-width: 768px) {
    .columns-grid {
        grid-template-columns: var(--columns-tablet);
    }
}

/* Desktop: 1024px+ */
@media (min-width: 1024px) {
    .columns-grid {
        grid-template-columns: var(--columns-desktop);
    }
}

.column > :deep(section) {
    padding: 0;
}
</style>
