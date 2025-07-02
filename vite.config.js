import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import ziggyPlugin from 'vite-plugin-ziggy';
import svgLoader from 'vite-svg-loader';

export default defineConfig({
    plugins: [
        // Plugin de Laravel para integración con Vite
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Plugin de Vue para soporte de SFC (Single File Components)
        vue({
            template: {
                transformAssetUrls: {
                    // Configuración para manejar assets en templates de Vue
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // Plugin de Tailwind CSS para estilos
        tailwindcss(),
        // Plugin de Ziggy para Vite
        ziggyPlugin(),
        svgLoader(),
    ],
});
