<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BarberPole from '@/Components/BarberPole.vue';

const features = [
    {
        title: 'Online Booking',
        description: 'Let clients book 24/7 from your website, Instagram, or Google. Automatic confirmations and reminders reduce no-shows by up to 60%.',
        icon: 'calendar',
        image: 'https://images.unsplash.com/photo-1585747860019-8729c0f98a8f?w=600&q=80',
        bullets: ['Custom booking page with your branding', 'Google Calendar & Apple Calendar sync', 'Automated SMS & email reminders', 'Walk-in queue management'],
    },
    {
        title: 'Staff Management',
        description: 'Track schedules, commissions, performance, and availability for your entire team. Each barber gets their own dashboard.',
        icon: 'users',
        image: 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=600&q=80',
        bullets: ['Individual barber schedules & time-off', 'Commission tracking & payroll reports', 'Performance leaderboards', 'Skill-based service assignments'],
    },
    {
        title: 'Point of Sale',
        description: 'Process payments, sell products, and manage tips — all from one screen. Works with card readers, Apple Pay, and cash.',
        icon: 'credit-card',
        image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80',
        bullets: ['Accept cards, cash, Apple Pay & Google Pay', 'Product inventory & retail sales', 'Automatic tip splitting', 'End-of-day reports & cash drawer tracking'],
    },
    {
        title: 'Customer CRM',
        description: 'Build lasting relationships with detailed client profiles. Track preferences, visit history, and spending to deliver personalized service.',
        icon: 'heart',
        image: 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600&q=80',
        bullets: ['Detailed client profiles with photos & notes', 'Visit history & preferred styles', 'Loyalty points & reward programs', 'Birthday & anniversary reminders'],
    },
    {
        title: 'Analytics & Reports',
        description: 'See exactly how your business is performing with real-time dashboards. Revenue, bookings, staff performance — all at a glance.',
        icon: 'chart',
        image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&q=80',
        bullets: ['Real-time revenue tracking', 'Service popularity & trends', 'Staff utilization rates', 'Custom report builder'],
    },
    {
        title: 'Multi-Location',
        description: 'Manage multiple shops from one account. Centralized reporting, shared client database, and location-specific settings.',
        icon: 'map',
        image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=80',
        bullets: ['Centralized dashboard for all locations', 'Transfer clients between shops', 'Location-specific pricing & staff', 'Consolidated financial reports'],
    },
];
</script>

<template>
    <Head title="Features" />

    <div class="min-h-screen bg-[#0f0f1a]">
        <!-- Nav -->
        <nav class="border-b border-white/5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
                <Link href="/" class="flex items-center gap-2">
                    <BarberPole size="md" />
                    <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                </Link>
                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="text-sm text-gray-400 hover:text-white transition">Sign in</Link>
                    <Link :href="route('register')" class="text-sm bg-[#D4A853] text-[#0f0f1a] px-4 py-2 rounded-lg font-semibold hover:bg-[#c09743] transition">Start Free</Link>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">Everything you need to run your shop</h1>
                <p class="text-xl text-gray-400 mb-8">From booking to payroll, BarberPro replaces a dozen tools with one simple platform built specifically for barbershops.</p>
                <Link :href="route('register')" class="inline-flex items-center gap-2 bg-[#D4A853] text-[#0f0f1a] px-8 py-4 rounded-xl font-bold text-lg hover:bg-[#c09743] transition">
                    Start your free trial
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </Link>
            </div>
        </section>

        <!-- Feature Sections -->
        <section class="pb-20">
            <div
                v-for="(feature, i) in features"
                :key="feature.title"
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
            >
                <div class="grid lg:grid-cols-2 gap-12 items-center" :class="i % 2 === 1 ? 'lg:grid-flow-col-dense' : ''">
                    <div :class="i % 2 === 1 ? 'lg:col-start-2' : ''">
                        <div class="inline-flex items-center gap-2 bg-[#D4A853]/10 text-[#D4A853] px-3 py-1 rounded-full text-sm font-medium mb-4">
                            {{ feature.title }}
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-4">{{ feature.title }}</h2>
                        <p class="text-gray-400 text-lg mb-6">{{ feature.description }}</p>
                        <ul class="space-y-3">
                            <li v-for="bullet in feature.bullets" :key="bullet" class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#D4A853] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span class="text-gray-300">{{ bullet }}</span>
                            </li>
                        </ul>
                    </div>
                    <div :class="i % 2 === 1 ? 'lg:col-start-1' : ''">
                        <img :src="feature.image" :alt="feature.title" class="rounded-2xl shadow-2xl shadow-black/50 w-full aspect-[4/3] object-cover" />
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#0f0f1a] to-[#1a1a2e]">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Ready to upgrade your shop?</h2>
                <p class="text-xl text-gray-400 mb-8">Join 2,000+ barbershops already using BarberPro. Free 14-day trial, no credit card required.</p>
                <Link :href="route('register')" class="inline-flex items-center gap-2 bg-[#D4A853] text-[#0f0f1a] px-8 py-4 rounded-xl font-bold text-lg hover:bg-[#c09743] transition">
                    Get started free
                </Link>
            </div>
        </section>
    </div>
</template>
