<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#16162a]">
        <!-- Sidebar Navigation for Desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-[#1a1a2e] border-r border-gray-800 px-6 pb-4">
                <div class="flex h-16 shrink-0 items-center">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="h-8 w-auto" />
                    </Link>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <NavLink
                                        :href="route('dashboard')"
                                        :active="route().current('dashboard')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('dashboard')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                        </svg>
                                        Dashboard
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        :href="route('appointments.index')"
                                        :active="route().current('appointments.*')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('appointments.*')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        Appointments
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        :href="route('staff.index')"
                                        :active="route().current('staff.*')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('staff.*')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        Staff
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        :href="route('services.index')"
                                        :active="route().current('services.*')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('services.*')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                        </svg>
                                        Services
                                    </NavLink>
                                </li>
                                <li>
                                    <NavLink
                                        :href="route('customers.index')"
                                        :active="route().current('customers.*')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('customers.*')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        Customers
                                    </NavLink>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider mt-4">Settings</div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <NavLink
                                        :href="route('settings.payments')"
                                        :active="route().current('settings.payments*')"
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                                        :class="route().current('settings.payments*')
                                            ? 'bg-[#D4A853] text-[#1a1a2e]'
                                            : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]'"
                                    >
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                        </svg>
                                        Payments
                                    </NavLink>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Top navigation bar for desktop -->
        <div class="lg:pl-64">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-800 bg-[#1a1a2e] px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button
                    type="button"
                    class="lg:hidden -m-2.5 p-2.5 text-[#f5f0e8] hover:text-[#D4A853]"
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path
                            :class="{
                                hidden: showingNavigationDropdown,
                                'inline-flex': !showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                        <path
                            :class="{
                                hidden: !showingNavigationDropdown,
                                'inline-flex': showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1"></div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- User dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center gap-x-4 px-3 py-2 text-sm font-semibold leading-6 text-[#f5f0e8] hover:text-[#D4A853] transition duration-150 ease-in-out"
                                >
                                    <span class="hidden lg:flex lg:items-center">
                                        <span class="text-sm font-semibold leading-6" aria-hidden="true">{{ $page.props.auth.user.name }}</span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="lg:hidden"
            >
                <div class="border-b border-gray-800 bg-[#1a1a2e] px-4 py-3">
                    <div class="flex items-center">
                        <ApplicationLogo class="h-8 w-auto" />
                    </div>
                </div>
                <div class="space-y-1 bg-[#1a1a2e] px-2 pb-3 pt-2">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('appointments.index')"
                        :active="route().current('appointments.*')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Appointments
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('staff.index')"
                        :active="route().current('staff.*')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Staff
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('services.index')"
                        :active="route().current('services.*')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Services
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('customers.index')"
                        :active="route().current('customers.*')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Customers
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('settings.payments')"
                        :active="route().current('settings.payments*')"
                        class="flex items-center gap-x-3"
                    >
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        Payments
                    </ResponsiveNavLink>
                </div>

                <!-- Responsive Settings Options -->
                <div class="border-t border-gray-800 bg-[#1a1a2e] pb-1 pt-4">
                    <div class="px-4">
                        <div class="text-base font-medium text-[#f5f0e8]">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-400">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1 px-2">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header class="bg-[#1a1a2e] shadow-sm border-b border-gray-800" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="py-10">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
