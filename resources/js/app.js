import './bootstrap';
import { createApp } from 'vue'
import { createHead } from '@vueuse/head'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useDarkModeStore } from './stores/darkMode'
import { useCartStore } from './stores/cartStore'
import ProductList from './components/shop/ProductList.vue'
import ProductView from './components/shop/ProductView.vue'
import ShoppingCart from './components/shop/ShoppingCart.vue'
import CheckoutForm from './components/shop/CheckoutForm.vue'

const app = createApp(App)
const head = createHead()
const pinia = createPinia()

app.use(router)
app.use(head)
app.use(pinia)

app.component('product-list', ProductList)
app.component('product-view', ProductView)
app.component('shopping-cart', ShoppingCart)
app.component('checkout-form', CheckoutForm)

// Initialize dark mode
const darkModeStore = useDarkModeStore()
darkModeStore.initDarkMode()

// Initialize cart store
const cartStore = useCartStore()

app.mount('#app')
