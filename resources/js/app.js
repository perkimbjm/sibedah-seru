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
        // Inisialisasi aplikasi utama untuk Inertia
        const app = createApp({ render: () => h(App, props) });

        // Inisialisasi Pinia untuk state management
        const pinia = createPinia();

        // Fungsi untuk memuat AOS (Animate on Scroll)
        const loadAOS = async () => {
            const { default: AOS } = await import("aos");
            await import("aos/dist/aos.css");
            AOS.init();
        };

        // Tambahkan event listener untuk memuat AOS setelah halaman selesai dimuat
        window.addEventListener("load", loadAOS);

        // Daftarkan semua plugin
        app.use(plugin).use(ZiggyVue).use(pinia);

        // Mount aplikasi utama ke elemen root
        return app.mount(el);
    },
    progress: {
        color: "#4B5563", // Warna progress bar
    },
});
