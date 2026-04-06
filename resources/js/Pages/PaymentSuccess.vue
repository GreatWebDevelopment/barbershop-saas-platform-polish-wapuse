<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BarberPole from '@/Components/BarberPole.vue';

defineProps({
    plan: String,
});
</script>

<template>
    <Head title="Payment Successful" />

    <div class="min-h-screen bg-[#0f0f1a] flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="flex justify-center mb-6">
                <BarberPole size="lg" />
            </div>

            <div class="rounded-2xl bg-[#1a1a2e] border border-white/10 p-8">
                <div class="w-16 h-16 rounded-full bg-green-500/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">Welcome to BarberPro!</h1>
                <p class="text-gray-400 mb-6">
                    Your <span class="text-[#D4A853] font-semibold capitalize">{{ plan }}</span> plan is now active.
                    You're all set to start managing your barbershop.
                </p>

                <Link
                    :href="route('dashboard')"
                    class="inline-block w-full py-3 bg-[#D4A853] text-[#0f0f1a] rounded-xl font-semibold hover:bg-[#c09743] transition"
                >
                    Go to Dashboard
                </Link>

                <p class="text-xs text-gray-500 mt-4">
                    Need help getting started? Check out our
                    <Link :href="route('features')" class="text-[#D4A853] hover:underline">feature guide</Link>.
                </p>
            </div>
        </div>
    </div>
</template>
