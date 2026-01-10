<template>
    <section :class="block.section_class">
        <div class="container px-6 py-12 mx-auto 2xl:px-0">
            <div
                class="mb-8 text-center"
                v-if="block.heading || block.subheading"
            >
                <h2 class="mb-4 text-3xl font-bold" v-if="block.heading">
                    {{ block.heading }}
                </h2>
                <p class="text-gray-600" v-if="block.subheading">
                    {{ block.subheading }}
                </p>
            </div>
            <div
                v-if="block.section_description"
                v-html="getMarkdownContent(block.section_description)"
                class="mx-auto mb-12 max-w-3xl text-center text-gray-500"
            ></div>

            <!-- Portfolio Grid -->
            <div class="portfolio-grid">
                <div
                    v-for="(item, index) in block.portfolio_items"
                    :key="index"
                    class="portfolio-item"
                    @mouseenter="handleMouseEnter($event, item)"
                    @mouseleave="handleMouseLeave"
                    @mousemove="handleMouseMove"
                >
                    <a
                        :href="item.website_url"
                        :target="item.website_url !== '#' ? '_blank' : '_self'"
                        class="flex flex-row justify-between items-center portfolio-link"
                    >
                        <h3 class="portfolio-title">{{ item.name }}</h3>
                        <div
                            v-html="getMarkdownContent(item.description)"
                            class="portfolio-description"
                        ></div>
                    </a>
                </div>
            </div>

            <!-- Floating Preview Image -->
            <div
                ref="previewImage"
                class="portfolio-preview"
                v-show="hoveredItem"
            >
                <img
                    v-if="hoveredItem"
                    :src="`/storage/${hoveredItem.preview_image}`"
                    :alt="hoveredItem.name"
                    class="object-cover object-top preview-img"
                />
            </div>
        </div>
        <div class="container mx-auto">
            <div class="portfolio-after">
                <p>
                    Detta är bara en bråkdel av vad jag gjort de senaste 15+
                    åren på olika byråer...
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { marked } from "marked";
import DOMPurify from "dompurify";
import { gsap } from "gsap";

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
});

const previewImage = ref(null);
const hoveredItem = ref(null);
let mouseX = 0;
let mouseY = 0;
let hideTimeout = null;

const getMarkdownContent = (text) => {
    if (!text) return "";
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const handleMouseEnter = (event, item) => {
    // Clear any pending hide timeout
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }

    const wasVisible = hoveredItem.value !== null;
    hoveredItem.value = item;

    if (!wasVisible) {
        // Set initial position
        gsap.set(previewImage.value, {
            x: event.clientX + 20,
            y: event.clientY - 100,
            scale: 0,
            opacity: 0,
        });

        // Animate in
        gsap.to(previewImage.value, {
            scale: 1,
            opacity: 1,
            duration: 0.3,
            ease: "power2.out",
        });
    } else {
        // Just update position if already visible
        gsap.to(previewImage.value, {
            x: event.clientX + 20,
            y: event.clientY - 100,
            duration: 0.3,
            ease: "power2.out",
        });
    }
};

const handleMouseLeave = () => {
    // Add a small delay before hiding to allow for quick transitions between items
    hideTimeout = setTimeout(() => {
        gsap.to(previewImage.value, {
            scale: 0,
            opacity: 0,
            duration: 0.2,
            ease: "power2.in",
            onComplete: () => {
                hoveredItem.value = null;
            },
        });
        hideTimeout = null;
    }, 50); // 50ms delay
};

const handleMouseMove = (event) => {
    if (!hoveredItem.value) return;

    mouseX = event.clientX;
    mouseY = event.clientY;

    gsap.to(previewImage.value, {
        x: mouseX + 20,
        y: mouseY - 100,
        duration: 0.3,
        ease: "power2.out",
    });
};

onMounted(() => {
    // Initialize GSAP
    gsap.set(previewImage.value, {
        scale: 0,
        opacity: 0,
    });
});

onUnmounted(() => {
    // Clean up any running animations and timeouts
    gsap.killTweensOf(previewImage.value);
    if (hideTimeout) {
        clearTimeout(hideTimeout);
    }
});
</script>

<style scoped>
.container-fluid {
    width: 100%;
    max-width: none;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}

.portfolio-item {
    border-bottom: 1px solid var(--primary-color);
    transition: background-color 0.3s ease;
}

.portfolio-link {
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.portfolio-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #111827;
    transition: color 0.3s ease;
}

.portfolio-description {
    font-size: 1.125rem;
    color: #6b7280;
    max-width: 600px;
}

.portfolio-preview {
    position: fixed;
    top: 0;
    left: 0;
    pointer-events: none;
    z-index: 1000;
    width: 360px;
    height: 260px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1),
        0 10px 10px -5px rgb(0 0 0 / 0.04);
}

.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .portfolio-title {
        font-size: 1.875rem;
    }

    .portfolio-description {
        font-size: 1rem;
    }

    .portfolio-link {
        padding: 1.5rem;
    }

    .portfolio-preview {
        display: none; /* Hide preview on mobile */
    }
}

@media (max-width: 640px) {
    .portfolio-title {
        font-size: 1.5rem;
    }

    .portfolio-link {
        padding: 1rem;
    }
}
</style>
