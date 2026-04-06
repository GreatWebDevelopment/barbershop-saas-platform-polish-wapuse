<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-[#f5f0e8]">Create your account</h2>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" class="text-[#f5f0e8]" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-[#1a1a2e] border-gray-700 text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" class="text-[#f5f0e8]" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-[#1a1a2e] border-gray-700 text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2 text-red-400" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-[#f5f0e8]"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-[#1a1a2e] border-gray-700 text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2 text-red-400"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6 flex items-center justify-end">
                <PrimaryButton
                    class="w-full justify-center bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold focus:ring-[#D4A853]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-gray-400">Already have an account? </span>
                <Link
                    :href="route('login')"
                    class="text-sm text-[#D4A853] hover:text-[#f5f0e8] focus:outline-none focus:ring-2 focus:ring-[#D4A853] rounded-md transition duration-150 ease-in-out"
                >
                    Sign in
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Override default input styles for dark theme */
:deep(input[type="text"]),
:deep(input[type="email"]),
:deep(input[type="password"]) {
    background-color: #1a1a2e;
    border-color: #374151;
    color: #f5f0e8;
}

:deep(input[type="text"]:focus),
:deep(input[type="email"]:focus),
:deep(input[type="password"]:focus) {
    border-color: #D4A853;
    ring-color: #D4A853;
}
</style>
