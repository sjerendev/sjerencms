import { ref, onMounted, onUnmounted } from 'vue';

/**
 * WebGL magnetic distortion effect composable
 * Creates a fish-eye/bulge distortion that follows cursor position
 *
 * @param {Object} options
 * @param {number} options.intensity - Effect intensity (1.0 for carousel, ~0.6 for cards)
 * @param {Ref} options.containerRef - Vue ref to container element
 * @param {string} options.imageSelector - CSS selector for images within container
 */
export function useWebGLDistortion(options = {}) {
    const {
        intensity = 1.0,
        containerRef = null,
        imageSelector = 'img',
    } = options;

    const isSupported = ref(false);
    const isTouch = ref(false);

    let THREE = null;
    let scenes = [];
    let animationFrameId = null;
    let isInitialized = false;

    // Custom shader for magnetic distortion effect
    const vertexShader = `
        varying vec2 vUv;
        void main() {
            vUv = uv;
            gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
        }
    `;

    const fragmentShader = `
        uniform sampler2D uTexture;
        uniform vec2 uMouse;
        uniform float uIntensity;
        uniform float uTime;
        uniform float uHover;
        varying vec2 vUv;

        void main() {
            vec2 uv = vUv;

            // Calculate distance from cursor
            vec2 mousePos = uMouse;
            float dist = distance(uv, mousePos);

            // Magnetic fish-eye distortion
            float radius = 0.35;
            float strength = uIntensity * uHover * 0.15;

            if (dist < radius) {
                float factor = (radius - dist) / radius;
                factor = pow(factor, 2.0);

                // Bulge effect - push pixels away from cursor
                vec2 direction = normalize(uv - mousePos);
                uv -= direction * factor * strength;
            }

            // Sample texture with distorted UVs
            vec4 color = texture2D(uTexture, uv);
            gl_FragColor = color;
        }
    `;

    const checkSupport = () => {
        if (typeof window === 'undefined') return false;

        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            isSupported.value = !!gl;
            return !!gl;
        } catch (e) {
            isSupported.value = false;
            return false;
        }
    };

    const checkTouch = () => {
        if (typeof window === 'undefined') return true;
        isTouch.value = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        return isTouch.value;
    };

    const checkReducedMotion = () => {
        if (typeof window === 'undefined') return false;
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    };

    const createScene = async (imageEl) => {
        if (!THREE) {
            THREE = await import('three');
        }

        const container = imageEl.parentElement;
        const rect = imageEl.getBoundingClientRect();

        // Create canvas overlay
        const canvas = document.createElement('canvas');
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '1';

        // Hide original image
        imageEl.style.opacity = '0';

        // Setup container
        container.style.position = 'relative';
        container.appendChild(canvas);

        // Create Three.js scene
        const scene = new THREE.Scene();
        const camera = new THREE.OrthographicCamera(-0.5, 0.5, 0.5, -0.5, 0.1, 10);
        camera.position.z = 1;

        const renderer = new THREE.WebGLRenderer({
            canvas,
            alpha: true,
            antialias: true
        });
        renderer.setSize(rect.width, rect.height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Load texture
        const textureLoader = new THREE.TextureLoader();
        const texture = await new Promise((resolve) => {
            textureLoader.load(imageEl.src, resolve);
        });

        // Create mesh with shader material
        const geometry = new THREE.PlaneGeometry(1, 1);
        const material = new THREE.ShaderMaterial({
            uniforms: {
                uTexture: { value: texture },
                uMouse: { value: new THREE.Vector2(0.5, 0.5) },
                uIntensity: { value: intensity },
                uTime: { value: 0 },
                uHover: { value: 0 }
            },
            vertexShader,
            fragmentShader,
            transparent: true
        });

        const mesh = new THREE.Mesh(geometry, material);
        scene.add(mesh);

        // State
        const state = {
            scene,
            camera,
            renderer,
            material,
            mesh,
            canvas,
            imageEl,
            container,
            targetHover: 0,
            currentHover: 0,
            targetMouse: { x: 0.5, y: 0.5 },
            currentMouse: { x: 0.5, y: 0.5 }
        };

        // Event handlers
        const handleMouseMove = (e) => {
            const bounds = container.getBoundingClientRect();
            state.targetMouse.x = (e.clientX - bounds.left) / bounds.width;
            state.targetMouse.y = 1 - (e.clientY - bounds.top) / bounds.height;
        };

        const handleMouseEnter = () => {
            state.targetHover = 1;
        };

        const handleMouseLeave = () => {
            state.targetHover = 0;
        };

        const handleResize = () => {
            const newRect = container.getBoundingClientRect();
            renderer.setSize(newRect.width, newRect.height);
        };

        container.addEventListener('mousemove', handleMouseMove);
        container.addEventListener('mouseenter', handleMouseEnter);
        container.addEventListener('mouseleave', handleMouseLeave);
        window.addEventListener('resize', handleResize);

        state.cleanup = () => {
            container.removeEventListener('mousemove', handleMouseMove);
            container.removeEventListener('mouseenter', handleMouseEnter);
            container.removeEventListener('mouseleave', handleMouseLeave);
            window.removeEventListener('resize', handleResize);

            geometry.dispose();
            material.dispose();
            texture.dispose();
            renderer.dispose();

            if (canvas.parentElement) {
                canvas.parentElement.removeChild(canvas);
            }
            imageEl.style.opacity = '1';
        };

        return state;
    };

    const animate = () => {
        scenes.forEach((state) => {
            // Smooth interpolation
            const lerpFactor = 0.1;
            state.currentHover += (state.targetHover - state.currentHover) * lerpFactor;
            state.currentMouse.x += (state.targetMouse.x - state.currentMouse.x) * lerpFactor;
            state.currentMouse.y += (state.targetMouse.y - state.currentMouse.y) * lerpFactor;

            // Update uniforms
            state.material.uniforms.uHover.value = state.currentHover;
            state.material.uniforms.uMouse.value.set(state.currentMouse.x, state.currentMouse.y);
            state.material.uniforms.uTime.value += 0.016;

            state.renderer.render(state.scene, state.camera);
        });

        animationFrameId = requestAnimationFrame(animate);
    };

    const init = async () => {
        // Skip on server side
        if (typeof window === 'undefined') return;

        // Skip if already initialized
        if (isInitialized) return;

        // Check for touch devices and reduced motion preference
        if (checkTouch() || checkReducedMotion()) return;

        // Check WebGL support
        if (!checkSupport()) return;

        if (!containerRef?.value) return;

        const images = containerRef.value.querySelectorAll(imageSelector);
        if (!images.length) return;

        try {
            scenes = await Promise.all(
                Array.from(images).map(img => createScene(img))
            );

            isInitialized = true;
            animate();
        } catch (error) {
            console.warn('WebGL distortion initialization failed:', error);
        }
    };

    const destroy = () => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }

        scenes.forEach(state => {
            if (state.cleanup) {
                state.cleanup();
            }
        });
        scenes = [];
        isInitialized = false;
    };

    return {
        init,
        destroy,
        isSupported,
        isTouch
    };
}
