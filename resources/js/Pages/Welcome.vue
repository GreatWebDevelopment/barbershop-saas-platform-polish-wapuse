<template>
    <div class="min-h-screen bg-[#1a1a2e]">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#16162a] border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex-shrink-0">
                        <Link href="/" class="text-2xl font-bold text-[#D4A853]">
                            BarberPro
                        </Link>
                    </div>
                    <div class="flex items-center gap-4">
                        <template v-if="$page.props.auth?.user">
                            <Link
                                :href="route('dashboard')"
                                class="text-gray-300 hover:text-[#D4A853] transition-colors duration-200 px-4 py-2"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-gray-300 hover:text-[#D4A853] transition-colors duration-200 px-4 py-2"
                            >
                                Login
                            </Link>
                            <Link
                                :href="route('register')"
                                class="bg-[#D4A853] text-[#16162a] hover:bg-[#c99a42] transition-colors duration-200 px-6 py-2 rounded-lg font-semibold"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#16162a] to-[#1a1a2e]">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                    The Modern Platform for<br />
                    <span class="text-[#D4A853]">Modern Barbershops</span>
                </h1>
                <p class="text-xl sm:text-2xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Streamline bookings, manage your team, and grow your business with the all-in-one barbershop management platform.
                </p>
                <Link
                    :href="route('register')"
                    class="inline-block bg-[#D4A853] text-[#16162a] hover:bg-[#c99a42] transition-all duration-200 px-10 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                >
                    Get Started Free
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#f5f0e8]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-bold text-[#16162a] mb-4">
                        Everything You Need to Succeed
                    </h2>
                    <p class="text-xl text-gray-600">
                        Powerful features designed specifically for barbershops
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Online Booking -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] hover:border-[#D4A853] transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                        <div class="w-14 h-14 bg-[#D4A853] rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#16162a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#16162a] mb-3">Online Booking</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Let clients book 24/7 with your custom booking page
                        </p>
                    </div>

                    <!-- Staff Management -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] hover:border-[#D4A853] transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                        <div class="w-14 h-14 bg-[#D4A853] rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#16162a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#16162a] mb-3">Staff Management</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Track schedules, commissions, and performance
                        </p>
                    </div>

                    <!-- Smart Analytics -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] hover:border-[#D4A853] transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                        <div class="w-14 h-14 bg-[#D4A853] rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#16162a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#16162a] mb-3">Smart Analytics</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Revenue trends, popular services, and growth insights
                        </p>
                    </div>

                    <!-- Customer CRM -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] hover:border-[#D4A853] transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                        <div class="w-14 h-14 bg-[#D4A853] rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#16162a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#16162a] mb-3">Customer CRM</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Build loyalty with visit history and preferences tracking
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#1a1a2e]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">
                        Simple, Transparent Pricing
                    </h2>
                    <p class="text-xl text-gray-300">
                        Choose the perfect plan for your barbershop
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Starter -->
                    <div class="bg-[#16162a] p-8 rounded-xl border-2 border-gray-700 hover:border-[#D4A853] transition-all duration-300">
                        <h3 class="text-2xl font-bold text-white mb-2">Starter</h3>
                        <div class="mb-6">
                            <span class="text-5xl font-bold text-[#D4A853]">$49</span>
                            <span class="text-gray-400">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                1 location
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                3 staff members
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Online booking
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Basic reports
                            </li>
                        </ul>
                        <Link
                            :href="route('register')"
                            class="block w-full text-center bg-[#D4A853] text-[#16162a] hover:bg-[#c99a42] transition-colors duration-200 px-6 py-3 rounded-lg font-bold"
                        >
                            Get Started
                        </Link>
                    </div>

                    <!-- Professional (Highlighted) -->
                    <div class="bg-[#16162a] p-8 rounded-xl border-2 border-[#D4A853] hover:shadow-2xl hover:shadow-[#D4A853]/20 transition-all duration-300 transform scale-105">
                        <div class="inline-block bg-[#D4A853] text-[#16162a] text-sm font-bold px-3 py-1 rounded-full mb-4">
                            MOST POPULAR
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Professional</h3>
                        <div class="mb-6">
                            <span class="text-5xl font-bold text-[#D4A853]">$99</span>
                            <span class="text-gray-400">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                3 locations
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                10 staff members
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                SMS reminders
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <Link
                            :href="route('register')"
                            class="block w-full text-center bg-[#D4A853] text-[#16162a] hover:bg-[#c99a42] transition-colors duration-200 px-6 py-3 rounded-lg font-bold"
                        >
                            Get Started
                        </Link>
                    </div>

                    <!-- Enterprise -->
                    <div class="bg-[#16162a] p-8 rounded-xl border-2 border-gray-700 hover:border-[#D4A853] transition-all duration-300">
                        <h3 class="text-2xl font-bold text-white mb-2">Enterprise</h3>
                        <div class="mb-6">
                            <span class="text-5xl font-bold text-[#D4A853]">$199</span>
                            <span class="text-gray-400">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Unlimited locations
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Unlimited staff
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Custom integrations
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Dedicated account manager
                            </li>
                            <li class="flex items-start text-gray-300">
                                <svg class="w-6 h-6 text-[#D4A853] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                API access
                            </li>
                        </ul>
                        <Link
                            :href="route('register')"
                            class="block w-full text-center bg-[#D4A853] text-[#16162a] hover:bg-[#c99a42] transition-colors duration-200 px-6 py-3 rounded-lg font-bold"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#f5f0e8]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl sm:text-5xl font-bold text-[#16162a] mb-4">
                        Trusted by Barbershops Everywhere
                    </h2>
                    <p class="text-xl text-gray-600">
                        See what our customers have to say
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] shadow-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-[#D4A853]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                            "BarberPro transformed how we run our shop. Bookings are up 40%."
                        </p>
                        <div class="border-t-2 border-[#D4A853] pt-4">
                            <p class="font-bold text-[#16162a]">Marcus Williams</p>
                            <p class="text-gray-600">Fresh Fades Studio</p>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] shadow-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-[#D4A853]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                            "Finally, software that understands barbershops. The analytics alone pay for itself."
                        </p>
                        <div class="border-t-2 border-[#D4A853] pt-4">
                            <p class="font-bold text-[#16162a]">David Chen</p>
                            <p class="text-gray-600">The Gentlemen's Quarter</p>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white p-8 rounded-xl border-2 border-[#16162a] shadow-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-[#D4A853]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                            "My clients love the online booking. No more phone tag."
                        </p>
                        <div class="border-t-2 border-[#D4A853] pt-4">
                            <p class="font-bold text-[#16162a]">James Rivera</p>
                            <p class="text-gray-600">Rivera's Classic Cuts</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#16162a] border-t border-gray-800 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8 mb-8">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <h3 class="text-2xl font-bold text-[#D4A853] mb-4">BarberPro</h3>
                        <p class="text-gray-400 text-sm">
                            The modern platform for modern barbershops.
                        </p>
                    </div>

                    <!-- Product -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Product</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Features
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Pricing
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Integrations
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Company -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Company</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    About
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Blog
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Careers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Support</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Help Center
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Contact
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Status
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Privacy
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-[#D4A853] transition-colors duration-200">
                                    Terms
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-t border-gray-800 pt-8">
                    <p class="text-center text-gray-400 text-sm">
                        &copy; 2026 BarberPro. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
