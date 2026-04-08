<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    location: Object,
    queueStatus: Object,
});
</script>

<template>
    <div class="min-h-screen bg-[#0f0f1a]">
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f1a]/90 backdrop-blur-md border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link href="/" class="text-xl font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></Link>
                    <Link href="/locations" class="text-gray-300 hover:text-[#D4A853] text-sm">All Locations</Link>
                </div>
            </div>
        </nav>

        <div class="pt-24 pb-16 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Location Header -->
            <div class="bg-[#1a1a2e] rounded-2xl border border-gray-800 p-8 mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">{{ location.name }}</h1>
                <p class="text-gray-400 mb-4">{{ location.address }}, {{ location.city }}, {{ location.state }} {{ location.zip }}</p>
                <div v-if="location.phone" class="text-gray-400 text-sm mb-6">
                    <a :href="`tel:${location.phone}`" class="text-[#D4A853] hover:underline">{{ location.phone }}</a>
                </div>

                <!-- Queue Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-[#16162a] rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-[#D4A853]">{{ queueStatus.total_waiting }}</div>
                        <div class="text-xs text-gray-500 mt-1">In Line</div>
                    </div>
                    <div class="bg-[#16162a] rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">~{{ queueStatus.wait_time }} min</div>
                        <div class="text-xs text-gray-500 mt-1">Est. Wait</div>
                    </div>
                    <div class="bg-[#16162a] rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-green-400">{{ queueStatus.active_staff?.length || 0 }}</div>
                        <div class="text-xs text-gray-500 mt-1">Stylists Active</div>
                    </div>
                </div>

                <Link
                    :href="`/queue/check-in/${location.id}`"
                    class="block w-full bg-[#D4A853] text-[#0f0f1a] text-center py-3 rounded-xl text-lg font-bold hover:bg-[#e0b865] transition"
                >
                    Check In Now
                </Link>
            </div>

            <!-- Services -->
            <div v-if="location.services?.length" class="bg-[#1a1a2e] rounded-2xl border border-gray-800 p-8 mb-8">
                <h2 class="text-xl font-bold text-white mb-4">Services</h2>
                <div class="space-y-3">
                    <div v-for="service in location.services" :key="service.id" class="flex justify-between items-center py-3 border-b border-gray-800 last:border-0">
                        <div>
                            <div class="text-white font-medium">{{ service.name }}</div>
                            <div class="text-gray-500 text-sm">{{ service.duration_minutes }} min</div>
                        </div>
                        <div class="text-[#D4A853] font-bold">${{ Number(service.price).toFixed(2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Active Staff -->
            <div v-if="location.active_staff?.length" class="bg-[#1a1a2e] rounded-2xl border border-gray-800 p-8">
                <h2 class="text-xl font-bold text-white mb-4">Stylists On Duty</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div v-for="staff in location.active_staff" :key="staff.id" class="bg-[#16162a] rounded-xl p-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-[#D4A853]/20 mx-auto mb-2 flex items-center justify-center text-lg font-bold text-[#D4A853]">
                            {{ staff.name?.charAt(0) }}
                        </div>
                        <div class="text-white text-sm font-medium">{{ staff.name }}</div>
                        <div v-if="staff.title" class="text-gray-500 text-xs">{{ staff.title }}</div>
                    </div>
                </div>
            </div>

            <!-- Hours -->
            <div v-if="location.hours" class="bg-[#1a1a2e] rounded-2xl border border-gray-800 p-8 mt-8">
                <h2 class="text-xl font-bold text-white mb-4">Hours</h2>
                <div class="space-y-2">
                    <div v-for="(time, day) in location.hours" :key="day" class="flex justify-between text-sm">
                        <span class="text-gray-400 capitalize">{{ day }}</span>
                        <span class="text-white">{{ time || 'Closed' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
