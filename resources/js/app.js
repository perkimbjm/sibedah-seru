import "./bootstrap";
import "../css/app.css";
import "flowbite";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createPinia } from "pinia";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        // Buat app instance terlebih dahulu
        const app = createApp({ render: () => h(App, props) });

        // Buat instance Pinia
        const pinia = createPinia();

        const loadAOS = async () => {
            const { default: AOS } = await import("aos");
            await import("aos/dist/aos.css");
            AOS.init();
        };

        window.addEventListener("load", loadAOS);

        // Register semua plugin
        app.use(plugin) // Inertia plugin
            .use(ZiggyVue) // Ziggy plugin
            .use(pinia); // Pinia plugin

        // Mount app
        return app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
