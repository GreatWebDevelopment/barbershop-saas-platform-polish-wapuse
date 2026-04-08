<template>
    <Head title="Loyalty Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Loyalty Program</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="bg-green-900/30 border border-green-800 text-green-400 rounded-lg px-4 py-3 text-sm">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Program Settings -->
                <div class="bg-[#16162a] border border-gray-800 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-100 mb-6">Program Configuration</h3>
                    <form @submit.prevent="saveSettings" class="space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-gray-100 font-medium">Enable Loyalty Program</div>
                                <div class="text-sm text-gray-400">Customers earn points on every purchase</div>
                            </div>
                            <button
                                type="button"
                                @click="settingsForm.loyalty_enabled = !settingsForm.loyalty_enabled"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                                :class="settingsForm.loyalty_enabled ? 'bg-[#D4A853]' : 'bg-gray-600'"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="settingsForm.loyalty_enabled ? 'translate-x-6' : 'translate-x-1'" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Points per Dollar</label>
                                <input
                                    v-model.number="settingsForm.loyalty_points_per_dollar"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-2.5"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Redemption Value ($ per 100pts)</label>
                                <input
                                    v-model.number="settingsForm.loyalty_redemption_value"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-2.5"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Referral Bonus Points</label>
                                <input
                                    v-model.number="settingsForm.referral_bonus_points"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-2.5"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="settingsForm.processing"
                                class="px-5 py-2.5 bg-[#D4A853] text-[#0f0f1a] font-semibold rounded-lg hover:bg-[#c49742] active:bg-[#b58831] disabled:opacity-50 transition"
                            >
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Reward Tiers -->
                <div class="bg-[#16162a] border border-gray-800 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-100 mb-6">Reward Tiers</h3>

                    <div v-if="rewards.length" class="space-y-3 mb-6">
                        <div
                            v-for="reward in rewards"
                            :key="reward.id"
                            class="flex items-center justify-between bg-[#1a1a2e] rounded-lg p-4 border border-gray-700"
                        >
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-[#D4A853]/20 flex items-center justify-center">
                                    <span class="text-[#D4A853] font-bold text-sm">{{ reward.points_required }}</span>
                                </div>
                                <div>
                                    <div class="text-gray-100 font-medium">{{ reward.name }}</div>
                                    <div class="text-sm text-gray-400">
                                        {{ reward.points_required }} pts =
                                        {{ reward.discount_type === 'fixed' ? '$' + Number(reward.discount_amount).toFixed(2) + ' off' : reward.discount_amount + '% off' }}
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="deleteReward(reward.id)"
                                class="text-gray-500 hover:text-red-400 transition"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-else class="text-center py-6 text-gray-500 text-sm mb-6">No reward tiers configured yet.</div>

                    <!-- Add Reward Form -->
                    <div class="border-t border-gray-700 pt-5">
                        <h4 class="text-sm font-medium text-gray-300 mb-3">Add Reward Tier</h4>
                        <form @submit.prevent="addReward" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                            <input
                                v-model="rewardForm.name"
                                type="text"
                                placeholder="Reward name"
                                class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                            />
                            <input
                                v-model.number="rewardForm.points_required"
                                type="number"
                                placeholder="Points"
                                min="1"
                                class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                            />
                            <input
                                v-model.number="rewardForm.discount_amount"
                                type="number"
                                step="0.01"
                                placeholder="Discount"
                                min="0.01"
                                class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                            />
                            <select
                                v-model="rewardForm.discount_type"
                                class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-300 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                            >
                                <option value="fixed">$ Fixed</option>
                                <option value="percentage">% Percentage</option>
                            </select>
                            <button
                                type="submit"
                                :disabled="rewardForm.processing"
                                class="px-4 py-2 bg-[#D4A853] text-[#0f0f1a] font-semibold rounded-lg hover:bg-[#c49742] text-sm disabled:opacity-50 transition"
                            >
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    shop: Object,
    rewards: Array,
});

const settingsForm = useForm({
    loyalty_enabled: props.shop.loyalty_enabled,
    loyalty_points_per_dollar: props.shop.loyalty_points_per_dollar,
    loyalty_redemption_value: props.shop.loyalty_redemption_value,
    referral_bonus_points: props.shop.referral_bonus_points,
});

const rewardForm = useForm({
    name: '',
    points_required: null,
    discount_amount: null,
    discount_type: 'fixed',
});

function saveSettings() {
    settingsForm.patch(route('settings.loyalty.update'));
}

function addReward() {
    rewardForm.post(route('settings.loyalty.rewards.store'), {
        onSuccess: () => rewardForm.reset(),
    });
}

function deleteReward(id) {
    if (confirm('Remove this reward tier?')) {
        router.delete(route('settings.loyalty.rewards.destroy', id));
    }
}
</script>
