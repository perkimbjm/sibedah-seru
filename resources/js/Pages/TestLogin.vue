<script setup>
import { ref, onMounted } from "vue";
import { EyeIcon, EyeOffIcon, WalletIcon } from "lucide-vue-next";

const email = ref("");
const password = ref("");
const showPassword = ref(false);
const rememberMe = ref(false);
const captchaInput = ref("");
const captchaError = ref("");

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
        captchaError.value = "Incorrect captcha result. Please try again.";
        generateCaptcha(); // Generate new captcha on incorrect attempt
        return false;
    }
    return true;
};

const handleSubmit = () => {
    if (!validateCaptcha()) {
        return;
    }

    // Handle login logic here
    console.log({
        email: email.value,
        password: password.value,
        rememberMe: rememberMe.value,
        captchaValid: true,
    });
};

// Generate initial captcha on component mount
onMounted(() => {
    generateCaptcha();
});
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-r from-green-100 to-emerald-100"
    >
        <div
            class="w-full max-w-6xl bg-white rounded-2xl shadow-xl flex overflow-hidden"
        >
            <!-- Left Side - Login Form -->
            <div class="w-full lg:w-1/2 px-8 py-12 sm:px-12">
                <div class="flex items-center gap-2 mb-12">
                    <div class="w-10 h-10 flex items-center justify-center">
                        <img src="/img/logobalangan.webp" alt="Balangan" />
                    </div>
                    <span class="text-xl font-semibold">SIBEDAH SERU</span>
                </div>

                <h1 class="text-3xl font-bold mb-2">Welcome Back !</h1>
                <p class="text-gray-600 mb-8">Silahkan Login Kembali</p>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Email</label
                        >
                        <input
                            type="email"
                            v-model="email"
                            required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                            placeholder="Enter your email"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Password</label
                        >
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="password"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                placeholder="Enter your password"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                                <EyeOffIcon v-else class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Captcha Section -->
                    <div>
                        <div class="p-4 mb-2">
                            <p
                                class="text-blue-800 text-4xl font-semibold text-center"
                            >
                                {{ captcha.num1 }} + {{ captcha.num2 }} =
                            </p>
                        </div>
                        <input
                            type="number"
                            v-model="captchaInput"
                            required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                            placeholder="Masukkan Captcha Hasil Penjumlahan"
                        />
                        <p
                            v-if="captchaError"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ captchaError }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="rememberMe"
                                class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500"
                            />
                            <span class="text-sm text-gray-600"
                                >Remember Me</span
                            >
                        </label>
                        <a
                            href="#"
                            class="text-sm text-emerald-600 hover:text-emerald-700"
                            >Lupa Password?</a
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition-colors"
                    >
                        Login
                    </button>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500"
                                >or continue</span
                            >
                        </div>
                    </div>

                    <button
                        type="button"
                        class="w-full flex items-center justify-center gap-3 border border-gray-200 hover:bg-gray-50 py-3 rounded-lg transition-colors"
                    >
                        <img src="/google.svg" alt="Google" class="w-5 h-5" />
                        Log in with Google
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-600">
                    Belum memiliki Akun?
                    <a
                        href="/register"
                        class="text-emerald-600 hover:text-emerald-700 font-medium"
                        >Daftar Sekarang</a
                    >
                </p>
            </div>

            <!-- Right Side - Illustration -->
            <div class="hidden lg:block w-1/2 bg-gray-300 p-12 relative">
                <div
                    class="h-full flex flex-col items-center justify-center text-center"
                >
                    <img
                        src="/img/login.png"
                        alt="Login Illustration"
                        class="mb-8 w-64"
                    />
                    <h2 class="text-3xl font-bold text-white mb-4">
                        SIBEDAH SERU
                    </h2>
                    <p class="text-gray-400">
                        Sistem Informasi<br />
                        Bedah Seribu Rumah
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
