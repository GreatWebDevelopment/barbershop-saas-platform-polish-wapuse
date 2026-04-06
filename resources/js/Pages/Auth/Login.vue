<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-[#f5f0e8]">Sign in to your account</h2>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400">
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

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="text-[#f5f0e8]" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-[#1a1a2e] border-gray-700 text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="border-gray-700 bg-[#1a1a2e] text-[#D4A853] focus:ring-[#D4A853]" />
                    <span class="ms-2 text-sm text-[#f5f0e8]">Remember me</span>
                </label>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-[#D4A853] hover:text-[#f5f0e8] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition duration-150 ease-in-out"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-auto bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold focus:ring-[#D4A853]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-gray-400">Don't have an account? </span>
                <Link
                    :href="route('register')"
                    class="text-sm text-[#D4A853] hover:text-[#f5f0e8] focus:outline-none focus:ring-2 focus:ring-[#D4A853] rounded-md transition duration-150 ease-in-out"
                >
                    Register
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Override default input styles for dark theme */
:deep(input[type="email"]),
:deep(input[type="password"]) {
    background-color: #1a1a2e;
    border-color: #374151;
    color: #f5f0e8;
}

:deep(input[type="email"]:focus),
:deep(input[type="password"]:focus) {
    border-color: #D4A853;
    ring-color: #D4A853;
}
</style>
