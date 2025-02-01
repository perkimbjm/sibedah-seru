<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import ActionSection from "@/Components/ActionSection.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route("other-browser-sessions.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title> Browser Sessions </template>

        <template #description>
            Manage and log out your active sessions on other browsers and devices.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                If necessary, you may log out of all of your other browser sessions across all of your devices. Some of
                your recent sessions are listed below; however, this list may not be exhaustive. If you feel your
                account has been compromised, you should also update your password.
            </div>

            <!-- Other Browser Sessions -->
            <div v-if="sessions && sessions.length > 0" class="mt-5 space-y-6">
                <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                    <!-- Tampilkan detail sesi -->
                </div>
            </div>

            <!-- Tampilkan pesan jika tidak ada sesi -->
            <div v-else class="mt-5 text-sm text-gray-600">
                No active sessions found.
            </div>

            <div class="flex items-center mt-5">
                <PrimaryButton @click="confirmLogout">
                    Log Out Other Browser Sessions
                </PrimaryButton>

                <ActionMessage :on="form.recentlySuccessful" class="ms-3">
                    Done.
                </ActionMessage>
            </div>

            <!-- Log Out Other Devices Confirmation Modal -->
            <DialogModal :show="confirmingLogout" @close="closeModal">
                <template #title> Log Out Other Browser Sessions </template>

                <template #content>
                    Please enter your password to confirm you would like to log out of your other browser sessions
                    across all of your devices.

                    <div class="mt-4">
                        <TextInput ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4"
                            placeholder="Password" autocomplete="current-password"
                            @keyup.enter="logoutOtherBrowserSessions" />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="logoutOtherBrowserSessions">
                        Log Out Other Browser Sessions
                    </PrimaryButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
