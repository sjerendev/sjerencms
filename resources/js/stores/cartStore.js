import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: []
    }),

    getters: {
        totalItems: (state) => state.items.length,
        
        totalPrice: (state) => {
            return state.items.reduce((total, item) => {
                return total + item.totalPrice
            }, 0).toFixed(2)
        }
    },

    actions: {
        addItem(item) {
            // Check if the item already exists with the same addons
            const existingItemIndex = this.items.findIndex(i => {
                if (i.id !== item.id) return false
                
                // Compare selected addons
                const existingAddonIds = i.selectedAddons.map(a => a.id).sort()
                const newAddonIds = item.selectedAddons.map(a => a.id).sort()
                
                return JSON.stringify(existingAddonIds) === JSON.stringify(newAddonIds)
            })

            if (existingItemIndex !== -1) {
                // Update existing item
                this.items[existingItemIndex].quantity += item.quantity
                this.items[existingItemIndex].totalPrice += item.totalPrice
            } else {
                // Add new item
                this.items.push(item)
            }
        },

        removeItem(index) {
            this.items.splice(index, 1)
        },

        clearCart() {
            this.items = []
        }
    },

    persist: true
})
