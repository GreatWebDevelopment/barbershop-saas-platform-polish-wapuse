<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    shop: Object,
});

const step = ref(1); // 1: name/phone, 2: party size, 3: service, 4: stylist, 5: confirm
const form = ref({
    shop_id: props.shop.id,
    customer_name: '',
    customer_phone: '',
    party_size: 1,
    service_id: null,
    stylist_preference: 'first_available',
    notes: '',
});
const submitting = ref(false);
const error = ref('');
const result = ref(null);

const canProceed = computed(() => {
    if (step.value === 1) return form.value.customer_name && form.value.customer_phone;
    return true;
});

function nextStep() {
    if (step.value < 4) step.value++;
    else submitCheckIn();
}

function prevStep() {
    if (step.value > 1) step.value--;
}

async function submitCheckIn() {
    submitting.value = true;
    error.value = '';
    try {
        const res = await axios.post('/api/v1/queue/check-in', form.value);
        result.value = res.data;
        step.value = 5;
    } catch (e) {
        error.value = e.response?.data?.message || 'Something went wrong. Please try again.';
    } finally {
        submitting.value = false;
    }
}

function formatPhone(e) {
    let val = e.target.value.replace(/\D/g, '');
    if (val.length > 10) val = val.slice(0, 10);
    if (val.length > 6) val = `(${val.slice(0,3)}) ${val.slice(3,6)}-${val.slice(6)}`;
    else if (val.length > 3) val = `(${val.slice(0,3)}) ${val.slice(3)}`;
    else if (val.length > 0) val = `(${val}`;
    form.value.customer_phone = val;
}
</script>

