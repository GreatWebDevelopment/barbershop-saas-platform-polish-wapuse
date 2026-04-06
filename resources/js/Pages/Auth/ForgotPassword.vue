<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-[#f5f0e8]">Forgot your password?</h2>
        </div>

        <div class="mb-4 text-sm text-gray-400">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-400"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="text-[#f5f0e8]" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-[#1a1a2e] border-gray-700 text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.email" />
            </div>

            <div class="mt-6 flex items-center justify-end">
                <PrimaryButton
                    class="w-full justify-center bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold focus:ring-[#D4A853]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Override default input styles for dark theme */
:deep(input[type="email"]) {
    background-color: #1a1a2e;
    border-color: #374151;
    color: #f5f0e8;
}

:deep(input[type="email"]:focus) {
    border-color: #D4A853;
    ring-color: #D4A853;
}
</style>
