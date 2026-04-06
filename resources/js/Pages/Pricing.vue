<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import BarberPole from '@/Components/BarberPole.vue';

const annual = ref(false);

const plans = [
    {
        name: 'Starter',
        monthlyPrice: 49,
        annualPrice: 39,
        description: 'Perfect for single-chair shops getting started.',
        features: [
            '1 location',
            'Up to 3 staff members',
            'Online booking page',
            'Appointment management',
            'Basic customer profiles',
            'SMS reminders (100/mo)',
            'Email support',
        ],
        cta: 'Start free trial',
        highlighted: false,
    },
    {
        name: 'Professional',
        monthlyPrice: 99,
        annualPrice: 79,
        description: 'For growing shops that need more power.',
        features: [
            'Up to 3 locations',
            'Unlimited staff',
            'Everything in Starter, plus:',
            'Advanced analytics & reports',
            'Commission tracking & payroll',
            'Loyalty program & rewards',
            'SMS reminders (500/mo)',
            'Marketing tools & campaigns',
            'Priority support',
        ],
        cta: 'Start free trial',
        highlighted: true,
    },
    {
        name: 'Enterprise',
        monthlyPrice: 199,
        annualPrice: 159,
        description: 'For multi-location businesses and franchises.',
        features: [
            'Unlimited locations',
            'Unlimited staff',
            'Everything in Professional, plus:',
            'API access & custom integrations',
            'White-label branding',
            'Dedicated account manager',
            'Unlimited SMS reminders',
            'Custom reports & exports',
            'Phone & chat support',
            'SLA guarantee',
        ],
        cta: 'Contact sales',
        highlighted: false,
    },
];

const faqs = [
    { q: 'Can I try BarberPro for free?', a: 'Yes! Every plan comes with a free 14-day trial. No credit card required to get started.' },
    { q: 'Can I change plans later?', a: 'Absolutely. Upgrade or downgrade at any time. Changes take effect on your next billing cycle.' },
    { q: 'Is there a contract or commitment?', a: 'No contracts. All plans are month-to-month (or annual for a discount). Cancel anytime.' },
    { q: 'Do you offer a native mobile app?', a: 'Yes! Add a branded iOS and Android app for your shop for an additional $99/month. Your clients can book, pay, and manage their profile from their phone.' },
    { q: 'What payment methods do you accept?', a: 'We accept all major credit cards, debit cards, and ACH bank transfers for your subscription. For your clients, we support cards, Apple Pay, Google Pay, and cash.' },
    { q: 'Can I import my existing client data?', a: 'Yes. We offer free data migration from most popular barbershop software. Our team will handle the import for you.' },
];
</script>

<template>
    <Head title="Pricing" />

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

        <!-- Header -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Simple, transparent pricing</h1>
            <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">No hidden fees. No setup costs. Start free and upgrade when you're ready.</p>

            <!-- Toggle -->
            <div class="flex items-center justify-center gap-3">
                <span class="text-sm" :class="!annual ? 'text-white font-semibold' : 'text-gray-500'">Monthly</span>
                <button
                    @click="annual = !annual"
                    class="relative w-12 h-6 rounded-full transition-colors"
                    :class="annual ? 'bg-[#D4A853]' : 'bg-gray-700'"
                >
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform" :class="annual ? 'translate-x-6' : ''"></div>
                </button>
                <span class="text-sm" :class="annual ? 'text-white font-semibold' : 'text-gray-500'">Annual</span>
                <span v-if="annual" class="text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full font-medium">Save 20%</span>
            </div>
        </section>

        <!-- Plans -->
        <section class="pb-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
                <div
                    v-for="plan in plans"
                    :key="plan.name"
                    class="rounded-2xl p-8 flex flex-col"
                    :class="plan.highlighted
                        ? 'bg-gradient-to-b from-[#D4A853]/20 to-[#1a1a2e] border-2 border-[#D4A853] relative'
                        : 'bg-[#1a1a2e] border border-white/10'"
                >
                    <div v-if="plan.highlighted" class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#D4A853] text-[#0f0f1a] text-xs font-bold px-3 py-1 rounded-full">
                        Most Popular
                    </div>
                    <h3 class="text-xl font-bold text-white mb-1">{{ plan.name }}</h3>
                    <p class="text-sm text-gray-400 mb-6">{{ plan.description }}</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-white">${{ annual ? plan.annualPrice : plan.monthlyPrice }}</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <Link
                        :href="route('register')"
                        class="w-full text-center py-3 rounded-lg font-semibold transition mb-8"
                        :class="plan.highlighted
                            ? 'bg-[#D4A853] text-[#0f0f1a] hover:bg-[#c09743]'
                            : 'bg-white/10 text-white hover:bg-white/20'"
                    >
                        {{ plan.cta }}
                    </Link>
                    <ul class="space-y-3 flex-1">
                        <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-[#D4A853] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-sm text-gray-300">{{ feature }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#1a1a2e]/50">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-white text-center mb-12">Frequently asked questions</h2>
                <div class="space-y-6">
                    <div v-for="faq in faqs" :key="faq.q" class="border-b border-white/10 pb-6">
                        <h3 class="text-lg font-semibold text-white mb-2">{{ faq.q }}</h3>
                        <p class="text-gray-400">{{ faq.a }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
