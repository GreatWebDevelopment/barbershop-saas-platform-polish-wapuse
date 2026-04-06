<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BarberPole from '@/Components/BarberPole.vue';

const team = [
    { name: 'Marcus Johnson', role: 'Founder & CEO', bio: 'Former barber turned tech entrepreneur. Built BarberPro after struggling with outdated tools running his own 3-location shop.', image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face' },
    { name: 'Sarah Chen', role: 'Head of Product', bio: '10 years in SaaS product management. Obsessed with making complex workflows feel effortless.', image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=300&h=300&fit=crop&crop=face' },
    { name: 'David Rivera', role: 'CTO', bio: 'Full-stack engineer who built scalable platforms at Stripe and Square before joining BarberPro.', image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&h=300&fit=crop&crop=face' },
    { name: 'Aisha Williams', role: 'Head of Customer Success', bio: 'Managed 500+ client relationships in hospitality tech. Knows what shop owners need before they ask.', image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&h=300&fit=crop&crop=face' },
];

const stats = [
    { value: '2,000+', label: 'Active barbershops' },
    { value: '1.2M', label: 'Appointments booked' },
    { value: '15K+', label: 'Barbers using BarberPro' },
    { value: '$48M+', label: 'Revenue processed' },
];
</script>

<template>
    <Head title="About" />

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
        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">Built by barbers, for barbers</h1>
                    <p class="text-lg text-gray-400 mb-6">BarberPro started in 2020 when our founder — a barber with three shops in Austin — got fed up with juggling spreadsheets, paper schedules, and five different apps just to run his business.</p>
                    <p class="text-lg text-gray-400">So he built something better. Today, BarberPro powers over 2,000 barbershops across the country, helping owners spend less time on admin and more time doing what they love — cutting hair.</p>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=600&q=80" alt="Barbershop" class="rounded-2xl shadow-2xl shadow-black/50 w-full aspect-[4/3] object-cover" />
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-[#1a1a2e]/50">
            <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div v-for="stat in stats" :key="stat.label">
                    <div class="text-3xl font-bold text-[#D4A853]">{{ stat.value }}</div>
                    <div class="text-sm text-gray-400 mt-1">{{ stat.label }}</div>
                </div>
            </div>
        </section>

        <!-- Mission -->
        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-white mb-6">Our mission</h2>
                <p class="text-xl text-gray-400 leading-relaxed">To give every barbershop — from the one-chair shop to the 20-location franchise — the same powerful tools that billion-dollar companies use. Simple enough for day one, powerful enough for year ten.</p>
            </div>
        </section>

        <!-- Team -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#1a1a2e]/50">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-white text-center mb-12">Meet the team</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="person in team" :key="person.name" class="text-center">
                        <img :src="person.image" :alt="person.name" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
                        <h3 class="text-lg font-semibold text-white">{{ person.name }}</h3>
                        <p class="text-sm text-[#D4A853] mb-2">{{ person.role }}</p>
                        <p class="text-sm text-gray-400">{{ person.bio }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Join the BarberPro family</h2>
                <p class="text-lg text-gray-400 mb-8">Start your free trial today and see why thousands of barbers trust us with their business.</p>
                <Link :href="route('register')" class="inline-flex items-center gap-2 bg-[#D4A853] text-[#0f0f1a] px-8 py-4 rounded-xl font-bold text-lg hover:bg-[#c09743] transition">
                    Get started free
                </Link>
            </div>
        </section>
    </div>
</template>
