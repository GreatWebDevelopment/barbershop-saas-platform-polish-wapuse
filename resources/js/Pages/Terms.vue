<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BarberPole from '@/Components/BarberPole.vue';
</script>

<template>
    <Head title="Terms of Service" />

    <div class="min-h-screen bg-[#0f0f1a]">
        <nav class="border-b border-white/5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
                <Link href="/" class="flex items-center gap-2">
                    <BarberPole size="md" />
                    <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                </Link>
            </div>
        </nav>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h1 class="text-4xl font-bold text-white mb-2">Terms of Service</h1>
            <p class="text-gray-500 mb-12">Last updated: January 1, 2026</p>

            <div class="space-y-8">
                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">1. Acceptance of Terms</h2>
                    <p class="text-gray-400 leading-relaxed">By accessing or using BarberPro, you agree to be bound by these Terms of Service. If you do not agree to all the terms and conditions, you may not access or use our services.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">2. Description of Service</h2>
                    <p class="text-gray-400 leading-relaxed">BarberPro provides a cloud-based business management platform for barbershops, including appointment scheduling, customer management, staff management, point of sale, and analytics tools.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">3. Account Registration</h2>
                    <p class="text-gray-400 leading-relaxed">You must provide accurate and complete information when creating an account. You are responsible for maintaining the security of your account credentials and for all activities that occur under your account.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">4. Subscription & Billing</h2>
                    <p class="text-gray-400 leading-relaxed">Paid plans are billed in advance on a monthly or annual basis. You may cancel your subscription at any time, and it will remain active until the end of the current billing period. We do not offer refunds for partial billing periods.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">5. Data Ownership</h2>
                    <p class="text-gray-400 leading-relaxed">You retain all ownership rights to your data. We do not claim any ownership over the content you upload or the data you create using our platform. You can export your data at any time.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">6. Acceptable Use</h2>
                    <p class="text-gray-400 leading-relaxed">You agree not to misuse our services. This includes attempting to access our systems through unauthorized means, interfering with other users' access to the service, or using the platform for any illegal activity.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">7. Service Availability</h2>
                    <p class="text-gray-400 leading-relaxed">We strive to maintain 99.9% uptime for our platform. However, we may occasionally need to perform maintenance or updates that may temporarily affect availability. We will provide advance notice for planned maintenance whenever possible.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">8. Limitation of Liability</h2>
                    <p class="text-gray-400 leading-relaxed">To the maximum extent permitted by law, BarberPro shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">9. Changes to Terms</h2>
                    <p class="text-gray-400 leading-relaxed">We reserve the right to modify these terms at any time. We will provide notice of significant changes via email or through the platform. Your continued use of the service after changes are posted constitutes your acceptance of the modified terms.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">10. Contact</h2>
                    <p class="text-gray-400 leading-relaxed">For questions about these Terms, please contact us at legal@barberpro.com or write to 742 Barber Lane, Austin, TX 78701.</p>
                </section>
            </div>
        </div>
    </div>
</template>
