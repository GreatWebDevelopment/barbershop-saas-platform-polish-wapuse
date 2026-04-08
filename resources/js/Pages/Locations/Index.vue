<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    locations: Array,
    googleMapsKey: String,
});

const searchQuery = ref('');
const userLat = ref(null);
const userLng = ref(null);
const sortBy = ref('distance');
const loading = ref(false);

const filteredLocations = computed(() => {
    let locs = [...props.locations];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        locs = locs.filter(l =>
            l.name.toLowerCase().includes(q) ||
            l.city?.toLowerCase().includes(q) ||
            l.state?.toLowerCase().includes(q) ||
            l.zip?.includes(q)
        );
    }
    if (sortBy.value === 'wait') {
        locs.sort((a, b) => (a.wait_time || 0) - (b.wait_time || 0));
    }
    return locs;
});

function getUserLocation() {
    loading.value = true;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat.value = pos.coords.latitude;
                userLng.value = pos.coords.longitude;
                loading.value = false;
            },
            () => { loading.value = false; }
        );
    } else {
        loading.value = false;
    }
}

onMounted(() => {
    getUserLocation();
});
</script>

<template>
    <div class="min-h-screen bg-[#0f0f1a]">
        <!-- Header -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f1a]/90 backdrop-blur-md border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link href="/" class="flex items-center gap-2">
                        <span class="text-xl font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                    </Link>
                    <div class="flex items-center gap-4">
                        <Link href="/locations" class="text-[#D4A853] text-sm font-medium">Locations</Link>
                        <Link :href="route('login')" class="text-gray-300 hover:text-white text-sm font-medium">Sign In</Link>
                    </div>
                </div>
            </div>
        </nav>

        <div class="pt-24 pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4">Find a Location Near You</h1>
                <p class="text-gray-400 text-lg">Walk in, check in, and skip the wait</p>
            </div>

            <!-- Search & Filter -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by city, state, or zip..."
                        class="w-full pl-10 pr-4 py-3 bg-[#1a1a2e] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-[#D4A853] focus:ring-[#D4A853]"
                    />
                </div>
                <div class="flex gap-2">
                    <button
                        @click="sortBy = 'distance'"
                        :class="sortBy === 'distance' ? 'bg-[#D4A853] text-[#0f0f1a]' : 'bg-[#1a1a2e] text-gray-300 border border-gray-700'"
                        class="px-4 py-3 rounded-xl text-sm font-medium transition"
                    >
                        Nearest
                    </button>
                    <button
                        @click="sortBy = 'wait'"
                        :class="sortBy === 'wait' ? 'bg-[#D4A853] text-[#0f0f1a]' : 'bg-[#1a1a2e] text-gray-300 border border-gray-700'"
                        class="px-4 py-3 rounded-xl text-sm font-medium transition"
                    >
                        Shortest Wait
                    </button>
                </div>
            </div>

            <!-- Location Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="location in filteredLocations"
                    :key="location.id"
                    class="bg-[#1a1a2e] rounded-2xl border border-gray-800 overflow-hidden hover:border-[#D4A853]/50 transition group"
                >
                    <!-- Map placeholder -->
                    <div class="h-40 bg-gradient-to-br from-[#1a1a2e] to-[#16162a] flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <div class="p-6">
                        <h3 class="text-lg font-bold text-white mb-1 group-hover:text-[#D4A853] transition">{{ location.name }}</h3>
                        <p class="text-gray-400 text-sm mb-4">
                            {{ location.address }}<br />
                            {{ location.city }}, {{ location.state }} {{ location.zip }}
                        </p>

                        <!-- Stats -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full" :class="location.waiting_count === 0 ? 'bg-green-400' : location.waiting_count < 5 ? 'bg-yellow-400' : 'bg-red-400'"></div>
                                <span class="text-sm text-gray-300">{{ location.waiting_count || 0 }} waiting</span>
                            </div>
                            <div class="text-sm text-gray-400">
                                ~{{ location.wait_time || 0 }} min wait
                            </div>
                            <div v-if="location.distance" class="text-sm text-gray-500">
                                {{ location.distance.toFixed(1) }} mi
                            </div>
                        </div>

                        <!-- Active staff -->
                        <div class="flex items-center gap-2 mb-5">
                            <div class="flex -space-x-2">
                                <div
                                    v-for="(staff, i) in (location.active_staff || []).slice(0, 4)"
                                    :key="i"
                                    class="w-8 h-8 rounded-full bg-[#D4A853]/20 border-2 border-[#1a1a2e] flex items-center justify-center text-xs font-bold text-[#D4A853]"
                                >
                                    {{ staff.name?.charAt(0) }}
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ (location.active_staff || []).length }} stylists on duty</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <Link
                                :href="`/queue/check-in/${location.id}`"
                                class="flex-1 bg-[#D4A853] text-[#0f0f1a] text-center py-2.5 rounded-lg text-sm font-bold hover:bg-[#e0b865] transition"
                            >
                                Check In Now
                            </Link>
                            <Link
                                :href="`/locations/${location.id}`"
                                class="px-4 py-2.5 border border-gray-700 text-gray-300 rounded-lg text-sm font-medium hover:border-[#D4A853] hover:text-[#D4A853] transition"
                            >
                                Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredLocations.length === 0" class="text-center py-20">
                <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                <p class="text-gray-400 text-lg">No locations found</p>
                <p class="text-gray-600 text-sm mt-1">Try a different search or expand your area</p>
            </div>
        </div>
    </div>
</template>
