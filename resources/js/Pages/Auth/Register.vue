<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    shop_name: '',
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

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-[#f5f0e8]">Create your account</h2>
            <p class="mt-2 text-sm text-gray-400">Start managing your barbershop in minutes</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Your name</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="John Smith"
                />
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <label for="shop_name" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Shop name</label>
                <input
                    id="shop_name"
                    type="text"
                    v-model="form.shop_name"
                    required
                    autocomplete="organization"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="Classic Cuts Barbershop"
                />
                <InputError class="mt-1.5" :message="form.errors.shop_name" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="you@barbershop.com"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Password</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="Min. 8 characters"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Confirm password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-700 bg-[#1a1a2e] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition"
                    placeholder="Confirm your password"
                />
                <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
            </div>

            <PrimaryButton
                class="w-full justify-center bg-[#D4A853] hover:bg-[#c09743] text-[#1a1a2e] font-semibold py-3 rounded-lg text-base focus:ring-[#D4A853] transition"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Create account
            </PrimaryButton>

            <p class="text-center text-sm text-gray-400">
                Already have an account?
                <Link :href="route('login')" class="text-[#D4A853] hover:text-[#f5f0e8] font-medium transition">
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
