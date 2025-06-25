<template>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
            <!-- Order summary -->
            <div class="mt-10 lg:mt-0">
                <h2 class="text-lg font-medium text-gray-900">Order summary</h2>

                <div class="mt-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="item in cart.items" :key="item.id" class="flex py-6 px-4 sm:px-6">
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h3 class="text-sm">
                                        <a href="#" class="font-medium text-gray-700 hover:text-gray-800">
                                            {{ item.name }}
                                        </a>
                                    </h3>
                                    <p class="ml-4 text-sm font-medium text-gray-900">{{ formatPrice(item.price) }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <dl class="border-t border-gray-200 py-6 px-4 space-y-6 sm:px-6">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ cart.formattedTotal }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                            <dt class="text-base font-medium">Total</dt>
                            <dd class="text-base font-medium text-gray-900">{{ cart.formattedTotal }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Payment -->
            <div class="mt-10 lg:mt-0">
                <h2 class="text-lg font-medium text-gray-900">Payment</h2>

                <div class="mt-6">
                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <div v-if="error" class="rounded-md bg-red-50 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <div class="mt-1">
                                    <input 
                                        type="email" 
                                        id="email" 
                                        v-model="email"
                                        required
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="card-element" class="block text-sm font-medium text-gray-700">Card details</label>
                                <div class="mt-1">
                                    <div 
                                        id="card-element" 
                                        class="block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="isProcessing"
                            class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isProcessing">Processing...</span>
                            <span v-else>Pay {{ cart.formattedTotal }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCartStore } from '../../stores/cartStore'
import { loadStripe } from '@stripe/stripe-js'

const cart = useCartStore()
const email = ref('')
const error = ref('')
const isProcessing = ref(false)
let stripe = null
let elements = null
let card = null

onMounted(async () => {
    // Initialize Stripe
    stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY)
    elements = stripe.elements()
    
    // Create card element
    card = elements.create('card')
    card.mount('#card-element')
    
    // Handle validation errors
    card.addEventListener('change', (event) => {
        if (event.error) {
            error.value = event.error.message
        } else {
            error.value = ''
        }
    })
})

const handleSubmit = async () => {
    if (!stripe || !elements) {
        return
    }

    isProcessing.value = true
    error.value = ''

    try {
        // Create payment intent
        const { data: { clientSecret } } = await axios.post('/api/create-payment-intent', {
            amount: cart.total * 100, // Convert to cents
            email: email.value
        })

        // Confirm payment
        const { error: stripeError } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    email: email.value
                }
            }
        })

        if (stripeError) {
            error.value = stripeError.message
        } else {
            // Payment successful
            await axios.post(route('shop.checkout.process'), {
                payment_intent: clientSecret,
                email: email.value,
                items: cart.items
            })

            // Clear cart and redirect to downloads page
            cart.clearCart()
            window.location.href = route('shop.downloads')
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred during checkout'
    } finally {
        isProcessing.value = false
    }
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price)
}
</script>