<template>
    <div class="min-h-screen bg-[#0f0f1a] flex flex-col">
        <!-- Header -->
        <nav class="bg-[#0f0f1a]/90 backdrop-blur-md border-b border-white/10">
            <div class="max-w-lg mx-auto px-4 flex justify-between items-center h-16">
                <Link href="/locations" class="text-gray-400 hover:text-white text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Back
                </Link>
                <span class="text-white font-bold">{{ shop.name }}</span>
                <div class="w-12"></div>
            </div>
        </nav>

        <div class="flex-1 max-w-lg mx-auto w-full px-4 py-8">
            <!-- Progress bar -->
            <div v-if="step < 5" class="flex gap-2 mb-8">
                <div v-for="i in 4" :key="i" class="flex-1 h-1.5 rounded-full" :class="i <= step ? 'bg-[#D4A853]' : 'bg-gray-800'"></div>
            </div>

            <!-- Step 1: Name & Phone -->
            <div v-if="step === 1" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">Welcome!</h2>
                    <p class="text-gray-400 mt-1">Let's get you checked in</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                    <input v-model="form.customer_name" type="text" placeholder="John Smith" class="w-full px-4 py-3 bg-[#1a1a2e] border border-gray-700 rounded-xl text-white placeholder-gray-600 focus:border-[#D4A853] focus:ring-[#D4A853]" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                    <input :value="form.customer_phone" @input="formatPhone" type="tel" placeholder="(555) 123-4567" class="w-full px-4 py-3 bg-[#1a1a2e] border border-gray-700 rounded-xl text-white placeholder-gray-600 focus:border-[#D4A853] focus:ring-[#D4A853]" />
                    <p class="text-xs text-gray-600 mt-1">We'll text you when it's your turn</p>
                </div>
            </div>

            <!-- Step 2: Party Size -->
            <div v-if="step === 2" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">Party Size</h2>
                    <p class="text-gray-400 mt-1">How many people?</p>
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <button
                        v-for="n in 5" :key="n"
                        @click="form.party_size = n"
                        :class="form.party_size === n ? 'bg-[#D4A853] text-[#0f0f1a] border-[#D4A853]' : 'bg-[#1a1a2e] text-white border-gray-700 hover:border-[#D4A853]'"
                        class="aspect-square rounded-xl border text-xl font-bold transition"
                    >
                        {{ n }}
                    </button>
                </div>
            </div>

            <!-- Step 3: Service -->
            <div v-if="step === 3" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">Select Service</h2>
                    <p class="text-gray-400 mt-1">Optional — or just walk in</p>
                </div>
                <button
                    @click="form.service_id = null"
                    :class="form.service_id === null ? 'border-[#D4A853] bg-[#D4A853]/10' : 'border-gray-700'"
                    class="w-full p-4 rounded-xl border text-left transition"
                >
                    <div class="text-white font-medium">No preference</div>
                    <div class="text-gray-500 text-sm">I'll decide at the chair</div>
                </button>
                <button
                    v-for="service in shop.services"
                    :key="service.id"
                    @click="form.service_id = service.id"
                    :class="form.service_id === service.id ? 'border-[#D4A853] bg-[#D4A853]/10' : 'border-gray-700'"
                    class="w-full p-4 rounded-xl border text-left transition flex justify-between items-center"
                >
                    <div>
                        <div class="text-white font-medium">{{ service.name }}</div>
                        <div class="text-gray-500 text-sm">{{ service.duration_minutes }} min</div>
                    </div>
                    <div class="text-[#D4A853] font-bold">${{ Number(service.price).toFixed(2) }}</div>
                </button>
            </div>

            <!-- Step 4: Stylist -->
            <div v-if="step === 4" class="space-y-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">Choose Stylist</h2>
                    <p class="text-gray-400 mt-1">Select your preferred stylist</p>
                </div>
                <button
                    @click="form.stylist_preference = 'first_available'"
                    :class="form.stylist_preference === 'first_available' ? 'border-[#D4A853] bg-[#D4A853]/10' : 'border-gray-700'"
                    class="w-full p-4 rounded-xl border text-left transition"
                >
                    <div class="text-white font-medium">First Available</div>
                    <div class="text-gray-500 text-sm">Fastest wait time</div>
                </button>
                <button
                    v-for="staff in shop.active_staff"
                    :key="staff.id"
                    @click="form.stylist_preference = String(staff.id)"
                    :class="form.stylist_preference === String(staff.id) ? 'border-[#D4A853] bg-[#D4A853]/10' : 'border-gray-700'"
                    class="w-full p-4 rounded-xl border text-left transition flex items-center gap-4"
                >
                    <div class="w-12 h-12 rounded-full bg-[#D4A853]/20 flex items-center justify-center text-lg font-bold text-[#D4A853]">
                        {{ staff.name?.charAt(0) }}
                    </div>
                    <div>
                        <div class="text-white font-medium">{{ staff.name }}</div>
                        <div v-if="staff.title" class="text-gray-500 text-sm">{{ staff.title }}</div>
                    </div>
                </button>
            </div>

            <!-- Step 5: Success -->
            <div v-if="step === 5 && result" class="text-center py-8">
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">You're Checked In!</h2>
                <div class="bg-[#1a1a2e] rounded-2xl border border-gray-800 p-8 mt-8">
                    <div class="text-6xl font-black text-[#D4A853] mb-4">{{ result.queue_number }}</div>
                    <div class="text-gray-400 mb-2">Your queue number</div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="bg-[#16162a] rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">#{{ result.position }}</div>
                            <div class="text-xs text-gray-500">Position</div>
                        </div>
                        <div class="bg-[#16162a] rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">~{{ result.estimated_wait_minutes }} min</div>
                            <div class="text-xs text-gray-500">Est. Wait</div>
                        </div>
                    </div>
                </div>
                <Link :href="`/queue/status/${result.queue_number}`" class="mt-6 inline-block bg-[#D4A853] text-[#0f0f1a] px-8 py-3 rounded-xl font-bold hover:bg-[#e0b865] transition">
                    Track My Status
                </Link>
            </div>

            <!-- Error -->
            <div v-if="error" class="mt-4 bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                {{ error }}
            </div>

            <!-- Navigation Buttons -->
            <div v-if="step < 5" class="flex gap-3 mt-8">
                <button v-if="step > 1" @click="prevStep" class="px-6 py-3 border border-gray-700 text-gray-300 rounded-xl font-medium hover:border-gray-500 transition">
                    Back
                </button>
                <button
                    @click="nextStep"
                    :disabled="!canProceed || submitting"
                    :class="canProceed ? 'bg-[#D4A853] text-[#0f0f1a] hover:bg-[#e0b865]' : 'bg-gray-800 text-gray-600 cursor-not-allowed'"
                    class="flex-1 py-3 rounded-xl font-bold transition"
                >
                    {{ submitting ? 'Checking In...' : step === 4 ? 'Check In' : 'Continue' }}
                </button>
            </div>
        </div>
    </div>
</template>
