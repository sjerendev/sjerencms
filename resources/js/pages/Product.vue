<template>
    <div>
        <div class="container px-4 py-12 mx-auto">
            <product-view :slug="$route.params.slug"></product-view>
        </div>
        <shopping-cart></shopping-cart>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
</script>
