<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const submitted = ref(false);
</script>

<template>
    <Head title="Contact" />

    <div class="min-h-screen bg-[#0f0f1a]">
        <!-- Nav -->
        <nav class="border-b border-white/5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-[#D4A853] rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#0f0f1a]" fill="currentColor" viewBox="0 0 24 24"><path d="M13 5.5C13 3.57 11.43 2 9.5 2 7.57 2 6 3.57 6 5.5c0 .28.22.5.5.5s.5-.22.5-.5C7 4.12 8.12 3 9.5 3S12 4.12 12 5.5 10.88 8 9.5 8c-.28 0-.5.22-.5.5s.22.5.5.5c1.93 0 3.5-1.57 3.5-3.5z"/></svg>
                    </div>
                    <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                </Link>
                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="text-sm text-gray-400 hover:text-white transition">Sign in</Link>
                    <Link :href="route('register')" class="text-sm bg-[#D4A853] text-[#0f0f1a] px-4 py-2 rounded-lg font-semibold hover:bg-[#c09743] transition">Start Free</Link>
                </div>
            </div>
        </nav>

        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-16">
                <!-- Info -->
                <div>
                    <h1 class="text-4xl font-bold text-white mb-4">Get in touch</h1>
                    <p class="text-lg text-gray-400 mb-10">Have a question? Want a demo? We'd love to hear from you. Our team typically responds within a few hours.</p>

                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-[#D4A853]/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#D4A853]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Email us</h3>
                                <p class="text-gray-400">hello@barberpro.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-[#D4A853]/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#D4A853]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Call us</h3>
                                <p class="text-gray-400">(512) 555-0187</p>
                                <p class="text-sm text-gray-500">Mon-Fri, 9am-6pm CT</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-[#D4A853]/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#D4A853]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Office</h3>
                                <p class="text-gray-400">742 Barber Lane<br>Austin, TX 78701</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-[#1a1a2e] rounded-2xl p-8 border border-white/10">
                    <form v-if="!submitted" class="space-y-5" @submit.prevent="submitted = true">
                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Your name</label>
                            <input type="text" class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition" placeholder="John Smith" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Email</label>
                            <input type="email" class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition" placeholder="john@myshop.com" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Shop name (optional)</label>
                            <input type="text" class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition" placeholder="Classic Cuts Barbershop" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#f5f0e8] mb-1.5">Message</label>
                            <textarea rows="4" class="w-full rounded-lg border border-gray-700 bg-[#16162a] px-4 py-3 text-[#f5f0e8] placeholder-gray-500 focus:border-[#D4A853] focus:ring-1 focus:ring-[#D4A853] transition resize-none" placeholder="How can we help?"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#D4A853] text-[#0f0f1a] py-3 rounded-lg font-semibold hover:bg-[#c09743] transition">
                            Send message
                        </button>
                    </form>
                    <div v-else class="py-12 text-center">
                        <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Message sent!</h3>
                        <p class="text-gray-400">Thanks for reaching out. We'll get back to you within a few hours.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
