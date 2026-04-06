<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-[#1a1a2e] pt-6 sm:justify-center sm:pt-0">
        <div class="text-center">
            <Link href="/">
                <ApplicationLogo class="h-16 w-auto" />
            </Link>
            <p class="mt-3 text-sm text-gray-400">The Modern Platform for Modern Barbershops</p>
        </div>

        <div class="mt-8 w-full overflow-hidden bg-[#16162a] px-8 py-8 shadow-lg border border-gray-800 sm:max-w-md sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
