import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toaster from './Components/Toaster.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ 
            render: () => h('div', [
                h(App, props),
                h(Toaster)
            ])
        })
            .use(plugin)
            .use(ZiggyVue);
        
        // Handle Inertia flash messages
        if (props.initialPage.props.flash) {
            const flash = props.initialPage.props.flash as { success?: string; error?: string };
            setTimeout(async () => {
                const { useToast } = await import('./composables/useToast');
                if (flash.success) {
                    useToast().success(flash.success);
                }
                if (flash.error) {
                    useToast().error(flash.error);
                }
            }, 100);
        }
        
        app.mount(el);
    },
    progress: {
        color: '#EC4899',
        showSpinner: true,
    },
});
