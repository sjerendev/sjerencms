<template>
    <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div v-if="cart.isOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cart.toggleCart"></div>

        <div v-if="cart.isOpen" class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div class="pointer-events-auto w-screen max-w-md">
                        <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                            <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">Shopping cart</h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button 
                                            type="button" 
                                            class="-m-2 p-2 text-gray-400 hover:text-gray-500"
                                            @click="cart.toggleCart"
                                        >
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <div class="flow-root">
                                        <ul role="list" class="-my-6 divide-y divide-gray-200">
                                            <li v-for="item in cart.items" :key="item.id" class="flex py-6">
                                                <div class="flex-1 ml-4">
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h3>{{ item.name }}</h3>
                                                        <p class="ml-4">{{ formatPrice(item.price) }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex">
                                                    <button 
                                                        type="button" 
                                                        @click="cart.removeItem(item.id)"
                                                        class="font-medium text-primary-600 hover:text-primary-500"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <p>Total</p>
                                    <p>{{ cart.formattedTotal }}</p>
                                </div>
                                <p class="mt-0.5 text-sm text-gray-500">Taxes calculated at checkout.</p>
                                <div class="mt-6">
                                    <a
                                        :href="route('shop.checkout')"
                                        class="flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700"
                                    >
                                        Checkout
                                    </a>
                                </div>
                                <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                                    <p>
                                        or
                                        <button 
                                            type="button" 
                                            class="font-medium text-primary-600 hover:text-primary-500"
                                            @click="cart.toggleCart"
                                        >
                                            Continue Shopping
                                            <span aria-hidden="true"> &rarr;</span>
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCartStore } from '../../stores/cartStore'

const cart = useCartStore()

function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price)
}
</script>
