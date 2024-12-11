<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useAuthStore } from "@/stores/auth";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { onMounted } from "vue";

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

// Fungsi untuk mengirim data form
const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onSuccess: (response) => {
            // Update auth store setelah login berhasil
            authStore.handleLoginSuccess(response?.props?.auth?.user);
            form.reset("password");
        },
        onError: () => {
            form.reset("password");
        },
    });
};

// Check auth status when component mounts
onMounted(() => {
    if (!authStore.isLoaded) {
        authStore.initAuth();
    }
});
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>
            </div>

            <div class="flex items-center justify-center">
                <button
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    class="w-full px-4 py-3 text-white bg-emerald-500 hover:bg-emerald-600 font-semibold rounded-lg transition-colors duration-300"
                >
                    {{ form.processing ? "Logging in..." : "Log in" }}
                </button>
            </div>

            <div class="text-center mt-4">
                <Link
                    :href="route('register')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Belum terdaftar? Daftar sekarang.
                </Link>
            </div>
        </form>
    </AuthenticationCard>
</template>
