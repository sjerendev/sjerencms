<template>
    <section :class="block.section_class">
        <div class="container py-12 mx-auto px-6 2xl:px-0">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-8" v-if="block.heading || block.subheading">
                    <h2 class="text-3xl font-bold mb-4" v-if="block.heading">{{ block.heading }}</h2>
                    <p class="text-gray-600" v-if="block.subheading">{{ block.subheading }}</p>
                </div>
                <div v-if="block.section_description" v-html="getMarkdownContent(block.section_description)"
                    class="max-w-3xl mx-auto mb-12 text-center text-gray-500"></div>
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Namn
                            </label>
                            <input type="text" v-model="formData.name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                E-post
                            </label>
                            <input type="email" v-model="formData.email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Gällande
                        </label>
                        <input type="text" v-model="formData.subject" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Meddelande
                        </label>
                        <textarea v-model="formData.message" required rows="5"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" :disabled="isSubmitting" @click.prevent="submitForm"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:bg-blue-400">
                            {{ isSubmitting ? 'Skickar...' : (block.submit_button_text || 'Skicka meddelande') }}
                        </button>
                    </div>
                    <transition name="fade">
                        <div v-if="submitStatus"
                            :class="['text-center p-4 rounded-lg',
                                submitStatus.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                            {{ submitStatus.message }}
                        </div>
                    </transition>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

<script setup>
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { ref, reactive } from 'vue'

const props = defineProps({
    block: {
        type: Object,
        required: true,
        default: () => ({
            notification_email: 'info@kalibercreative.se',
            success_message: 'Tack för ditt meddelande. Vi kommer att kontakta dig snart!'
        })
    }
})

const formData = reactive({
    name: '',
    email: '',
    subject: '',
    message: ''
})

const isSubmitting = ref(false)
const submitStatus = ref(null)

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const getCsrfToken = () => {
    const tokenCookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='));

    if (!tokenCookie) {
        throw new Error('CSRF token not found. Please refresh the page.');
    }

    return decodeURIComponent(tokenCookie.split('=')[1]);
}

const submitForm = async () => {
    console.log('Form submission started');
    isSubmitting.value = true;
    submitStatus.value = null;

    try {
        console.log('Sending form data:', formData);
        const response = await fetch('/contact-form-submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                form_data: formData,
                notification_email: props.block.notification_email || 'info@kalibercreative.se',
                success_message: props.block.success_message || 'Tack för ditt meddelande. Vi kommer att kontakta dig snart!'
            }),
            credentials: 'include'
        });

        const data = await response.json();
        console.log('Response:', data);

        if (response.ok) {
            submitStatus.value = {
                type: 'success',
                message: props.block.success_message || 'Tack för ditt meddelande. Vi kommer att kontakta dig snart!'
            };
            // Reset form
            Object.keys(formData).forEach(key => formData[key] = '');

            // Clear success message after 5 seconds
            setTimeout(() => {
                submitStatus.value = null;
            }, 5000);
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        submitStatus.value = {
            type: 'error',
            message: error.message
        };
    } finally {
        isSubmitting.value = false;
    }
};
</script>
