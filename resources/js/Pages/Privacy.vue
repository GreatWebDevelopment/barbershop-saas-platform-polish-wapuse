<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Privacy Policy" />

    <div class="min-h-screen bg-[#0f0f1a]">
        <nav class="border-b border-white/5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-[#D4A853] rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#0f0f1a]" fill="currentColor" viewBox="0 0 24 24"><path d="M13 5.5C13 3.57 11.43 2 9.5 2 7.57 2 6 3.57 6 5.5c0 .28.22.5.5.5s.5-.22.5-.5C7 4.12 8.12 3 9.5 3S12 4.12 12 5.5 10.88 8 9.5 8c-.28 0-.5.22-.5.5s.22.5.5.5c1.93 0 3.5-1.57 3.5-3.5z"/></svg>
                    </div>
                    <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                </Link>
            </div>
        </nav>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h1 class="text-4xl font-bold text-white mb-2">Privacy Policy</h1>
            <p class="text-gray-500 mb-12">Last updated: January 1, 2026</p>

            <div class="prose-invert space-y-8">
                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">1. Information We Collect</h2>
                    <p class="text-gray-400 leading-relaxed">We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support. This includes your name, email address, phone number, business information, and payment details.</p>
                    <p class="text-gray-400 leading-relaxed mt-3">We also automatically collect certain information when you use our platform, including your IP address, browser type, device information, and usage patterns.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">2. How We Use Your Information</h2>
                    <p class="text-gray-400 leading-relaxed">We use the information we collect to provide, maintain, and improve our services, process transactions, send you technical notices and support messages, and respond to your comments and questions.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">3. Information Sharing</h2>
                    <p class="text-gray-400 leading-relaxed">We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties. We may share information with trusted third parties who assist us in operating our platform, conducting our business, or serving our users, so long as those parties agree to keep this information confidential.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">4. Data Security</h2>
                    <p class="text-gray-400 leading-relaxed">We implement industry-standard security measures to protect your personal information. All data is encrypted in transit using TLS and at rest using AES-256 encryption. We regularly audit our security practices and maintain SOC 2 Type II compliance.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">5. Your Rights</h2>
                    <p class="text-gray-400 leading-relaxed">You have the right to access, correct, or delete your personal data at any time. You can export your data from your account settings or contact us for assistance. We will respond to all data requests within 30 days.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">6. Cookies</h2>
                    <p class="text-gray-400 leading-relaxed">We use cookies and similar tracking technologies to track activity on our platform and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-white mb-3">7. Contact Us</h2>
                    <p class="text-gray-400 leading-relaxed">If you have any questions about this Privacy Policy, please contact us at privacy@barberpro.com or write to us at 742 Barber Lane, Austin, TX 78701.</p>
                </section>
            </div>
        </div>
    </div>
</template>
