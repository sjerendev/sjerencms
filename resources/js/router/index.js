import { createRouter as _createRouter, createWebHistory, createMemoryHistory } from 'vue-router'

export function createRouter(type = 'client') {
    return _createRouter({
        history: type === 'client' ? createWebHistory() : createMemoryHistory(),
        routes: [
            {
                path: '/',
                name: 'home',
                component: () => import('../pages/Home.vue')
            },
            {
                path: '/blogg',
                name: 'blog',
                component: () => import('../pages/Blog.vue')
            },
            {
                path: '/blogg/:slug',
                name: 'post',
                component: () => import('../pages/Post.vue')
            },
            {
                path: '/shop',
                name: 'shop',
                component: () => import('../pages/Shop.vue')
            },
            {
                path: '/shop/products/:slug',
                name: 'product',
                component: () => import('../pages/Product.vue')
            },
            {
                path: '/shop/checkout',
                name: 'checkout',
                component: () => import('../pages/Checkout.vue')
            },
            {
                path: '/shop/downloads',
                name: 'downloads',
                component: () => import('../pages/Downloads.vue')
            },
            {
                path: '/:slug',
                name: 'page',
                component: () => import('../pages/Page.vue')
            },
            {
                path: '/:pathMatch(.*)*',
                name: 'not-found',
                component: () => import('../pages/NotFound.vue')
            }
        ]
    })
}
