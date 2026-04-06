<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Left side - Image panel -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <img
                src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=1200&q=80"
                alt="Barbershop"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-[#1a1a2e]/90 via-[#1a1a2e]/60 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-between p-12">
                <Link href="/">
                    <ApplicationLogo class="h-10 w-auto" />
                </Link>
                <div class="max-w-md">
                    <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                        Run your barbershop like a pro
                    </h1>
                    <p class="text-lg text-white/80">
                        Scheduling, payments, staff management, and customer loyalty — all in one platform built for modern barbershops.
                    </p>
                    <div class="flex items-center gap-6 mt-8">
                        <div>
                            <div class="text-2xl font-bold text-[#D4A853]">2,000+</div>
                            <div class="text-sm text-white/60">Active shops</div>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <div class="text-2xl font-bold text-[#D4A853]">1.2M</div>
                            <div class="text-sm text-white/60">Appointments</div>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div>
                            <div class="text-2xl font-bold text-[#D4A853]">4.9/5</div>
                            <div class="text-sm text-white/60">Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Form panel -->
        <div class="flex flex-1 flex-col items-center justify-center bg-[#16162a] px-6 py-12 lg:px-12">
            <div class="w-full max-w-sm">
                <div class="lg:hidden text-center mb-8">
                    <Link href="/">
                        <ApplicationLogo class="h-12 w-auto mx-auto" />
                    </Link>
                    <p class="mt-3 text-sm text-gray-400">The Modern Platform for Modern Barbershops</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
