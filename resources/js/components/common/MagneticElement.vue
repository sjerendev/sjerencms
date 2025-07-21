<template>
  <div
    ref="magneticElement"
    class="magnetic-element"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @mousemove="handleMouseMove"
  >
    <slot></slot>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { gsap } from 'gsap'

const props = defineProps({
  // Magnetic strength (how much the element moves towards the cursor)
  strength: {
    type: Number,
    default: 0.3
  },
  // Animation duration for the magnetic effect
  duration: {
    type: Number,
    default: 0.4
  },
  // Animation duration for snapping back to original position
  snapDuration: {
    type: Number,
    default: 0.6
  },
  // Easing function for magnetic movement
  ease: {
    type: String,
    default: 'power2.out'
  },
  // Easing function for snapping back
  snapEase: {
    type: String,
    default: 'elastic.out(1, 0.3)'
  },
  // Whether to scale the element on hover
  scale: {
    type: Boolean,
    default: false
  },
  // Scale amount when hovering
  scaleAmount: {
    type: Number,
    default: 1.05
  },
  // Whether to add a subtle rotation effect
  rotate: {
    type: Boolean,
    default: false
  },
  // Maximum rotation in degrees
  maxRotation: {
    type: Number,
    default: 5
  },
  // Disable on mobile devices
  disableOnMobile: {
    type: Boolean,
    default: true
  }
})

const magneticElement = ref(null)
let isHovering = false
let animationFrame = null

// Check if device is mobile
const isMobile = () => {
  return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

const handleMouseEnter = (event) => {
  if (props.disableOnMobile && isMobile()) return
  
  isHovering = true
  
  // Scale effect on hover
  if (props.scale) {
    gsap.to(magneticElement.value, {
      scale: props.scaleAmount,
      duration: props.duration,
      ease: props.ease
    })
  }
}

const handleMouseLeave = () => {
  if (props.disableOnMobile && isMobile()) return
  
  isHovering = false
  
  // Cancel any pending animation frame
  if (animationFrame) {
    cancelAnimationFrame(animationFrame)
    animationFrame = null
  }
  
  // Snap back to original position with elastic effect
  gsap.to(magneticElement.value, {
    x: 0,
    y: 0,
    rotation: 0,
    scale: 1,
    duration: props.snapDuration,
    ease: props.snapEase
  })
}

const handleMouseMove = (event) => {
  if (props.disableOnMobile && isMobile() || !isHovering) return
  
  // Cancel previous animation frame
  if (animationFrame) {
    cancelAnimationFrame(animationFrame)
  }
  
  animationFrame = requestAnimationFrame(() => {
    const rect = magneticElement.value.getBoundingClientRect()
    const centerX = rect.left + rect.width / 2
    const centerY = rect.top + rect.height / 2
    
    // Calculate distance from center
    const deltaX = event.clientX - centerX
    const deltaY = event.clientY - centerY
    
    // Apply magnetic effect with strength multiplier
    const moveX = deltaX * props.strength
    const moveY = deltaY * props.strength
    
    // Calculate rotation if enabled
    let rotation = 0
    if (props.rotate) {
      const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY)
      const maxDistance = Math.sqrt(rect.width * rect.width + rect.height * rect.height) / 2
      const rotationIntensity = Math.min(distance / maxDistance, 1)
      rotation = (deltaX / rect.width) * props.maxRotation * rotationIntensity
    }
    
    // Animate to new position
    gsap.to(magneticElement.value, {
      x: moveX,
      y: moveY,
      rotation: rotation,
      duration: props.duration,
      ease: props.ease
    })
  })
}

onMounted(() => {
  // Set initial state
  gsap.set(magneticElement.value, {
    x: 0,
    y: 0,
    rotation: 0,
    scale: 1
  })
})

onUnmounted(() => {
  // Clean up animations and animation frames
  if (animationFrame) {
    cancelAnimationFrame(animationFrame)
  }
  gsap.killTweensOf(magneticElement.value)
})
</script>

<style scoped>
.magnetic-element {
  display: inline-block;
  will-change: transform;
}

/* Ensure the wrapped element maintains its pointer events */
.magnetic-element > * {
  pointer-events: auto;
}
</style>