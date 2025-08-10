<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ChevronLeftIcon } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

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

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Reset Password" />
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-r from-green-100 to-emerald-100">
        <div class="flex overflow-hidden w-full max-w-6xl bg-white rounded-2xl shadow-xl dark:bg-gray-900">
            <!-- Left Side - Reset Password Form -->
            <div class="px-8 py-12 w-full lg:w-1/2 sm:px-12">
                <div class="flex gap-2 items-center mb-3">
                    <button class="flex justify-center items-center w-10 h-10" @click="handleBack">
                        <ChevronLeftIcon class="w-8 h-8" />
                    </button>
                    <div class="flex justify-center items-center w-60">
                        <AuthenticationCardLogo />
                    </div>
                </div>

                <h1 class="mb-2 text-3xl font-bold">Reset Password</h1>
                <p class="mb-8 text-gray-600">Masukkan password baru untuk akun Anda</p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" v-model="form.email" type="email" required autocomplete="username"
                            placeholder="Masukkan Email Anda" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password Baru" />
                        <TextInput id="password" v-model="form.password" type="password" required
                            autocomplete="new-password" placeholder="Masukkan Password Baru" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                            required autocomplete="new-password" placeholder="Konfirmasi Password Baru" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <button :class="{ 'opacity-50': form.processing }" :disabled="form.processing"
                        class="px-4 py-3 w-full font-semibold text-white bg-emerald-500 rounded-lg transition-colors duration-300 hover:bg-emerald-600 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Mereset Password...' : 'Reset Password' }}
                    </button>
                </form>

                <p class="mt-8 text-sm text-center text-gray-600">
                    Ingat password Anda?
                    <Link :href="route('login')" class="font-medium text-emerald-600 hover:text-emerald-700">Login
                    Sekarang</Link>
                </p>
            </div>
            <!-- End Left Side -->

            <!-- Right Side - Illustration -->
            <div class="hidden relative p-12 w-1/2 bg-gray-300 sm:block dark:bg-gray-600">
                <div class="flex flex-col justify-center items-center h-full text-center">
                    <img src="/img/tugu.svg" alt="Reset Password Illustration" class="mb-3" />
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
