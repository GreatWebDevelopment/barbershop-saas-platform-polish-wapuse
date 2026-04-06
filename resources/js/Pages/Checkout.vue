<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BarberPole from '@/Components/BarberPole.vue';

const props = defineProps({
    plan: String,
    planName: String,
    billing: String,
    price: Number,
    stripeKey: String,
});

const selectedMethod = ref('stripe');
const processing = ref(false);
const error = ref('');

const formattedPrice = (props.price / 100).toFixed(2);

function payWithStripe() {
    processing.value = true;
    error.value = '';
    router.post(route('subscription.stripe'), {
        plan: props.plan,
        billing: props.billing,
    }, {
        onError: (errors) => {
            error.value = errors.stripe || 'Something went wrong with Stripe.';
            processing.value = false;
        },
    });
}

async function payWithPaypal() {
    processing.value = true;
    error.value = '';

    try {
        const res = await fetch(route('subscription.paypal.create'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ plan: props.plan, billing: props.billing }),
        });

        const data = await res.json();

        // Simulate PayPal approval (in production, redirect to PayPal)
        const captureRes = await fetch(route('subscription.paypal.capture'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                subscription_id: data.subscription_id,
                paypal_order_id: 'PAYPAL-' + Date.now(),
            }),
        });

        if (captureRes.ok) {
            window.location.href = route('subscription.success') + '?plan=' + props.plan;
        } else {
            error.value = 'PayPal capture failed.';
        }
    } catch (e) {
        error.value = 'PayPal payment failed. Please try again.';
    } finally {
        processing.value = false;
    }
}

function submit() {
    if (selectedMethod.value === 'stripe') {
        payWithStripe();
    } else {
        payWithPaypal();
    }
}
</script>

<template>
    <Head title="Checkout" />

    <div class="min-h-screen bg-[#0f0f1a]">
        <!-- Nav -->
        <nav class="border-b border-white/5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
                <Link href="/" class="flex items-center gap-2">
                    <BarberPole size="md" />
                    <span class="text-lg font-bold text-white">Barber<span class="text-[#D4A853]">Pro</span></span>
                </Link>
                <Link :href="route('pricing')" class="text-sm text-gray-400 hover:text-white transition">
                    &larr; Back to Pricing
                </Link>
            </div>
        </nav>

        <div class="max-w-lg mx-auto py-16 px-4">
            <!-- Order Summary -->
            <div class="rounded-2xl bg-[#1a1a2e] border border-white/10 p-8 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <BarberPole size="sm" />
                    <h1 class="text-2xl font-bold text-white">Checkout</h1>
                </div>

                <div class="border-b border-white/10 pb-6 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-400">Plan</span>
                        <span class="text-white font-semibold">{{ planName }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-400">Billing</span>
                        <span class="text-white font-semibold capitalize">{{ billing }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total</span>
                        <span class="text-2xl font-bold text-[#D4A853]">${{ formattedPrice }}<span class="text-sm text-gray-500">/{{ billing === 'annual' ? 'mo' : 'month' }}</span></span>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <h2 class="text-lg font-semibold text-white mb-4">Payment Method</h2>

                <div class="space-y-3 mb-6">
                    <label
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition"
                        :class="selectedMethod === 'stripe'
                            ? 'border-[#D4A853] bg-[#D4A853]/10'
                            : 'border-white/10 hover:border-white/20'"
                    >
                        <input v-model="selectedMethod" type="radio" value="stripe" class="text-[#D4A853] focus:ring-[#D4A853] bg-[#16162a] border-gray-600" />
                        <div class="flex-1">
                            <div class="text-white font-medium">Credit / Debit Card</div>
                            <div class="text-sm text-gray-400">Pay securely with Stripe</div>
                        </div>
                        <svg class="w-8 h-8 text-[#635BFF]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                        </svg>
                    </label>

                    <label
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition"
                        :class="selectedMethod === 'paypal'
                            ? 'border-[#D4A853] bg-[#D4A853]/10'
                            : 'border-white/10 hover:border-white/20'"
                    >
                        <input v-model="selectedMethod" type="radio" value="paypal" class="text-[#D4A853] focus:ring-[#D4A853] bg-[#16162a] border-gray-600" />
                        <div class="flex-1">
                            <div class="text-white font-medium">PayPal</div>
                            <div class="text-sm text-gray-400">Pay with your PayPal account</div>
                        </div>
                        <svg class="w-8 h-8 text-[#00457C]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/>
                        </svg>
                    </label>
                </div>

                <p v-if="error" class="text-red-400 text-sm mb-4">{{ error }}</p>

                <button
                    @click="submit"
                    :disabled="processing"
                    class="w-full py-3 rounded-xl font-semibold text-lg transition disabled:opacity-50"
                    :class="selectedMethod === 'stripe'
                        ? 'bg-[#D4A853] text-[#0f0f1a] hover:bg-[#c09743]'
                        : 'bg-[#0070BA] text-white hover:bg-[#005ea6]'"
                >
                    <span v-if="processing">Processing...</span>
                    <span v-else-if="selectedMethod === 'stripe'">Pay ${{ formattedPrice }} with Card</span>
                    <span v-else>Pay ${{ formattedPrice }} with PayPal</span>
                </button>

                <p class="text-center text-xs text-gray-500 mt-4">
                    Secure payment powered by {{ selectedMethod === 'stripe' ? 'Stripe' : 'PayPal' }}.
                    Cancel anytime.
                </p>
            </div>
        </div>
    </div>
</template>
