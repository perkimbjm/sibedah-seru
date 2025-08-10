<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { useAuthStore } from "@/stores/auth";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, onMounted, computed } from "vue";
import { EyeIcon, EyeOffIcon, ChevronLeftIcon, ClockIcon } from "lucide-vue-next";

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

// Rate limiting state
const rateLimit = ref({
    failedAttempts: 0,
    lockedUntil: null,
    isLocked: false
});

const remainingTime = ref(0);
const countdownInterval = ref(null);

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

// Rate limiting utility functions
const RATE_LIMIT_KEY = 'login_rate_limit';
const MAX_ATTEMPTS = 5;
const LOCK_DURATION = 90; // seconds

const loadRateLimitFromStorage = () => {
    const stored = localStorage.getItem(RATE_LIMIT_KEY);
    if (stored) {
        const data = JSON.parse(stored);
        rateLimit.value = {
            failedAttempts: data.failedAttempts || 0,
            lockedUntil: data.lockedUntil ? new Date(data.lockedUntil) : null,
            isLocked: data.isLocked || false
        };
    }
};

const saveRateLimitToStorage = () => {
    localStorage.setItem(RATE_LIMIT_KEY, JSON.stringify({
        failedAttempts: rateLimit.value.failedAttempts,
        lockedUntil: rateLimit.value.lockedUntil,
        isLocked: rateLimit.value.isLocked
    }));
};

const checkRateLimit = () => {
    if (rateLimit.value.lockedUntil && new Date() < rateLimit.value.lockedUntil) {
        rateLimit.value.isLocked = true;
        return false;
    } else if (rateLimit.value.lockedUntil && new Date() >= rateLimit.value.lockedUntil) {
        // Reset rate limit after lock period
        rateLimit.value.failedAttempts = 0;
        rateLimit.value.lockedUntil = null;
        rateLimit.value.isLocked = false;
        saveRateLimitToStorage();
        return true;
    }
    return true;
};

const incrementFailedAttempts = () => {
    rateLimit.value.failedAttempts++;

    if (rateLimit.value.failedAttempts >= MAX_ATTEMPTS) {
        rateLimit.value.isLocked = true;
        rateLimit.value.lockedUntil = new Date(Date.now() + (LOCK_DURATION * 1000));
        startCountdown();
    }

    saveRateLimitToStorage();
};

