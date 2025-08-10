<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, onMounted, computed } from "vue";
import { EyeIcon, EyeOffIcon, ClockIcon } from "lucide-vue-next";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
    captcha: "",
});

// Captcha state
const captcha = ref({
    num1: 0,
    num2: 0,
});

const captchaInput = ref("");
const captchaError = ref("");
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

// Rate limiting state
const rateLimit = ref({
    failedAttempts: 0,
    lockedUntil: null,
    isLocked: false
});

const remainingTime = ref(0);
const countdownInterval = ref(null);

// Rate limiting utility functions
const RATE_LIMIT_KEY = 'register_rate_limit';
const MAX_ATTEMPTS = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 10 : 3;
const LOCK_DURATION = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' ? 60 : 300; // 1 minute for dev, 5 minutes for production

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

// Computed properties
const isFormDisabled = computed(() => {
    return rateLimit.value.isLocked || form.processing;
});

const buttonText = computed(() => {
    if (form.processing) return "Mendaftar...";
    if (rateLimit.value.isLocked) return `Tunggu ${formatTime(remainingTime.value)}`;
    return "Register";
});

const submit = () => {
    if (!checkRateLimit()) {
        return;
    }

    if (!validateCaptcha()) {
        return;
    }

    form.transform((data) => ({
        ...data,
        terms: form.terms ? "on" : "",
        captcha: captchaInput.value,
    })).post(route("register"), {
        onSuccess: () => {
            // Reset rate limit on successful registration
            rateLimit.value.failedAttempts = 0;
            rateLimit.value.lockedUntil = null;
            rateLimit.value.isLocked = false;
            saveRateLimitToStorage();
        },
        onError: () => {
            form.reset("password", "password_confirmation");
            generateCaptcha(); // Generate captcha baru ketika register gagal
            captchaInput.value = ""; // Reset captcha input
            captchaError.value = ""; // Clear captcha error

            // Increment failed attempts
            incrementFailedAttempts();
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// Check auth status when component mounts
onMounted(() => {
    generateCaptcha();
    loadRateLimitFromStorage();
    checkRateLimit();

    if (rateLimit.value.isLocked) {
        startCountdown();
    }
});
</script>

<template>

    <Head title="Register" />

    <div class="overflow-hidden text-white bg-center bg-cover" style="
            background-image: url('/img/balangan.jpg');
            background-position: cover;
            background-size: cover;
        ">
        <AuthenticationCard>
            <template #logo>
                <div class="p-6 text-3xl font-bold text-center text-white" style="text-shadow: 1px 1px 2px #000">
                    Pendaftaran Akun SIBEDAH SERU
                </div>
                <div class="flex justify-center">
                    <AuthenticationCardLogo />
                </div>
            </template>

            <form @submit.prevent="submit" :class="{ 'opacity-50 pointer-events-none': rateLimit.isLocked }">
                <div>
                    <InputLabel for="name" value="Nama" style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        " />
                    <TextInput id="name" v-model="form.name" type="text" class="block mt-1 w-full" required autofocus
                        autocomplete="name" style="color: black; background-color: #f9fafb" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        " />
                    <TextInput id="email" v-model="form.email" type="email" class="block mt-1 w-full" required
                        autocomplete="username" style="color: black; background-color: #f9fafb" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        " />
                    <div class="relative">
                        <TextInput id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            class="block mt-1 w-full" required autocomplete="new-password"
                            style="color: black; background-color: #f9fafb" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 text-gray-500 -translate-y-1/2 hover:text-gray-700">
                            <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                            <EyeOffIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        " />
                    <div class="relative">
                        <TextInput id="password_confirmation" v-model="form.password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'" class="block mt-1 w-full" required
                            autocomplete="new-password" style="color: black; background-color: #f9fafb" />
                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute right-3 top-1/2 text-gray-500 -translate-y-1/2 hover:text-gray-700">
                            <EyeIcon v-if="!showPasswordConfirmation" class="w-5 h-5" />
                            <EyeOffIcon v-else class="w-5 h-5" />
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <!-- Captcha Section -->
                <div class="mt-4">
                    <div class="p-4 mb-2">
                        <p class="p-3 text-3xl font-semibold text-center text-blue-800 bg-white">
                            {{ captcha.num1 }} + {{ captcha.num2 }} =
                        </p>
                    </div>
                    <input type="number" v-model="captchaInput" required
                        class="px-4 py-3 w-full text-black rounded-lg border border-gray-200 transition-all outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
                        placeholder="Masukkan Captcha Hasil Penjumlahan" />
                    <p v-if="captchaError" class="mt-1 text-sm text-red-500">
                        {{ captchaError }}
                    </p>
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                    <InputLabel for="terms">
                        <div class="flex items-center">
                            <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                            <div class="ms-2">
                                I agree to the
                                <a target="_blank" :href="route('terms.show')"
                                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms
                                    of Service</a>
                                and
                                <a target="_blank" :href="route('policy.show')"
                                    class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy
                                    Policy</a>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.terms" />
                    </InputLabel>
                </div>

                <!-- Rate Limit Warning -->
                <div v-if="rateLimit.failedAttempts > 0 && !rateLimit.isLocked"
                    class="p-3 mt-4 text-sm text-orange-600 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex gap-2 items-center">
                        <ClockIcon class="w-4 h-4" />
                        <span>Percobaan pendaftaran gagal: {{ rateLimit.failedAttempts }}/{{ MAX_ATTEMPTS }}.</span>
                    </div>
                </div>

                <!-- Locked Message -->
                <div v-if="rateLimit.isLocked"
                    class="p-3 mt-4 text-sm text-red-600 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex gap-2 items-center">
                        <ClockIcon class="w-4 h-4" />
                        <span>Terlalu banyak percobaan pendaftaran gagal. Silakan tunggu {{ formatTime(remainingTime) }}
                            sebelum mencoba lagi.</span>
                    </div>
                </div>

                <div class="flex justify-end items-center mt-4">
                    <Link :href="route('login')"
                        class="text-sm text-gray-200 underline rounded-md hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sudah terdaftar ?
                    </Link>

                    <PrimaryButton class="ms-4" :class="{ 'opacity-25': isFormDisabled }" :disabled="isFormDisabled">
                        {{ buttonText }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </div>
</template>
