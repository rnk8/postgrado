import './bootstrap';
import '../css/app.css';

// Imports de Vue y Inertia
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy'; // Este archivo lo genera Laravel automáticamente

// Configuración del nombre de la aplicación
const appName = import.meta.env.VITE_APP_NAME || 'Sistema Postgrado UAGRM';

// Inicialización de la aplicación Inertia + Vue
createInertiaApp({
    // Configuración del título de las páginas
    title: (title) => `${title} - ${appName}`,
    
    // Resolución automática de componentes Vue desde el directorio Pages
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    
    // Configuración del setup de la aplicación Vue
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)  // Plugin de Inertia
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    // Configuración del progreso de carga
    progress: {
        color: '#4F46E5',     // Color del indicador de progreso
        showSpinner: true,    // Mostrar spinner de carga
    },
});
