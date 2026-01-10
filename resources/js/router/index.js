import { createRouter as _createRouter, createWebHistory, createMemoryHistory } from 'vue-router'

export function createRouter(type = 'client') {
    return _createRouter({
        history: type === 'client' ? createWebHistory() : createMemoryHistory(),
        routes: [
            // Swedish routes (no prefix)
            {
                path: '/',
                name: 'home',
                component: () => import('../pages/Home.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/blogg',
                name: 'blog',
                component: () => import('../pages/Blog.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/blogg/:slug',
                name: 'post',
                component: () => import('../pages/Post.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/shop',
                name: 'shop',
                component: () => import('../pages/Shop.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/shop/products/:slug',
                name: 'product',
                component: () => import('../pages/Product.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/shop/checkout',
                name: 'checkout',
                component: () => import('../pages/Checkout.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/shop/downloads',
                name: 'downloads',
                component: () => import('../pages/Downloads.vue'),
                meta: { language: 'sv' }
            },
            // English routes (with /en prefix)
            {
                path: '/en',
                name: 'home-en',
                component: () => import('../pages/Home.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/blog',
                name: 'blog-en',
                component: () => import('../pages/Blog.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/blog/:slug',
                name: 'post-en',
                component: () => import('../pages/Post.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/shop',
                name: 'shop-en',
                component: () => import('../pages/Shop.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/shop/products/:slug',
                name: 'product-en',
                component: () => import('../pages/Product.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/shop/checkout',
                name: 'checkout-en',
                component: () => import('../pages/Checkout.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/shop/downloads',
                name: 'downloads-en',
                component: () => import('../pages/Downloads.vue'),
                meta: { language: 'en' }
            },
            {
                path: '/en/:slug',
                name: 'page-en',
                component: () => import('../pages/Page.vue'),
                meta: { language: 'en' }
            },
            // Swedish page routes (must come after specific routes)
            {
                path: '/:slug',
                name: 'page',
                component: () => import('../pages/Page.vue'),
                meta: { language: 'sv' }
            },
            {
                path: '/:pathMatch(.*)*',
                name: 'not-found',
                component: () => import('../pages/NotFound.vue')
            }
        ]
    })
}
