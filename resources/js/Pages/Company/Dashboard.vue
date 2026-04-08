<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    company: Object,
    shops: Array,
    todayStats: Object,
    revenueByLocation: Array,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">{{ company.name }} — Company Dashboard</h2>
                <span class="text-gray-500 text-sm">{{ shops.length }} locations</span>
            </div>
        </template>

        <!-- Today's Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5">
                <div class="text-3xl font-bold text-[#D4A853]">{{ todayStats.total_check_ins }}</div>
                <div class="text-xs text-gray-500 mt-1">Check-Ins Today</div>
            </div>
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5">
                <div class="text-3xl font-bold text-green-400">{{ todayStats.completed }}</div>
                <div class="text-xs text-gray-500 mt-1">Completed</div>
            </div>
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5">
                <div class="text-3xl font-bold text-yellow-400">{{ todayStats.total_waiting }}</div>
                <div class="text-xs text-gray-500 mt-1">Currently Waiting</div>
            </div>
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5">
                <div class="text-3xl font-bold text-red-400">{{ todayStats.no_shows }}</div>
                <div class="text-xs text-gray-500 mt-1">No Shows</div>
            </div>
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5">
                <div class="text-3xl font-bold text-white">${{ todayStats.revenue.toFixed(0) }}</div>
                <div class="text-xs text-gray-500 mt-1">Revenue Today</div>
            </div>
        </div>

        <!-- Location Cards -->
        <h3 class="text-lg font-bold text-white mb-4">Locations</h3>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 mb-8">
            <div v-for="shop in shops" :key="shop.id" class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-5 hover:border-[#D4A853]/50 transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-white font-bold">{{ shop.name }}</h4>
                        <p class="text-gray-500 text-sm">{{ shop.city }}, {{ shop.state }}</p>
                    </div>
                    <Link :href="`/display/${shop.id}`" target="_blank" class="text-xs text-[#D4A853] hover:underline">TV Display</Link>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <div class="text-xl font-bold" :class="shop.waiting_count > 5 ? 'text-red-400' : shop.waiting_count > 0 ? 'text-yellow-400' : 'text-green-400'">
                            {{ shop.waiting_count }}
                        </div>
                        <div class="text-xs text-gray-600">Waiting</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-blue-400">{{ shop.in_service_count }}</div>
                        <div class="text-xs text-gray-600">In Service</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-green-400">{{ shop.active_staff_count }}</div>
                        <div class="text-xs text-gray-600">Staff Active</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Location -->
        <h3 class="text-lg font-bold text-white mb-4">This Week by Location</h3>
        <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-500 text-xs uppercase px-6 py-3">Location</th>
                        <th class="text-right text-gray-500 text-xs uppercase px-6 py-3">Check-Ins</th>
                        <th class="text-right text-gray-500 text-xs uppercase px-6 py-3">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="loc in revenueByLocation" :key="loc.name" class="border-b border-gray-800/50 last:border-0">
                        <td class="text-white px-6 py-3">{{ loc.name }}</td>
                        <td class="text-gray-400 text-right px-6 py-3">{{ loc.check_ins }}</td>
                        <td class="text-[#D4A853] text-right font-medium px-6 py-3">${{ loc.revenue.toFixed(2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
