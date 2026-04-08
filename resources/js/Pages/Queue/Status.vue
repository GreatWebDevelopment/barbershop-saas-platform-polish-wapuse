<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    entry: Object,
    shopId: Number,
});

const queueEntry = ref(props.entry);
const loading = ref(false);
let pollInterval = null;
let echoChannel = null;

const statusConfig = {
    waiting: { label: 'In Line', color: 'text-yellow-400', bg: 'bg-yellow-400/10', icon: '⏳' },
    called: { label: "It's Your Turn!", color: 'text-green-400', bg: 'bg-green-400/10', icon: '🔔' },
    in_service: { label: 'In Service', color: 'text-blue-400', bg: 'bg-blue-400/10', icon: '✂️' },
    completed: { label: 'Completed', color: 'text-gray-400', bg: 'bg-gray-400/10', icon: '✅' },
    no_show: { label: 'No Show', color: 'text-red-400', bg: 'bg-red-400/10', icon: '❌' },
    cancelled: { label: 'Cancelled', color: 'text-gray-500', bg: 'bg-gray-500/10', icon: '🚫' },
};

const status = computed(() => statusConfig[queueEntry.value.status] || statusConfig.waiting);

async function refreshStatus() {
    try {
        const res = await axios.get(`/api/v1/queue/${queueEntry.value.queue_number}`);
        queueEntry.value = res.data.queue_entry;
    } catch (e) {
        // entry may have been removed
    }
}

async function cancelCheckIn() {
    if (!confirm('Are you sure you want to cancel your check-in?')) return;
    loading.value = true;
    try {
        await axios.delete(`/api/v1/queue/${queueEntry.value.queue_number}`);
        queueEntry.value.status = 'cancelled';
    } catch (e) {
        // already cancelled
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    // Poll every 10 seconds as fallback
    pollInterval = setInterval(refreshStatus, 10000);

    // Try WebSocket via Echo (if available)
    if (window.Echo) {
        echoChannel = window.Echo.channel(`queue.${props.shopId}`)
            .listen('QueueUpdated', () => {
                refreshStatus();
            });
    }
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
    if (echoChannel) echoChannel.stopListening('QueueUpdated');
});
</script>

<template>
    <div class="min-h-screen bg-[#0f0f1a] flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Queue Number -->
            <div class="text-center mb-8">
                <div class="text-gray-500 text-sm mb-2">Queue Number</div>
                <div class="text-6xl font-black text-[#D4A853]">{{ queueEntry.queue_number }}</div>
            </div>

            <!-- Status Card -->
            <div :class="status.bg" class="rounded-2xl border border-gray-800 p-8 text-center mb-6">
                <div class="text-5xl mb-4">{{ status.icon }}</div>
                <div :class="status.color" class="text-2xl font-bold mb-2">{{ status.label }}</div>

                <template v-if="queueEntry.status === 'waiting'">
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="bg-[#0f0f1a] rounded-xl p-4">
                            <div class="text-3xl font-bold text-white">{{ queueEntry.position }}</div>
                            <div class="text-xs text-gray-500">Position</div>
                        </div>
                        <div class="bg-[#0f0f1a] rounded-xl p-4">
                            <div class="text-3xl font-bold text-white">~{{ queueEntry.estimated_wait_minutes }}</div>
                            <div class="text-xs text-gray-500">Min Wait</div>
                        </div>
                    </div>
                </template>

                <template v-if="queueEntry.status === 'called'">
                    <p class="text-gray-300 mt-2">Please head to the front desk!</p>
                    <div v-if="queueEntry.staff" class="mt-4 text-gray-400">
                        Your stylist: <span class="text-white font-medium">{{ queueEntry.staff.name }}</span>
                    </div>
                </template>

                <template v-if="queueEntry.status === 'in_service'">
                    <div v-if="queueEntry.staff" class="mt-4 text-gray-400">
                        With: <span class="text-white font-medium">{{ queueEntry.staff.name }}</span>
                    </div>
                </template>

                <template v-if="queueEntry.status === 'completed'">
                    <p class="text-gray-400 mt-2">Thanks for visiting!</p>
                </template>
            </div>

            <!-- Shop Info -->
            <div class="bg-[#1a1a2e] rounded-xl border border-gray-800 p-4 mb-6">
                <div class="text-white font-medium">{{ queueEntry.shop?.name }}</div>
                <div class="text-gray-500 text-sm">{{ queueEntry.shop?.address }}, {{ queueEntry.shop?.city }}</div>
            </div>

            <!-- Cancel button -->
            <button
                v-if="['waiting', 'called'].includes(queueEntry.status)"
                @click="cancelCheckIn"
                :disabled="loading"
                class="w-full py-3 border border-red-500/30 text-red-400 rounded-xl font-medium hover:bg-red-500/10 transition"
            >
                {{ loading ? 'Cancelling...' : 'Cancel Check-In' }}
            </button>

            <!-- Back to locations -->
            <Link href="/locations" class="block mt-4 text-center text-gray-500 text-sm hover:text-[#D4A853] transition">
                Find another location
            </Link>
        </div>
    </div>
</template>
