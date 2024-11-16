import { defineStore } from "pinia";
import { router, usePage } from "@inertiajs/vue3";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        isLoaded: false,
    }),

    getters: {
        // Menggunakan Inertia page props untuk mendapatkan status auth
        isAuthenticated: () => {
            const page = usePage();
            return page.props.auth?.user != null;
        },

        // Getter untuk mendapatkan user data
        user: () => {
            const page = usePage();
            return page.props.auth?.user;
        },
    },

    actions: {
        initAuth() {
            // Hanya set isLoaded karena data auth sudah ada di Inertia page props
            this.isLoaded = true;
        },

        logout() {
            router.post(
                "/logout",
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        // Inertia akan otomatis memperbarui page props
                        router.visit("/login");
                    },
                }
            );
        },
    },
});
