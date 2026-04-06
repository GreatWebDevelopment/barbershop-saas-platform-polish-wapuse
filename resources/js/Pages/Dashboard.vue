<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    todaysAppointments: Array,
    topStylist: Object,
    recentCustomers: Array,
    revenueByDay: Array,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const statusConfig = {
    confirmed: { label: 'Confirmed', color: 'bg-green-500' },
    pending: { label: 'Pending', color: 'bg-yellow-500' },
    cancelled: { label: 'Cancelled', color: 'bg-red-500' },
    'no-show': { label: 'No Show', color: 'bg-gray-500' },
    completed: { label: 'Completed', color: 'bg-blue-500' },
};

const maxRevenue = computed(() => {
    if (!props.revenueByDay || props.revenueByDay.length === 0) return 1;
    return Math.max(...props.revenueByDay.map(day => day.revenue));
});

const getBarHeight = (revenue) => {
    if (maxRevenue.value === 0) return 0;
    return (revenue / maxRevenue.value) * 100;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <div class="flex flex-col sm:flex-row gap-3">
                    <Link
                        :href="route('appointments.create')"
                        class="inline-flex items-center justify-center px-6 py-3 bg-[#D4A853] text-[#1a1a2e] font-semibold rounded-lg hover:bg-[#c49843] transition-colors"
                    >
                        New Appointment
                    </Link>
                    <Link
                        :href="route('appointments.create')"
                        class="inline-flex items-center justify-center px-6 py-3 bg-[#16162a] text-[#D4A853] font-semibold rounded-lg hover:bg-[#1a1a2e] border-2 border-[#D4A853] transition-colors"
                    >
                        Add Walk-in
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                    <div class="text-[#f5f0e8] text-sm font-medium mb-2">Today's Appointments</div>
                    <div class="text-[#D4A853] text-3xl font-bold">{{ stats.todaysAppointmentCount }}</div>
                </div>
                <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                    <div class="text-[#f5f0e8] text-sm font-medium mb-2">Week Revenue</div>
                    <div class="text-[#D4A853] text-3xl font-bold">{{ formatCurrency(stats.weekRevenue) }}</div>
                </div>
                <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                    <div class="text-[#f5f0e8] text-sm font-medium mb-2">Month Revenue</div>
                    <div class="text-[#D4A853] text-3xl font-bold">{{ formatCurrency(stats.monthRevenue) }}</div>
                </div>
                <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                    <div class="text-[#f5f0e8] text-sm font-medium mb-2">Total Customers</div>
                    <div class="text-[#D4A853] text-3xl font-bold">{{ stats.totalCustomers.toLocaleString() }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Revenue Chart -->
                    <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                        <h2 class="text-xl font-bold text-[#f5f0e8] mb-6">Revenue Last 7 Days</h2>
                        <div class="space-y-2">
                            <div class="flex items-end justify-between h-48 gap-2">
                                <div
                                    v-for="day in revenueByDay"
                                    :key="day.date"
                                    class="flex-1 flex flex-col items-center"
                                >
                                    <div class="w-full flex items-end justify-center h-full">
                                        <div
                                            class="w-full bg-[#D4A853] rounded-t-md hover:bg-[#c49843] transition-all relative group"
                                            :style="{ height: getBarHeight(day.revenue) + '%', minHeight: day.revenue > 0 ? '4px' : '0' }"
                                        >
                                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-[#16162a] text-[#D4A853] text-xs font-semibold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                                {{ formatCurrency(day.revenue) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[#f5f0e8] text-xs mt-2 text-center">
                                        {{ formatDate(day.date) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Schedule -->
                    <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                        <h2 class="text-xl font-bold text-[#f5f0e8] mb-6">Today's Schedule</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full" v-if="todaysAppointments.length > 0">
                                <thead>
                                    <tr class="border-b border-[#16162a]">
                                        <th class="text-left text-[#D4A853] text-sm font-semibold pb-3">Time</th>
                                        <th class="text-left text-[#D4A853] text-sm font-semibold pb-3">Customer</th>
                                        <th class="text-left text-[#D4A853] text-sm font-semibold pb-3 hidden sm:table-cell">Service</th>
                                        <th class="text-left text-[#D4A853] text-sm font-semibold pb-3 hidden md:table-cell">Stylist</th>
                                        <th class="text-left text-[#D4A853] text-sm font-semibold pb-3">Status</th>
                                        <th class="text-right text-[#D4A853] text-sm font-semibold pb-3 hidden sm:table-cell">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="appointment in todaysAppointments"
                                        :key="appointment.id"
                                        class="border-b border-[#16162a] hover:bg-[#16162a] transition-colors"
                                    >
                                        <td class="py-4 text-[#f5f0e8] text-sm">
                                            {{ formatTime(appointment.starts_at) }}
                                        </td>
                                        <td class="py-4 text-[#f5f0e8] text-sm">
                                            {{ appointment.customer.first_name }} {{ appointment.customer.last_name }}
                                            <span v-if="appointment.is_walkin" class="ml-2 text-xs text-[#D4A853]">(Walk-in)</span>
                                        </td>
                                        <td class="py-4 text-[#f5f0e8] text-sm hidden sm:table-cell">
                                            {{ appointment.service.name }}
                                        </td>
                                        <td class="py-4 text-[#f5f0e8] text-sm hidden md:table-cell">
                                            {{ appointment.staff.name }}
                                        </td>
                                        <td class="py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                                :class="statusConfig[appointment.status]?.color || 'bg-gray-500'"
                                            >
                                                {{ statusConfig[appointment.status]?.label || appointment.status }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-[#f5f0e8] text-sm text-right hidden sm:table-cell">
                                            {{ formatCurrency(appointment.price) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else class="text-center py-8 text-[#f5f0e8]">
                                No appointments scheduled for today
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Top Stylist -->
                    <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg" v-if="topStylist">
                        <h2 class="text-xl font-bold text-[#f5f0e8] mb-4">Top Stylist</h2>
                        <div class="text-center">
                            <div class="w-20 h-20 bg-[#D4A853] rounded-full mx-auto mb-4 flex items-center justify-center">
                                <span class="text-[#1a1a2e] text-2xl font-bold">
                                    {{ topStylist.name.charAt(0) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-[#D4A853]">{{ topStylist.name }}</h3>
                            <p class="text-[#f5f0e8] text-sm mb-3">{{ topStylist.title }}</p>
                            <div class="bg-[#16162a] rounded-lg p-3">
                                <div class="text-[#D4A853] text-2xl font-bold">{{ topStylist.appointments_count }}</div>
                                <div class="text-[#f5f0e8] text-xs">Appointments This Week</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Customers -->
                    <div class="bg-[#1a1a2e] rounded-lg p-6 shadow-lg">
                        <h2 class="text-xl font-bold text-[#f5f0e8] mb-4">Recent Customers</h2>
                        <div class="space-y-4" v-if="recentCustomers.length > 0">
                            <div
                                v-for="customer in recentCustomers"
                                :key="customer.email"
                                class="border-b border-[#16162a] pb-4 last:border-b-0 last:pb-0"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-[#f5f0e8] font-medium">
                                            {{ customer.first_name }} {{ customer.last_name }}
                                        </h3>
                                        <p class="text-[#f5f0e8] text-xs opacity-75 mt-1">
                                            {{ customer.email }}
                                        </p>
                                        <p class="text-[#f5f0e8] text-xs opacity-75 mt-1">
                                            Last visit: {{ formatDate(customer.last_visit) }}
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        <div class="bg-[#D4A853] text-[#1a1a2e] text-xs font-bold px-2 py-1 rounded">
                                            {{ customer.loyalty_points }} pts
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-[#f5f0e8]">
                            No recent customers
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
