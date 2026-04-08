<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    shop: Object,
    queueStatus: Object,
    reverbKey: String,
    reverbHost: String,
    reverbPort: Number,
});

const queue = ref(props.queueStatus);
const currentTime = ref(new Date());
let pollInterval = null;
let clockInterval = null;

const nowServing = computed(() => {
    const inService = queue.value.in_service || [];
    const called = queue.value.called || [];
    return [...called, ...inService];
});

const waitingList = computed(() => queue.value.waiting || []);

async function refreshQueue() {
    try {
        const res = await axios.get(`/api/v1/queue/shop/${props.shop.id}`);
        queue.value = res.data;
    } catch (e) {}
}

onMounted(() => {
    pollInterval = setInterval(refreshQueue, 5000);
    clockInterval = setInterval(() => { currentTime.value = new Date(); }, 1000);

    if (window.Echo) {
        window.Echo.channel(`queue.${props.shop.id}`)
            .listen('QueueUpdated', (data) => {
                queue.value = data;
            });
    }
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
    if (clockInterval) clearInterval(clockInterval);
});

function formatTime(date) {
    return new Date(date).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}
</script>

<template>
    <div class="min-h-screen bg-[#0a0a15] text-white overflow-hidden select-none cursor-none">
        <!-- Header Bar -->
        <div class="bg-[#1a1a2e] border-b border-[#D4A853]/30 px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="text-3xl font-black">Barber<span class="text-[#D4A853]">Pro</span></div>
                <div class="text-gray-400 text-lg">{{ shop.name }}</div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-mono text-[#D4A853]">
                    {{ currentTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) }}
                </div>
                <div class="text-gray-500 text-sm">{{ currentTime.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' }) }}</div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-80px)]">
            <!-- Left: Now Serving -->
            <div class="w-1/2 p-8 flex flex-col">
                <h2 class="text-[#D4A853] text-2xl font-bold mb-6 uppercase tracking-wider">Now Serving</h2>

                <div v-if="nowServing.length === 0" class="flex-1 flex items-center justify-center">
                    <div class="text-gray-600 text-2xl">No one being served</div>
                </div>

                <div v-else class="space-y-4 flex-1 overflow-hidden">
                    <div
                        v-for="entry in nowServing"
                        :key="entry.id"
                        class="bg-gradient-to-r from-[#D4A853]/20 to-transparent border border-[#D4A853]/30 rounded-2xl p-6 flex items-center gap-6"
                    >
                        <div class="text-5xl font-black text-[#D4A853] min-w-[120px] text-center">
                            {{ entry.queue_number }}
                        </div>
                        <div class="flex-1">
                            <div class="text-2xl font-bold">{{ entry.customer_name }}</div>
                            <div v-if="entry.staff" class="text-gray-400 mt-1">
                                with {{ entry.staff.name }}
                            </div>
                        </div>
                        <div class="px-4 py-2 rounded-full text-sm font-bold" :class="entry.status === 'called' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400'">
                            {{ entry.status === 'called' ? 'CALLED' : 'IN SERVICE' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Waiting List -->
            <div class="w-1/2 bg-[#0d0d1a] p-8 flex flex-col border-l border-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-gray-400 text-2xl font-bold uppercase tracking-wider">Waiting</h2>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <div class="text-4xl font-black text-white">{{ waitingList.length }}</div>
                            <div class="text-xs text-gray-600 uppercase">In Line</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-black text-[#D4A853]">~{{ queue.wait_time || 0 }}</div>
                            <div class="text-xs text-gray-600 uppercase">Min Wait</div>
                        </div>
                    </div>
                </div>

                <div v-if="waitingList.length === 0" class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <div class="text-gray-500 text-2xl">No wait!</div>
                        <div class="text-gray-700 mt-2">Walk right in</div>
                    </div>
                </div>

                <div v-else class="space-y-3 flex-1 overflow-hidden">
                    <div
                        v-for="(entry, i) in waitingList.slice(0, 12)"
                        :key="entry.id"
                        class="bg-[#1a1a2e]/60 border border-gray-800 rounded-xl px-6 py-4 flex items-center gap-4"
                        :class="i === 0 ? 'border-[#D4A853]/40 bg-[#D4A853]/5' : ''"
                    >
                        <div class="text-2xl font-bold text-gray-400 min-w-[50px]">{{ entry.position }}</div>
                        <div class="text-2xl font-bold text-[#D4A853] min-w-[100px]">{{ entry.queue_number }}</div>
                        <div class="flex-1 text-lg text-gray-300">{{ entry.customer_name }}</div>
                        <div class="text-gray-600 text-sm">{{ entry.party_size > 1 ? `${entry.party_size} people` : '' }}</div>
                        <div class="text-gray-500 text-sm">~{{ entry.estimated_wait_minutes }} min</div>
                    </div>
                    <div v-if="waitingList.length > 12" class="text-center text-gray-600 py-2">
                        +{{ waitingList.length - 12 }} more
                    </div>
                </div>

                <!-- Active Staff -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600 text-sm uppercase tracking-wider">Stylists on duty:</span>
                        <div class="flex gap-2">
                            <div
                                v-for="staff in queue.active_staff"
                                :key="staff.id"
                                class="flex items-center gap-2 bg-[#1a1a2e] rounded-full px-3 py-1"
                            >
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                <span class="text-sm text-gray-300">{{ staff.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
