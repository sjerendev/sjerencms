<template>
    <div style="display: none"><!-- Font loader component --></div>
</template>

<script>
export default {
    name: 'FontLoader',
    mounted() {
        if ('fonts' in document) {
            // Create a link preconnect for Google Fonts
            const preconnectGoogle = document.createElement('link')
            preconnectGoogle.rel = 'preconnect'
            preconnectGoogle.href = 'https://fonts.googleapis.com'
            
            const preconnectGstatic = document.createElement('link')
            preconnectGstatic.rel = 'preconnect'
            preconnectGstatic.href = 'https://fonts.gstatic.com'
            preconnectGstatic.crossOrigin = 'anonymous'

            // Add font-display: swap to optimize rendering
            const fontLink = document.createElement('link')
            fontLink.rel = 'stylesheet'
            fontLink.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'
            fontLink.setAttribute('media', 'print')
            fontLink.setAttribute('onload', "this.media='all'")

            document.head.appendChild(preconnectGoogle)
            document.head.appendChild(preconnectGstatic)
            document.head.appendChild(fontLink)

            // Add a fallback
            const fallback = document.createElement('style')
            fallback.textContent = `
                @font-face {
                    font-family: 'Inter';
                    font-style: normal;
                    font-weight: 400;
                    font-display: swap;
                    src: local('Arial');
                }
            `
            document.head.appendChild(fallback)
        }
    }
}
</script>
