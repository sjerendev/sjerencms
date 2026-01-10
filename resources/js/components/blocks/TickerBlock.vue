<template>
    <section :class="['ticker-block', block.section_class]">
        <div class="ticker-stripe" :style="{ transform: `rotate(${block.angle / 2.5}deg)` }">
            <div 
                class="ticker-content"
                :style="{ 
                    'background-color': block.background_color,
                    'color': block.text_color,
                    'transform': 'translateZ(0)' /* Force GPU acceleration */
                }"
            >
                <div class="ticker-track" ref="track" :class="block.direction">
                    <div 
                        class="ticker-item" 
                        v-for="(word, index) in block.words" 
                        :key="index"
                    >{{ word }}</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { gsap } from 'gsap';

const props = defineProps({
    block: {
        type: Object,
        required: true,
        default: () => ({
            background_color: '#ffffff',
            text_color: '#000000',
            angle: 0,
            direction: 'left-to-right',
            words: [],
            section_class: ''
        })
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
});

const track = ref(null);
let animation;

onMounted(() => {
    // Wait for next tick to ensure DOM is fully rendered
    setTimeout(() => {
        if (!track.value) return;
        
        const items = Array.from(track.value.querySelectorAll('.ticker-item'));
        if (items.length === 0) return;
        
        const isRightToLeft = props.block.direction === 'right-to-left';
        
        // Clone items multiple times for a more seamless loop
        for (let i = 0; i < 3; i++) {
            items.forEach(item => {
                track.value.appendChild(item.cloneNode(true));
            });
        }
        
        // Calculate the width of the content
        const contentWidth = items.reduce((total, item) => total + item.offsetWidth, 0);
        
        // Force a browser repaint to ensure smooth animation
        track.value.style.display = 'none';
        track.value.offsetHeight; // Trigger reflow
        track.value.style.display = 'flex';
        
        // Set initial position based on direction
        if (isRightToLeft) {
            // For right-to-left, start at the negative position
            gsap.set(track.value, { x: -contentWidth, force3D: true });
        }
        
        // Create infinite loop animation with improved settings
        animation = gsap.to(track.value, {
            // For left-to-right, animate to negative
            // For right-to-left, animate to positive
            x: isRightToLeft ? 0 : -contentWidth,
            ease: 'none',
            duration: contentWidth / 50, // Adjust speed based on content length
            repeat: -1,
            force3D: true, // Force 3D transforms for smoother animation
            onRepeat: () => {
                // Reset position for seamless looping
                gsap.set(track.value, { 
                    x: isRightToLeft ? -contentWidth : 0, 
                    force3D: true 
                });
            }
        });
    }, 100);
});

onUnmounted(() => {
    if (animation) {
        animation.kill();
    }
});
</script>

<style scoped>
.ticker-block {
    position: relative;
    overflow: hidden;
    /* Improve rendering */
    transform-style: preserve-3d;
    perspective: 1000px;
}

.ticker-stripe {
    position: relative;
    width: 120%; /* Extra width to cover gaps when rotated */
    left: -10%;
    transform-origin: center center;
    overflow: hidden;
    /* Improve rendering quality */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    backface-visibility: hidden;
    will-change: transform;
}

.ticker-content {
    width: 100%;
    padding: 20px 0;
    overflow: hidden;
    /* Improve rendering quality */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    backface-visibility: hidden;
    will-change: transform;
    /* Add subtle shadow to smooth edges */
    box-shadow: 0 0 1px rgba(0,0,0,0.05);
}

.ticker-track {
    position: relative;
    white-space: nowrap;
    display: flex;
}

.ticker-item {
    padding: 0 20px;
    font-size: 1.5rem;
    font-weight: bold;
    display: inline-block;
}
</style>
