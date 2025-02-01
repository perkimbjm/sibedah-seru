<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { useAuthStore } from "@/stores/auth";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, onMounted } from "vue";
import { EyeIcon, EyeOffIcon, ChevronLeftIcon } from "lucide-vue-next";

// Mendefinisikan properti yang diperlukan
defineProps({
    canResetPassword: Boolean,
    status: String,
});

// Initialize auth store
const authStore = useAuthStore();

// Menginisialisasi form menggunakan useForm dari Inertia
const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const emailInput = ref(null);
const showPassword = ref(false);
const captchaInput = ref("");
const captchaError = ref("");

// Fungsi handleBack yang baru
const handleBack = () => {
    if (window.history.length > 1) {
        router.visit(document.referrer || "/", {
            method: "get",
            preserveScroll: true,
        });
    } else {
        router.visit("/");
    }
};

// Fungsi untuk mengirim data form
const submit = () => {
    if (!validateCaptcha()) {
        return;
    }
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onSuccess: (response) => {
            console.log(response); // Periksa respons
            if (response.data && response.data.token) {
                localStorage.setItem('token', response.data.token);
                window.location.assign(route("dashboard"));
            } else {
                alert('Login failed: Token not found.');
            }

        },
        onError: () => {
            form.reset("password");
        },
    });
};

// Captcha state
const captcha = ref({
    num1: 0,
    num2: 0,
});

// Generate random numbers for captcha
const generateCaptcha = () => {
    captcha.value = {
        num1: Math.floor(Math.random() * 50) + 1, // Random number between 1-50
        num2: Math.floor(Math.random() * 50) + 1,
    };
    captchaInput.value = ""; // Reset input when generating new captcha
    captchaError.value = ""; // Clear any previous errors
};

// Validate captcha
const validateCaptcha = () => {
    const sum = captcha.value.num1 + captcha.value.num2;
    if (parseInt(captchaInput.value) !== sum) {
        captchaError.value = "Captcha Salah Penjumlahan. Maaf Coba Lagi.";
        generateCaptcha();
        return false;
    }
    return true;
};

const handleGoogleLogin = () => {
    window.location.href = route("auth.google");
};

// Check auth status when component mounts
onMounted(() => {
    generateCaptcha();
    if (!authStore.isLoaded) {
        authStore.initAuth();
    }

    setTimeout(() => {
        if (emailInput.value) {
            emailInput.value.focus();
        }
    }, 100);
});
</script>

<template>

    <Head title="Log in" />
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-green-100 to-emerald-100">
        <div class="w-full max-w-6xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl flex overflow-hidden">
            <!-- Left Side - Login Form -->
            <div class="w-full lg:w-1/2 px-8 py-12 sm:px-12">
                <div class="flex items-center gap-2 mb-3">
                    <button class="w-10 h-10 flex items-center justify-center" @click="handleBack">
                        <ChevronLeftIcon class="w-8 h-8" />
                    </button>
                    <div class="w-60 flex items-center justify-center">
                        <AuthenticationCardLogo />
                    </div>
                </div>

                <h1 class="text-3xl font-bold mb-2">Welcome Back !</h1>
                <p class="text-gray-600 mb-8">Silahkan Login Kembali</p>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" ref="emailInput" v-model="form.email" type="email" required
                            autocomplete="email" placeholder="Masukkan Email kamu" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Section -->
                    <div>
                        <InputLabel for="password" value="Password">Password</InputLabel>
                        <div class="relative">
                            <TextInput id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                required autocomplete="current-password" placeholder="Masukkan kata sandi" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                                <EyeOffIcon v-else class="w-5 h-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Captcha Section -->
                    <div>
                        <div class="p-4 mb-2">
                            <p class="text-blue-800 text-3xl font-semibold text-center">
                                {{ captcha.num1 }} + {{ captcha.num2 }} =
                            </p>
                        </div>
                        <input type="number" v-model="captchaInput" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                            placeholder="Masukkan Captcha Hasil Penjumlahan" />
                        <p v-if="captchaError" class="mt-1 text-sm text-red-500">
                            {{ captchaError }}
                        </p>
                    </div>

                    <!-- Remember me and Forgot Password-->
                    <div class="flex items-center justify-between mt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <Checkbox v-model:checked="form.remember" name="remember" />
                            <span class="ms-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-emerald-600 hover:text-emerald-700 transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Lupa Password ?
                        </Link>
                    </div>

                    <button :class="{ 'opacity-50': form.processing }" :disabled="form.processing"
                        class="w-full px-4 py-3 text-white bg-emerald-500 hover:bg-emerald-600 font-semibold rounded-lg transition-colors duration-300">
                        {{ form.processing ? "Logging in..." : "Log in" }}
                    </button>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">or continue</span>
                        </div>
                    </div>

                    <button type="button" @click="handleGoogleLogin"
                        class="w-full flex items-center justify-center gap-3 border border-gray-200 hover:bg-gray-50 py-3 rounded-lg transition-colors"
                        :disabled="form.processing">
                        <img src="/img/google.png" alt="Google" class="w-5 h-5" />
                        {{
                            form.processing
                                ? "Processing..."
                                : "Log in with Google"
                        }}
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-600">
                    Belum memiliki Akun?
                    <Link :href="route('register')" class="text-emerald-600 hover:text-emerald-700 font-medium">Daftar
                    Sekarang</Link>
                </p>
            </div>
            <!-- End Left Side -->

            <!-- Right Side - Illustration -->
            <div class="hidden sm:block w-1/2 bg-gray-300 dark:bg-gray-600 p-12 relative">
                <div class="h-full flex flex-col items-center justify-center text-center">
                    <img src="/img/tugu.svg" alt="Login Illustration" class="mb-3" />
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        SIBEDAH SERU
                    </h2>
                    <p class="text-gray-600">
                        Sistem Informasi<br />
                        Bedah Seribu Rumah
                    </p>
                </div>
            </div>
            <!--End Right Side-->
        </div>
    </div>
</template>
