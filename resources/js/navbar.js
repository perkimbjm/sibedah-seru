import { createApp } from "vue";
import Navbar from "@/Pages/NavbarOnly.vue";

// Cari elemen dengan ID `vue-navbar` di halaman Blade
const vueNavbar = document.getElementById("vue-navbar");

// Jika elemen ditemukan, mount Vue
if (vueNavbar) {
    createApp(Navbar).mount("#vue-navbar");
}
