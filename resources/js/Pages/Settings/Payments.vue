<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    stripe: Object,
    paypal: Object,
    shop: Object,
});

const flash = computed(() => usePage().props.flash || {});

const existingMethods = props.shop?.payment_methods || [];
const methodChecks = ref({
    cash: existingMethods.includes('cash'),
    card: existingMethods.includes('card'),
    paypal: existingMethods.includes('paypal'),
});

function saveMethods() {
    const methods = [];
    if (methodChecks.value.cash) methods.push('cash');
    if (methodChecks.value.card) methods.push('card');
    if (methodChecks.value.paypal) methods.push('paypal');
    router.post(route('settings.payments.methods'), { payment_methods: methods });
}

function disconnectStripe() {
    if (confirm('Are you sure you want to disconnect your Stripe account?')) {
        router.post(route('settings.payments.stripe.disconnect'));
    }
}

function disconnectPaypal() {
    if (confirm('Are you sure you want to disconnect your PayPal account?')) {
        router.post(route('settings.payments.paypal.disconnect'));
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Payment Settings" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-[#f5f0e8]">Payment Settings</h1>
                <p class="mt-1 text-sm text-gray-400">Connect your payment accounts to start accepting payments from customers.</p>
            </div>

            <!-- Flash Messages -->
            <div v-if="flash.success" class="rounded-lg bg-green-900/30 border border-green-700 p-4">
                <p class="text-sm text-green-400">{{ flash.success }}</p>
            </div>
            <div v-if="flash.error" class="rounded-lg bg-red-900/30 border border-red-700 p-4">
                <p class="text-sm text-red-400">{{ flash.error }}</p>
            </div>

            <!-- Stripe Connect -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow border border-gray-800">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#635bff] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-[#f5f0e8]">Stripe</h2>
                            <p class="text-sm text-gray-400">Accept credit card payments</p>
                        </div>
                    </div>

                    <span v-if="stripe.connected" class="inline-flex items-center gap-1.5 rounded-full bg-green-900/40 border border-green-700 px-3 py-1 text-xs font-medium text-green-400">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        Connected
                    </span>
                    <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-gray-800 border border-gray-700 px-3 py-1 text-xs font-medium text-gray-400">
                        <span class="h-2 w-2 rounded-full bg-gray-500"></span>
                        Not Connected
                    </span>
                </div>

                <!-- Connected State -->
                <div v-if="stripe.connected" class="space-y-4">
                    <div class="rounded-lg bg-[#16162a] p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Account ID</span>
                            <span class="text-[#f5f0e8] font-mono text-xs">{{ stripe.account_id }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Connected</span>
                            <span class="text-[#f5f0e8]">{{ formatDate(stripe.connected_at) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Mode</span>
                            <span :class="stripe.livemode ? 'text-green-400' : 'text-yellow-400'" class="font-medium">
                                {{ stripe.livemode ? 'Live' : 'Test' }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="disconnectStripe"
                        class="text-sm text-red-400 hover:text-red-300 transition-colors"
                    >
                        Disconnect Stripe Account
                    </button>
                </div>

                <!-- Disconnected State -->
                <div v-else>
                    <a
                        :href="route('settings.payments.stripe.connect')"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#635bff] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#5851db] transition-colors"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                        </svg>
                        Connect with Stripe
                    </a>
                    <p class="mt-3 text-xs text-gray-500">You'll be redirected to Stripe to authorize your account.</p>
                </div>
            </div>

            <!-- PayPal Connect -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow border border-gray-800">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#003087] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c1.145.192 2.478.746 2.478 3.094 0 3.628-3.124 5.965-7.012 5.965h-1.96c-.525 0-.97.382-1.05.9L12.1 22.02a.641.641 0 0 1-.633.54H8.178l-.08.503a.641.641 0 0 0 .633.74h3.18c.524 0 .968-.382 1.05-.9l.86-5.46c.082-.518.526-.9 1.05-.9h1.172c4.07 0 7.252-2.594 7.906-6.834.263-1.704-.048-3.043-.727-3.792z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-[#f5f0e8]">PayPal</h2>
                            <p class="text-sm text-gray-400">Accept PayPal payments</p>
                        </div>
                    </div>

                    <span v-if="paypal.connected" class="inline-flex items-center gap-1.5 rounded-full bg-green-900/40 border border-green-700 px-3 py-1 text-xs font-medium text-green-400">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        Connected
                    </span>
                    <span v-else class="inline-flex items-center gap-1.5 rounded-full bg-gray-800 border border-gray-700 px-3 py-1 text-xs font-medium text-gray-400">
                        <span class="h-2 w-2 rounded-full bg-gray-500"></span>
                        Not Connected
                    </span>
                </div>

                <!-- Connected State -->
                <div v-if="paypal.connected" class="space-y-4">
                    <div class="rounded-lg bg-[#16162a] p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Merchant ID</span>
                            <span class="text-[#f5f0e8] font-mono text-xs">{{ paypal.merchant_id }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Connected</span>
                            <span class="text-[#f5f0e8]">{{ formatDate(paypal.connected_at) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Payments Receivable</span>
                            <span :class="paypal.payments_receivable ? 'text-green-400' : 'text-yellow-400'" class="font-medium">
                                {{ paypal.payments_receivable ? 'Yes' : 'Pending' }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="disconnectPaypal"
                        class="text-sm text-red-400 hover:text-red-300 transition-colors"
                    >
                        Disconnect PayPal Account
                    </button>
                </div>

                <!-- Disconnected State -->
                <div v-else>
                    <a
                        :href="route('settings.payments.paypal.connect')"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0070ba] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#005ea6] transition-colors"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c1.145.192 2.478.746 2.478 3.094 0 3.628-3.124 5.965-7.012 5.965h-1.96c-.525 0-.97.382-1.05.9L12.1 22.02a.641.641 0 0 1-.633.54H8.178l-.08.503a.641.641 0 0 0 .633.74h3.18c.524 0 .968-.382 1.05-.9l.86-5.46c.082-.518.526-.9 1.05-.9h1.172c4.07 0 7.252-2.594 7.906-6.834.263-1.704-.048-3.043-.727-3.792z"/>
                        </svg>
                        Connect with PayPal
                    </a>
                    <p class="mt-3 text-xs text-gray-500">You'll be redirected to PayPal to complete onboarding.</p>
                </div>
            </div>

            <!-- Accepted Payment Methods -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow border border-gray-800">
                <h2 class="text-lg font-semibold text-[#f5f0e8] mb-2">Accepted Payment Methods</h2>
                <p class="text-sm text-gray-400 mb-6">Choose which payment methods your customers can use for appointments.</p>

                <form @submit.prevent="saveMethods" class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="methodChecks.cash"
                            type="checkbox"
                            class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <span class="text-sm text-gray-300">Cash</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="methodChecks.card"
                            type="checkbox"
                            class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <span class="text-sm text-gray-300">Card</span>
                        <span v-if="!stripe.connected" class="text-xs text-yellow-500">(Requires Stripe connection)</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="methodChecks.paypal"
                            type="checkbox"
                            class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <span class="text-sm text-gray-300">PayPal</span>
                        <span v-if="!paypal.connected" class="text-xs text-yellow-500">(Requires PayPal connection)</span>
                    </label>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="rounded-md bg-[#D4A853] px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-[#c49742] disabled:opacity-50"
                        >
                            Save Payment Methods
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