const startCountdown = () => {
    if (countdownInterval.value) {
        clearInterval(countdownInterval.value);
    }

    countdownInterval.value = setInterval(() => {
        if (rateLimit.value.lockedUntil) {
            const now = new Date();
            const timeLeft = Math.ceil((rateLimit.value.lockedUntil - now) / 1000);

            if (timeLeft <= 0) {
                clearInterval(countdownInterval.value);
                rateLimit.value.failedAttempts = 0;
                rateLimit.value.lockedUntil = null;
                rateLimit.value.isLocked = false;
                remainingTime.value = 0;
                saveRateLimitToStorage();
            } else {
                remainingTime.value = timeLeft;
            }
        }
    }, 1000);
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// Computed properties
const isFormDisabled = computed(() => {
    return rateLimit.value.isLocked || form.processing;
});

const buttonText = computed(() => {
    if (form.processing) return "Logging in...";
    if (rateLimit.value.isLocked) return `Tunggu ${formatTime(remainingTime.value)}`;
    return "Log in";
});

// Fungsi untuk mengirim data form
const submit = () => {
    if (!checkRateLimit()) {
        return;
    }

    if (!validateCaptcha()) {
        return;
    }

    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onSuccess: (response) => {
            // Reset rate limit on successful login
            rateLimit.value.failedAttempts = 0;
            rateLimit.value.lockedUntil = null;
            rateLimit.value.isLocked = false;
            saveRateLimitToStorage();

            // Periksa apakah user sudah terautentikasi melalui store
            if (authStore.isAuthenticated) {
                // Redirect ke dashboard langsung karena session sudah valid
                window.location.assign(route("dashboard"));
            } else {
                alert('Login failed: Please check your credentials.');
            }
        },
        onError: () => {
            form.reset("password");
            generateCaptcha(); // Generate captcha baru ketika login gagal
            captchaInput.value = ""; // Reset captcha input
            captchaError.value = ""; // Clear captcha error

            // Increment failed attempts
            incrementFailedAttempts();
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
    loadRateLimitFromStorage();
    checkRateLimit();

    if (rateLimit.value.isLocked) {
        startCountdown();
    }

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
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-r from-green-100 to-emerald-100">
        <div class="flex overflow-hidden w-full max-w-6xl bg-white rounded-2xl shadow-xl dark:bg-gray-900">
            <!-- Left Side - Login Form -->
            <div class="px-8 py-12 w-full lg:w-1/2 sm:px-12">
                <div class="flex gap-2 items-center mb-3">
                    <button class="flex justify-center items-center w-10 h-10" @click="handleBack">
                        <ChevronLeftIcon class="w-8 h-8" />
                    </button>
                    <div class="flex justify-center items-center w-60">
                        <AuthenticationCardLogo />
                    </div>
                </div>

                <h1 class="mb-2 text-3xl font-bold">Welcome Back !</h1>
                <p class="mb-8 text-gray-600">Silahkan Login Kembali</p>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6"
                    :class="{ 'opacity-50 pointer-events-none': rateLimit.isLocked }">
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
                                class="absolute right-3 top-1/2 text-gray-500 -translate-y-1/2 hover:text-gray-700">
                                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                                <EyeOffIcon v-else class="w-5 h-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Captcha Section -->
                    <div>
                        <div class="p-4 mb-2">
                            <p class="text-3xl font-semibold text-center text-blue-800">
                                {{ captcha.num1 }} + {{ captcha.num2 }} =
                            </p>
                        </div>
                        <input type="number" v-model="captchaInput" required
                            class="px-4 py-3 w-full rounded-lg border border-gray-200 transition-all outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
                            placeholder="Masukkan Captcha Hasil Penjumlahan" />
                        <p v-if="captchaError" class="mt-1 text-sm text-red-500">
                            {{ captchaError }}
                        </p>
                    </div>

                    <!-- Remember me and Forgot Password-->
                    <div class="flex justify-between items-center mt-4">
                        <label class="flex gap-2 items-center cursor-pointer">
                            <Checkbox v-model:checked="form.remember" name="remember" />
                            <span class="text-sm text-gray-600 ms-2">Remember me</span>
                        </label>

                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-emerald-600 transition duration-150 ease-in-out hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Lupa Password ?
                        </Link>
                    </div>

                    <!-- Rate Limit Warning -->
                    <div v-if="rateLimit.failedAttempts > 0 && !rateLimit.isLocked"
                        class="p-3 text-sm text-orange-600 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex gap-2 items-center">
                            <ClockIcon class="w-4 h-4" />
                            <span>Percobaan login gagal: {{ rateLimit.failedAttempts }}/{{ MAX_ATTEMPTS }}.</span>
                        </div>
                    </div>

                    <!-- Locked Message -->
                    <div v-if="rateLimit.isLocked"
                        class="p-3 text-sm text-red-600 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex gap-2 items-center">
                            <ClockIcon class="w-4 h-4" />
                            <span>Terlalu banyak percobaan login gagal. Silakan tunggu {{ formatTime(remainingTime) }}
                                sebelum mencoba lagi.</span>
                        </div>
                    </div>

                    <button :class="{ 'opacity-50': isFormDisabled }" :disabled="isFormDisabled"
                        class="px-4 py-3 w-full font-semibold text-white bg-emerald-500 rounded-lg transition-colors duration-300 hover:bg-emerald-600 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        {{ buttonText }}
                    </button>

                    <div class="relative">
                        <div class="flex absolute inset-0 items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="flex relative justify-center text-sm">
                            <span class="px-2 text-gray-500 bg-white">or continue</span>
                        </div>
                    </div>

                    <button type="button" @click="handleGoogleLogin"
                        class="flex gap-3 justify-center items-center py-3 w-full rounded-lg border border-gray-200 transition-colors hover:bg-gray-50"
                        :disabled="isFormDisabled">
                        <img src="/img/google.png" alt="Google" class="w-5 h-5" />
                        {{
                            isFormDisabled
                                ? "Tidak tersedia"
                                : "Log in with Google"
                        }}
                    </button>
                </form>

                <p class="mt-8 text-sm text-center text-gray-600">
                    Belum memiliki Akun?
                    <Link :href="route('register')" class="font-medium text-emerald-600 hover:text-emerald-700">Daftar
                    Sekarang</Link>
                </p>
            </div>
            <!-- End Left Side -->

            <!-- Right Side - Illustration -->
            <div class="hidden relative p-12 w-1/2 bg-gray-300 sm:block dark:bg-gray-600">
                <div class="flex flex-col justify-center items-center h-full text-center">
                    <img src="/img/tugu.svg" alt="Login Illustration" class="mb-3" />
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">
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
