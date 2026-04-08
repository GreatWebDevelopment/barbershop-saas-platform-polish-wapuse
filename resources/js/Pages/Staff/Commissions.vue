<template>
    <Head :title="`${staffMember.name} — Commissions`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('staff.index')" class="text-gray-400 hover:text-[#D4A853] transition">
                    &larr; Staff
                </Link>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    {{ staffMember.name }} — Commissions ({{ staffMember.commission_percent }}%)
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div v-for="card in summaryCards" :key="card.label" class="bg-[#16162a] rounded-lg p-5">
                        <p class="text-sm text-gray-400">{{ card.label }}</p>
                        <p class="text-2xl font-bold text-[#D4A853] mt-1">${{ card.value.toFixed(2) }}</p>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="bg-[#16162a] shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-100 mb-4">Completed Appointments</h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-700">
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Date</th>
                                        <th class="text-left py-3 px-4 text-gray-400 font-medium">Service</th>
                                        <th class="text-right py-3 px-4 text-gray-400 font-medium">Price</th>
                                        <th class="text-right py-3 px-4 text-gray-400 font-medium">Commission</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="appt in appointments"
                                        :key="appt.id"
                                        class="border-b border-gray-800 hover:bg-[#1a1a2e]/50"
                                    >
                                        <td class="py-3 px-4 text-gray-300">{{ appt.date }}</td>
                                        <td class="py-3 px-4 text-gray-100">{{ appt.service }}</td>
                                        <td class="py-3 px-4 text-right text-gray-300">${{ appt.price.toFixed(2) }}</td>
                                        <td class="py-3 px-4 text-right text-[#D4A853] font-semibold">${{ appt.commission.toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p v-if="appointments.length === 0" class="text-center py-12 text-gray-500">
                            No completed appointments yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    staffMember: Object,
    appointments: Array,
    totals: Object,
});

const summaryCards = computed(() => [
    { label: 'Today', value: props.totals.daily },
    { label: 'This Week', value: props.totals.weekly },
    { label: 'This Month', value: props.totals.monthly },
    { label: 'All Time', value: props.totals.all_time },
]);
</script>
