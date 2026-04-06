<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[#f5f0e8]">Welcome back</h2>
            <p class="mt-2 text-sm text-gray-400">Sign in to manage your shop</p>
        </div>

        <div v-if="status" class="mb-4 rounded-lg bg-green-500/10 border border-green-500/20 px-4 py-3 text-sm text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="you@barbershop.com"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-[#f5f0e8]">Password</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-[#D4A853] hover:text-[#f5f0e8] transition"
                    >
                        Forgot password?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="Enter your password"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <Checkbox name="remember" v-model:checked="form.remember" class="border-gray-700 bg-[#1a1a2e] text-[#D4A853] focus:ring-[#D4A853] rounded" />
                <span class="text-sm text-gray-400">Remember me</span>
            </label>

            <PrimaryButton
                class="w-full justify-center bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold py-3 rounded-lg text-base focus:ring-[#D4A853] transition"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Sign in
            </PrimaryButton>

            <p class="text-center text-sm text-gray-400">
                Don't have an account?
                <Link :href="route('register')" class="text-[#D4A853] hover:text-[#f5f0e8] font-medium transition">
                    Get started free
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
