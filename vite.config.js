import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.js", "resources/css/app.css"],
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
        // react({
        //     jsxRuntime: 'automatic',
        // }),
    ],
    optimizeDeps: {
        include: ["vue3-particles", "leaflet"],
    },
    server: {
        host: "localhost",
        port: 5173, // port baru untuk Vite
        strictPort: true,
        hmr: {
            host: "localhost",
            port: 5173,
        },
    },
});
