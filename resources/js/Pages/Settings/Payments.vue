<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    shop: Object,
});

const stripeTestMode = ref(false);
const paypalSandboxMode = ref(false);

const stripeForm = useForm({
    stripe_account_id: props.shop.stripe_account_id || '',
    stripe_enabled: props.shop.stripe_enabled || false,
});

const paypalForm = useForm({
    paypal_email: props.shop.paypal_email || '',
    paypal_client_id: props.shop.paypal_client_id || '',
    paypal_enabled: props.shop.paypal_enabled || false,
});

const existingMethods = props.shop.payment_methods || [];
const methodChecks = ref({
    cash: existingMethods.includes('cash'),
    card: existingMethods.includes('card'),
    paypal: existingMethods.includes('paypal'),
});

function saveStripe() {
    stripeForm.post(route('settings.payments.stripe'));
}

function savePaypal() {
    paypalForm.post(route('settings.payments.paypal'));
}

function saveMethods() {
    const methods = [];
    if (methodChecks.value.cash) methods.push('cash');
    if (methodChecks.value.card) methods.push('card');
    if (methodChecks.value.paypal) methods.push('paypal');
    router.post(route('settings.payments.methods'), { payment_methods: methods });
}
</script>

<template>
    <Head title="Payment Settings" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
            <h1 class="text-2xl font-bold text-[#f5f0e8]">Payment Settings</h1>

            <!-- Stripe Settings -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-[#f5f0e8]">Stripe Settings</h2>
                    <span class="flex items-center gap-2 text-sm text-gray-400">
                        <span
                            class="inline-block h-2.5 w-2.5 rounded-full"
                            :class="stripeForm.stripe_enabled ? 'bg-green-500' : 'bg-gray-500'"
                        ></span>
                        {{ stripeForm.stripe_enabled ? 'Connected' : 'Not Connected' }}
                    </span>
                </div>

                <form @submit.prevent="saveStripe" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Stripe Account ID</label>
                        <input
                            v-model="stripeForm.stripe_account_id"
                            type="text"
                            placeholder="acct_..."
                            class="w-full rounded-md border-gray-600 bg-[#16162a] text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <p v-if="stripeForm.errors.stripe_account_id" class="mt-1 text-sm text-red-400">{{ stripeForm.errors.stripe_account_id }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Test Mode</label>
                        <button
                            type="button"
                            @click="stripeTestMode = !stripeTestMode"
                            :class="stripeTestMode ? 'bg-[#D4A853]' : 'bg-gray-600'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200"
                        >
                            <span
                                :class="stripeTestMode ? 'translate-x-5' : 'translate-x-0.5'"
                                class="pointer-events-none mt-0.5 inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Enabled</label>
                        <button
                            type="button"
                            @click="stripeForm.stripe_enabled = !stripeForm.stripe_enabled"
                            :class="stripeForm.stripe_enabled ? 'bg-[#D4A853]' : 'bg-gray-600'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200"
                        >
                            <span
                                :class="stripeForm.stripe_enabled ? 'translate-x-5' : 'translate-x-0.5'"
                                class="pointer-events-none mt-0.5 inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="stripeForm.processing"
                            class="rounded-md bg-[#D4A853] px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-[#c49742] disabled:opacity-50"
                        >
                            Save Stripe Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- PayPal Settings -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-[#f5f0e8]">PayPal Settings</h2>
                    <span class="flex items-center gap-2 text-sm text-gray-400">
                        <span
                            class="inline-block h-2.5 w-2.5 rounded-full"
                            :class="paypalForm.paypal_enabled ? 'bg-green-500' : 'bg-gray-500'"
                        ></span>
                        {{ paypalForm.paypal_enabled ? 'Connected' : 'Not Connected' }}
                    </span>
                </div>

                <form @submit.prevent="savePaypal" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">PayPal Email</label>
                        <input
                            v-model="paypalForm.paypal_email"
                            type="text"
                            placeholder="payments@example.com"
                            class="w-full rounded-md border-gray-600 bg-[#16162a] text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <p v-if="paypalForm.errors.paypal_email" class="mt-1 text-sm text-red-400">{{ paypalForm.errors.paypal_email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">PayPal Client ID</label>
                        <input
                            v-model="paypalForm.paypal_client_id"
                            type="text"
                            placeholder="Client ID"
                            class="w-full rounded-md border-gray-600 bg-[#16162a] text-[#f5f0e8] focus:border-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <p v-if="paypalForm.errors.paypal_client_id" class="mt-1 text-sm text-red-400">{{ paypalForm.errors.paypal_client_id }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Sandbox Mode</label>
                        <button
                            type="button"
                            @click="paypalSandboxMode = !paypalSandboxMode"
                            :class="paypalSandboxMode ? 'bg-[#D4A853]' : 'bg-gray-600'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200"
                        >
                            <span
                                :class="paypalSandboxMode ? 'translate-x-5' : 'translate-x-0.5'"
                                class="pointer-events-none mt-0.5 inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-300">Enabled</label>
                        <button
                            type="button"
                            @click="paypalForm.paypal_enabled = !paypalForm.paypal_enabled"
                            :class="paypalForm.paypal_enabled ? 'bg-[#D4A853]' : 'bg-gray-600'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200"
                        >
                            <span
                                :class="paypalForm.paypal_enabled ? 'translate-x-5' : 'translate-x-0.5'"
                                class="pointer-events-none mt-0.5 inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="paypalForm.processing"
                            class="rounded-md bg-[#D4A853] px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-[#c49742] disabled:opacity-50"
                        >
                            Save PayPal Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Accepted Payment Methods -->
            <div class="rounded-lg bg-[#1a1a2e] p-6 shadow">
                <h2 class="text-lg font-semibold text-[#f5f0e8] mb-6">Accepted Payment Methods</h2>

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
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="methodChecks.paypal"
                            type="checkbox"
                            class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]"
                        />
                        <span class="text-sm text-gray-300">PayPal</span>
                    </label>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="false"
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
