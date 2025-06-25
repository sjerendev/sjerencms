import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path'

export default defineConfig(({ command, mode }) => {
    const isSSR = mode === 'ssr'
    
    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/entry-client.js',
                    'resources/css/filament/admin/theme.css'
                ],
                ssr: 'resources/js/entry-server.js',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources')
            }
        },
        build: {
            rollupOptions: {
                output: {}
            }
        },
        optimizeDeps: {
            include: ['@iconify/vue']
        },
        server: {
            proxy: {
                '/api': {
                    target: 'http://localhost:8000',
                    changeOrigin: true,
                    secure: false
                }
            }
        }
    }
})
