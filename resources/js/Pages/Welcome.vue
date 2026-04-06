<template>
    <div class="min-h-screen bg-[#0f0f1a]">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f1a]/90 backdrop-blur-md border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link href="/" class="flex items-center gap-2">
                        <BarberPole size="md" />
                        <span class="text-xl font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                    </Link>
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#features" class="text-gray-300 hover:text-[#D4A853] transition text-sm font-medium">Features</a>
                        <a href="#pricing" class="text-gray-300 hover:text-[#D4A853] transition text-sm font-medium">Pricing</a>
                        <a href="#testimonials" class="text-gray-300 hover:text-[#D4A853] transition text-sm font-medium">Reviews</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <template v-if="$page.props.auth?.user">
                            <Link :href="route('dashboard')" class="text-sm text-gray-300 hover:text-[#D4A853] transition font-medium">Dashboard</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm text-gray-300 hover:text-white transition font-medium">Sign In</Link>
                            <Link :href="route('register')" class="bg-[#D4A853] text-[#0f0f1a] hover:bg-[#e0b865] transition px-5 py-2 rounded-lg text-sm font-bold">Start Free Trial</Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-16 overflow-hidden">
            <div class="absolute inset-0">
                <img
                    src="https://images.unsplash.com/photo-1503951914875-452d3946e4ec?w=1920&q=80&auto=format"
                    alt="Barbershop interior"
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f0f1a] via-[#0f0f1a]/85 to-[#0f0f1a]/60"></div>
            </div>
            <!-- Decorative barber poles -->
            <div class="hidden xl:block absolute right-12 top-24 bottom-0 z-10">
                <div class="sticky top-32">
                    <BarberPole size="tall" />
                </div>
            </div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-40">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 bg-[#D4A853]/15 border border-[#D4A853]/30 rounded-full px-4 py-1.5 mb-6">
                        <div class="w-2 h-2 bg-[#D4A853] rounded-full animate-pulse"></div>
                        <span class="text-[#D4A853] text-sm font-medium">Trusted by 2,000+ barbershops</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-6">
                        Run your shop like a
                        <span class="text-[#D4A853]">boss.</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-300 mb-8 leading-relaxed max-w-lg">
                        The all-in-one platform that handles bookings, staff, payments, and growth — so you can focus on what you do best.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link :href="route('register')" class="inline-flex items-center justify-center bg-[#D4A853] text-[#0f0f1a] hover:bg-[#e0b865] transition-all px-8 py-4 rounded-xl font-bold text-lg shadow-lg shadow-[#D4A853]/20 hover:shadow-xl hover:shadow-[#D4A853]/30">
                            Start Free Trial
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </Link>
                        <a href="#features" class="inline-flex items-center justify-center border-2 border-white/20 text-white hover:border-[#D4A853] hover:text-[#D4A853] transition-all px-8 py-4 rounded-xl font-semibold text-lg">
                            See How It Works
                        </a>
                    </div>
                    <div class="flex items-center gap-6 mt-8">
                        <div class="flex -space-x-2">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-[#0f0f1a] object-cover" alt="" />
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=80&h=80&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-[#0f0f1a] object-cover" alt="" />
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-[#0f0f1a] object-cover" alt="" />
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face" class="w-10 h-10 rounded-full border-2 border-[#0f0f1a] object-cover" alt="" />
                        </div>
                        <div class="text-sm">
                            <div class="flex items-center gap-1 text-[#D4A853]">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-gray-400">4.9 from 500+ reviews</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof Bar -->
        <section class="bg-[#16162a] border-y border-white/5 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-extrabold text-[#D4A853]">2,000+</div>
                        <div class="text-sm text-gray-400 mt-1">Active Shops</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-[#D4A853]">1.2M</div>
                        <div class="text-sm text-gray-400 mt-1">Appointments Booked</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-[#D4A853]">$48M</div>
                        <div class="text-sm text-gray-400 mt-1">Revenue Processed</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-[#D4A853]">99.9%</div>
                        <div class="text-sm text-gray-400 mt-1">Uptime</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#0f0f1a]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                        Everything your shop needs
                    </h2>
                    <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                        From first appointment to repeat customer — we've got every step covered.
                    </p>
                </div>

                <!-- Feature 1: Booking -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-24">
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#D4A853]/15 rounded-xl mb-6">
                            <svg class="w-6 h-6 text-[#D4A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Online Booking That Actually Works</h3>
                        <p class="text-gray-400 leading-relaxed mb-6">
                            Clients book anytime from their phone. Automatic confirmations, reminders, and no-show protection built in. Your calendar stays full without lifting a finger.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>24/7 online booking page</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>SMS & email reminders</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Walk-in queue management</li>
                        </ul>
                    </div>
                    <div class="relative">
                        <img
                            src="https://images.unsplash.com/photo-1585747860019-8729c0f98a8f?w=800&q=80&auto=format"
                            alt="Barber working on client"
                            class="rounded-2xl shadow-2xl"
                        />
                        <div class="absolute -bottom-4 -left-4 bg-[#16162a] border border-white/10 rounded-xl p-4 shadow-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="text-white text-sm font-semibold">New Booking</div>
                                    <div class="text-gray-400 text-xs">Mike R. — Skin Fade at 2:30 PM</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2: Staff -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-24">
                    <div class="order-2 lg:order-1 relative">
                        <img
                            src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=800&q=80&auto=format"
                            alt="Barbershop team"
                            class="rounded-2xl shadow-2xl"
                        />
                        <div class="absolute -bottom-4 -right-4 bg-[#16162a] border border-white/10 rounded-xl p-4 shadow-xl">
                            <div class="text-[#D4A853] text-2xl font-bold">$12,450</div>
                            <div class="text-gray-400 text-xs">Team revenue this month</div>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#D4A853]/15 rounded-xl mb-6">
                            <svg class="w-6 h-6 text-[#D4A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Staff Management Made Simple</h3>
                        <p class="text-gray-400 leading-relaxed mb-6">
                            Track schedules, commissions, and performance for every barber. See who's crushing it and who needs support — all in real time.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Individual commission tracking</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Flexible shift scheduling</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Performance leaderboards</li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3: Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#D4A853]/15 rounded-xl mb-6">
                            <svg class="w-6 h-6 text-[#D4A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Know Your Numbers</h3>
                        <p class="text-gray-400 leading-relaxed mb-6">
                            Revenue trends, peak hours, top services, customer retention — see exactly what's working and where to grow. Data-driven decisions, no guesswork.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Revenue & growth dashboards</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Popular service analysis</li>
                            <li class="flex items-center gap-3 text-gray-300"><svg class="w-5 h-5 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Customer loyalty metrics</li>
                        </ul>
                    </div>
                    <div class="relative">
                        <img
                            src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=800&q=80&auto=format"
                            alt="Barbershop interior"
                            class="rounded-2xl shadow-2xl"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#16162a]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                        Plans that grow with you
                    </h2>
                    <p class="text-lg text-gray-400">
                        Start free. Upgrade when you're ready.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <!-- Starter -->
                    <div class="bg-[#0f0f1a] p-8 rounded-2xl border border-white/10 hover:border-[#D4A853]/50 transition-all duration-300">
                        <h3 class="text-xl font-bold text-white mb-1">Starter</h3>
                        <p class="text-gray-500 text-sm mb-6">For solo barbers</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-white">$49</span>
                            <span class="text-gray-500">/mo</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="item in ['1 location', '3 staff members', 'Online booking', 'Customer CRM', 'Basic reports']" :key="item" class="flex items-center gap-3 text-gray-300 text-sm">
                                <svg class="w-4 h-4 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ item }}
                            </li>
                        </ul>
                        <Link :href="route('register')" class="block w-full text-center border-2 border-[#D4A853] text-[#D4A853] hover:bg-[#D4A853] hover:text-[#0f0f1a] transition-all px-6 py-3 rounded-xl font-bold">
                            Get Started
                        </Link>
                    </div>

                    <!-- Professional -->
                    <div class="bg-[#0f0f1a] p-8 rounded-2xl border-2 border-[#D4A853] relative shadow-xl shadow-[#D4A853]/10">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#D4A853] text-[#0f0f1a] text-xs font-bold px-4 py-1 rounded-full">
                            MOST POPULAR
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Professional</h3>
                        <p class="text-gray-500 text-sm mb-6">For growing shops</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-white">$99</span>
                            <span class="text-gray-500">/mo</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="item in ['3 locations', 'Unlimited staff', 'Advanced analytics', 'SMS reminders', 'Marketing tools', 'Priority support']" :key="item" class="flex items-center gap-3 text-gray-300 text-sm">
                                <svg class="w-4 h-4 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ item }}
                            </li>
                        </ul>
                        <Link :href="route('register')" class="block w-full text-center bg-[#D4A853] text-[#0f0f1a] hover:bg-[#e0b865] transition-all px-6 py-3 rounded-xl font-bold">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- Enterprise -->
                    <div class="bg-[#0f0f1a] p-8 rounded-2xl border border-white/10 hover:border-[#D4A853]/50 transition-all duration-300">
                        <h3 class="text-xl font-bold text-white mb-1">Enterprise</h3>
                        <p class="text-gray-500 text-sm mb-6">For chains & franchises</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-white">$199</span>
                            <span class="text-gray-500">/mo</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-for="item in ['Unlimited locations', 'Unlimited staff', 'Custom integrations', 'API access', 'Dedicated manager', 'White-label option']" :key="item" class="flex items-center gap-3 text-gray-300 text-sm">
                                <svg class="w-4 h-4 text-[#D4A853] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ item }}
                            </li>
                        </ul>
                        <Link :href="route('register')" class="block w-full text-center border-2 border-[#D4A853] text-[#D4A853] hover:bg-[#D4A853] hover:text-[#0f0f1a] transition-all px-6 py-3 rounded-xl font-bold">
                            Contact Sales
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section id="testimonials" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#0f0f1a]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                        Barbers love BarberPro
                    </h2>
                    <p class="text-lg text-gray-400">
                        Don't take our word for it — hear from shop owners like you.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="testimonial in testimonials" :key="testimonial.name" class="bg-[#16162a] rounded-2xl p-8 border border-white/5">
                        <div class="flex items-center gap-1 mb-4">
                            <svg v-for="i in 5" :key="i" class="w-4 h-4 text-[#D4A853]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-6">{{ testimonial.quote }}</p>
                        <div class="flex items-center gap-3">
                            <img :src="testimonial.avatar" :alt="testimonial.name" class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <div class="text-white font-semibold text-sm">{{ testimonial.name }}</div>
                                <div class="text-gray-500 text-xs">{{ testimonial.shop }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 px-4 sm:px-6 lg:px-8 bg-[#16162a]">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6">
                    Ready to level up your shop?
                </h2>
                <p class="text-lg text-gray-400 mb-8 max-w-2xl mx-auto">
                    Join thousands of barbershops already growing with BarberPro. Start your free trial today — no credit card required.
                </p>
                <Link :href="route('register')" class="inline-flex items-center bg-[#D4A853] text-[#0f0f1a] hover:bg-[#e0b865] transition-all px-10 py-4 rounded-xl font-bold text-lg shadow-lg shadow-[#D4A853]/20">
                    Start Your Free Trial
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#0f0f1a] border-t border-white/5 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
                    <div class="col-span-2 md:col-span-1">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-[#D4A853] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#0f0f1a]" fill="currentColor" viewBox="0 0 24 24"><path d="M13 5.5C13 3.57 11.43 2 9.5 2 7.57 2 6 3.57 6 5.5c0 .28.22.5.5.5s.5-.22.5-.5C7 4.12 8.12 3 9.5 3S12 4.12 12 5.5 10.88 8 9.5 8c-.28 0-.5.22-.5.5s.22.5.5.5c1.93 0 3.5-1.57 3.5-3.5z"/></svg>
                            </div>
                            <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                        </div>
                        <p class="text-gray-500 text-sm">The modern platform for modern barbershops.</p>
                    </div>
                    <div v-for="section in footerLinks" :key="section.title">
                        <h4 class="text-white font-semibold text-sm mb-4">{{ section.title }}</h4>
                        <ul class="space-y-2">
                            <li v-for="link in section.links" :key="link.label">
                                <Link v-if="link.route" :href="link.route" class="text-gray-500 hover:text-[#D4A853] transition text-sm">{{ link.label }}</Link>
                                <span v-else class="text-gray-600 text-sm cursor-default">{{ link.label }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-white/5 pt-8 text-center">
                    <p class="text-gray-600 text-sm">&copy; 2026 BarberPro. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import BarberPole from '@/Components/BarberPole.vue';

const testimonials = [
    {
        quote: "BarberPro transformed how we run our shop. Bookings are up 40% and no-shows dropped to almost zero. The online booking alone pays for itself.",
        name: "Marcus Williams",
        shop: "Fresh Fades Studio, Atlanta",
        avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face",
    },
    {
        quote: "I used to spend hours on scheduling and tracking commissions. Now it's all automatic. My barbers love seeing their stats and it motivates them to do more.",
        name: "David Chen",
        shop: "The Gentlemen's Quarter, NYC",
        avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=80&h=80&fit=crop&crop=face",
    },
    {
        quote: "We went from one location to three in a year. BarberPro made it seamless — same system, same data, all connected. Can't imagine running without it.",
        name: "James Rivera",
        shop: "Rivera's Classic Cuts, Miami",
        avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&crop=face",
    },
];

const footerLinks = [
    { title: 'Product', links: [
        { label: 'Features', route: route('features') },
        { label: 'Pricing', route: route('pricing') },
    ]},
    { title: 'Company', links: [
        { label: 'About', route: route('about') },
        { label: 'Contact', route: route('contact') },
    ]},
    { title: 'Legal', links: [
        { label: 'Privacy', route: route('privacy') },
        { label: 'Terms', route: route('terms') },
    ]},
];
</script>
