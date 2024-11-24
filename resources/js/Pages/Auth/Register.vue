<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />

    <div
        class="bg-cover bg-center text-white overflow-hidden"
        style="
            background-image: url('/img/balangan.jpg');
            background-position: cover;
            background-size: cover;
        "
    >
        <AuthenticationCard>
            <template #logo>
                <div
                    class="text-3xl font-bold text-white text-center p-6"
                    style="text-shadow: 1px 1px 2px #000"
                >
                    Pendaftaran Akun SIBEDAH SERU
                </div>
                <div class="flex justify-center">
                    <AuthenticationCardLogo />
                </div>
            </template>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel
                        for="name"
                        value="Nama"
                        style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        "
                    />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                        style="color: black; background-color: #f9fafb"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="email"
                        value="Email"
                        style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        "
                    />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                        style="color: black; background-color: #f9fafb"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password"
                        value="Password"
                        style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        "
                    />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                        style="color: black; background-color: #f9fafb"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                        style="
                            color: white;
                            text-shadow: 1px 1px 2px #000;
                            font-weight: bold;
                        "
                    />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                        style="color: black; background-color: #f9fafb"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <div
                    v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                    class="mt-4"
                >
                    <InputLabel for="terms">
                        <div class="flex items-center">
                            <Checkbox
                                id="terms"
                                v-model:checked="form.terms"
                                name="terms"
                                required
                            />

                            <div class="ms-2">
                                I agree to the
                                <a
                                    target="_blank"
                                    :href="route('terms.show')"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >Terms of Service</a
                                >
                                and
                                <a
                                    target="_blank"
                                    :href="route('policy.show')"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >Privacy Policy</a
                                >
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.terms" />
                    </InputLabel>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        :href="route('login')"
                        class="underline text-sm text-gray-200 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Sudah terdaftar ?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </div>
</template>
