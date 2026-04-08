<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler, ArcElement } from 'chart.js';
import { Line, Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler, ArcElement);

const props = defineProps({
    stats: Object,
    todaysAppointments: Array,
    revenueByDay: Array,
    revenueByMethod: Object,
    stylistPerformance: Array,
    popularServices: Array,
    heatmapData: Array,
    customerMetrics: Object,
});

const sortColumn = ref('revenue');
const sortDirection = ref('desc');

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
};

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
};

const statusConfig = {
    confirmed: { label: 'Confirmed', color: 'bg-green-500' },
    pending: { label: 'Pending', color: 'bg-yellow-500' },
    cancelled: { label: 'Cancelled', color: 'bg-red-500' },
    'no-show': { label: 'No Show', color: 'bg-gray-500' },
    completed: { label: 'Completed', color: 'bg-blue-500' },
};

// Revenue Line Chart
const revenueChartData = computed(() => ({
    labels: props.revenueByDay.map(d => d.date),
    datasets: [{
        label: 'Revenue',
        data: props.revenueByDay.map(d => d.revenue),
        borderColor: '#D4A853',
        backgroundColor: 'rgba(212, 168, 83, 0.1)',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#D4A853',
        pointBorderColor: '#D4A853',
        pointRadius: 2,
        pointHoverRadius: 5,
    }],
}));

const revenueChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1a1a2e',
            titleColor: '#D4A853',
            bodyColor: '#f5f0e8',
            borderColor: '#D4A853',
            borderWidth: 1,
            callbacks: { label: (ctx) => '$' + ctx.parsed.y.toLocaleString() },
        },
    },
    scales: {
        x: { ticks: { color: '#888', maxTicksLimit: 10, font: { size: 10 } }, grid: { display: false } },
        y: { ticks: { color: '#888', callback: (v) => '$' + v.toLocaleString() }, grid: { color: 'rgba(255,255,255,0.05)' } },
    },
};

// Payment Method Doughnut
const methodColors = { cash: '#4ade80', card: '#60a5fa', paypal: '#f59e0b', unknown: '#6b7280' };
const paymentChartData = computed(() => {
    const methods = props.revenueByMethod || {};
    const labels = Object.keys(methods).map(k => k.charAt(0).toUpperCase() + k.slice(1));
    return {
        labels,
        datasets: [{
            data: Object.values(methods).map(v => v.total),
            backgroundColor: Object.keys(methods).map(k => methodColors[k] || '#6b7280'),
            borderWidth: 0,
        }],
    };
});
const paymentChartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { color: '#ccc', padding: 12, font: { size: 11 } } },
        tooltip: { backgroundColor: '#1a1a2e', bodyColor: '#f5f0e8', callbacks: { label: (ctx) => ctx.label + ': $' + ctx.parsed.toLocaleString() } },
    },
};

// Popular Services Bar Chart
const servicesChartData = computed(() => ({
    labels: (props.popularServices || []).map(s => s.name),
    datasets: [{
        label: 'Bookings',
        data: (props.popularServices || []).map(s => s.booking_count),
        backgroundColor: '#D4A853',
        borderRadius: 4,
    }],
}));
const servicesChartOptions = {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1a1a2e', bodyColor: '#f5f0e8' } },
    scales: {
        x: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.05)' } },
        y: { ticks: { color: '#ccc', font: { size: 11 } }, grid: { display: false } },
    },
};

// Sortable stylist table
const sortedStylists = computed(() => {
    const data = [...(props.stylistPerformance || [])];
    return data.sort((a, b) => {
        const aVal = a[sortColumn.value] ?? 0;
        const bVal = b[sortColumn.value] ?? 0;
        return sortDirection.value === 'desc' ? bVal - aVal : aVal - bVal;
    });
});

const toggleSort = (col) => {
    if (sortColumn.value === col) {
        sortDirection.value = sortDirection.value === 'desc' ? 'asc' : 'desc';
    } else {
        sortColumn.value = col;
        sortDirection.value = 'desc';
    }
};

const sortIcon = (col) => {
    if (sortColumn.value !== col) return '';
    return sortDirection.value === 'desc' ? ' \u25BC' : ' \u25B2';
};

// Heatmap helpers
const heatmapHours = Array.from({ length: 15 }, (_, i) => i + 7);
const heatmapMax = computed(() => {
    let max = 1;
    (props.heatmapData || []).forEach(d => d.hours.forEach(h => { if (h > max) max = h; }));
    return max;
});
const heatColor = (val) => {
    if (val === 0) return 'background-color: rgba(212, 168, 83, 0.05)';
    const intensity = Math.min(val / heatmapMax.value, 1);
    return `background-color: rgba(212, 168, 83, ${0.15 + intensity * 0.85})`;
};
const formatHour = (h) => {
    if (h === 0 || h === 12) return '12' + (h === 0 ? 'a' : 'p');
    return (h > 12 ? h - 12 : h) + (h >= 12 ? 'p' : 'a');
};

const changePct = (current, previous) => {
    if (previous === 0) return current > 0 ? '+100%' : '--';
    const pct = Math.round(((current - previous) / previous) * 100);
    return (pct >= 0 ? '+' : '') + pct + '%';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-[#0f0f1a] py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white">Analytics Dashboard</h1>
                    <p class="text-gray-400 text-sm mt-1">Performance overview for your shop</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('appointments.create')" class="px-5 py-2.5 bg-[#D4A853] text-[#0f0f1a] font-semibold rounded-lg hover:bg-[#c49843] transition-colors text-sm">
                        New Appointment
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-[#1a1a2e] rounded-xl p-5 border border-white/5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-400 text-sm">Today's Revenue</span>
                        <span class="w-8 h-8 rounded-lg bg-[#D4A853]/10 flex items-center justify-center text-[#D4A853]">$</span>
                    </div>
                    <div class="text-white text-2xl font-bold">{{ formatCurrency(stats.todaysRevenue) }}</div>
                </div>

                <div class="bg-[#1a1a2e] rounded-xl p-5 border border-white/5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-400 text-sm">Today's Appointments</span>
                        <span class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                    </div>
                    <div class="text-white text-2xl font-bold">{{ stats.todayBreakdown.total }}</div>
                    <div class="flex gap-3 mt-2 text-xs">
                        <span class="text-green-400">{{ stats.todayBreakdown.completed }} done</span>
                        <span class="text-blue-400">{{ stats.todayBreakdown.upcoming }} upcoming</span>
                        <span class="text-red-400" v-if="stats.todayBreakdown.cancelled">{{ stats.todayBreakdown.cancelled }} cancelled</span>
                    </div>
                </div>

                <div class="bg-[#1a1a2e] rounded-xl p-5 border border-white/5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-400 text-sm">New Customers</span>
                        <span class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </span>
                    </div>
                    <div class="text-white text-2xl font-bold">{{ stats.newCustomersThisMonth }}</div>
                    <div class="text-gray-500 text-xs mt-2">This month</div>
                </div>

                <div class="bg-[#1a1a2e] rounded-xl p-5 border border-white/5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-400 text-sm">Month Revenue</span>
                        <span class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <div class="text-white text-2xl font-bold">{{ formatCurrency(stats.monthRevenue) }}</div>
                </div>
            </div>

            <!-- Revenue Chart + Payment Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">Revenue — Last 30 Days</h2>
                    <div class="h-64">
                        <Line :data="revenueChartData" :options="revenueChartOptions" />
                    </div>
                </div>
                <div class="bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">By Payment Method</h2>
                    <div class="h-52">
                        <Doughnut :data="paymentChartData" :options="paymentChartOptions" />
                    </div>
                    <div class="mt-4 space-y-2">
                        <div v-for="(val, key) in revenueByMethod" :key="key" class="flex justify-between text-sm">
                            <span class="text-gray-400 capitalize">{{ key }}</span>
                            <span class="text-white">{{ formatCurrency(val.total) }} <span class="text-gray-500">({{ val.count }})</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stylist Performance -->
            <div class="bg-[#1a1a2e] rounded-xl p-6 border border-white/5 mb-8">
                <h2 class="text-lg font-semibold text-white mb-4">Stylist Performance — This Month</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="text-left py-3 text-gray-400 font-medium">Stylist</th>
                                <th class="text-right py-3 text-gray-400 font-medium cursor-pointer hover:text-[#D4A853] select-none" @click="toggleSort('appointments_completed')">
                                    Appts{{ sortIcon('appointments_completed') }}
                                </th>
                                <th class="text-right py-3 text-gray-400 font-medium cursor-pointer hover:text-[#D4A853] select-none" @click="toggleSort('revenue')">
                                    Revenue{{ sortIcon('revenue') }}
                                </th>
                                <th class="text-right py-3 text-gray-400 font-medium cursor-pointer hover:text-[#D4A853] select-none hidden md:table-cell" @click="toggleSort('avg_service_minutes')">
                                    Avg Time{{ sortIcon('avg_service_minutes') }}
                                </th>
                                <th class="text-right py-3 text-gray-400 font-medium cursor-pointer hover:text-[#D4A853] select-none hidden sm:table-cell" @click="toggleSort('commission_earned')">
                                    Commission{{ sortIcon('commission_earned') }}
                                </th>
                                <th class="text-right py-3 text-gray-400 font-medium hidden lg:table-cell">vs Last Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stylist in sortedStylists" :key="stylist.id" class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#D4A853]/20 flex items-center justify-center text-[#D4A853] text-xs font-bold">
                                            {{ stylist.name.charAt(0) }}
                                        </div>
                                        <span class="text-white">{{ stylist.name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-right text-gray-300">{{ stylist.appointments_completed }}</td>
                                <td class="py-3 text-right text-white font-medium">{{ formatCurrency(stylist.revenue) }}</td>
                                <td class="py-3 text-right text-gray-300 hidden md:table-cell">{{ stylist.avg_service_minutes }}m</td>
                                <td class="py-3 text-right text-[#D4A853] hidden sm:table-cell">{{ formatCurrency(stylist.commission_earned) }}</td>
                                <td class="py-3 text-right hidden lg:table-cell">
                                    <span :class="stylist.revenue >= stylist.last_month_revenue ? 'text-green-400' : 'text-red-400'" class="text-xs">
                                        {{ changePct(stylist.revenue, stylist.last_month_revenue) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="!stylistPerformance || stylistPerformance.length === 0" class="text-center py-8 text-gray-500">No staff data available</div>
                </div>
            </div>

            <!-- Popular Services + Peak Hours -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Popular Services -->
                <div class="bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">Popular Services</h2>
                    <div class="h-72" v-if="popularServices && popularServices.length > 0">
                        <Bar :data="servicesChartData" :options="servicesChartOptions" />
                    </div>
                    <div class="mt-4 space-y-2">
                        <div v-for="service in popularServices" :key="service.name" class="flex justify-between text-sm">
                            <span class="text-gray-300">{{ service.name }}</span>
                            <span class="text-gray-400">{{ service.booking_count }} bookings &middot; {{ formatCurrency(service.revenue) }}</span>
                        </div>
                    </div>
                    <div v-if="!popularServices || popularServices.length === 0" class="text-center py-8 text-gray-500">No service data yet</div>
                </div>

                <!-- Peak Hours Heatmap -->
                <div class="bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">Peak Hours</h2>
                    <p class="text-gray-500 text-xs mb-4">Appointment density over the last 4 weeks</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="pb-2 text-gray-500 text-left w-10"></th>
                                    <th v-for="h in heatmapHours" :key="h" class="pb-2 text-gray-500 text-center px-0.5">{{ formatHour(h) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in heatmapData" :key="row.day">
                                    <td class="py-0.5 text-gray-400 pr-2 font-medium">{{ row.day }}</td>
                                    <td v-for="(val, i) in row.hours" :key="i" class="py-0.5 px-0.5">
                                        <div
                                            class="w-full h-6 rounded-sm flex items-center justify-center text-[10px] transition-colors"
                                            :style="heatColor(val)"
                                            :class="val > 0 ? 'text-white/80' : 'text-transparent'"
                                            :title="row.day + ' ' + formatHour(heatmapHours[i]) + ': ' + val + ' bookings'"
                                        >
                                            {{ val > 0 ? val : '' }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center gap-2 mt-4 text-xs text-gray-500">
                        <span>Less</span>
                        <div class="flex gap-0.5">
                            <div class="w-4 h-3 rounded-sm" style="background-color: rgba(212,168,83,0.1)"></div>
                            <div class="w-4 h-3 rounded-sm" style="background-color: rgba(212,168,83,0.35)"></div>
                            <div class="w-4 h-3 rounded-sm" style="background-color: rgba(212,168,83,0.6)"></div>
                            <div class="w-4 h-3 rounded-sm" style="background-color: rgba(212,168,83,0.85)"></div>
                            <div class="w-4 h-3 rounded-sm" style="background-color: rgba(212,168,83,1)"></div>
                        </div>
                        <span>More</span>
                    </div>
                </div>
            </div>

            <!-- Customer Metrics + Today's Schedule -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Customer Metrics -->
                <div class="bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">Customer Metrics</h2>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Total Customers</span>
                            <span class="text-white font-semibold">{{ customerMetrics.total.toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">New This Month</span>
                            <span class="text-green-400 font-semibold">+{{ customerMetrics.newThisMonth }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Returning Rate</span>
                            <span class="text-[#D4A853] font-semibold">{{ customerMetrics.returningRate }}%</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Top Customers by Spend</h3>
                    <div class="space-y-3">
                        <div v-for="(customer, i) in customerMetrics.topCustomers" :key="i" class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-[#D4A853]/20 flex items-center justify-center text-[#D4A853] text-[10px] font-bold">{{ i + 1 }}</span>
                                <span class="text-gray-300 text-sm">{{ customer.name }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-white text-sm font-medium">{{ formatCurrency(customer.total_spent) }}</div>
                                <div class="text-gray-500 text-xs">{{ customer.visit_count }} visits</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="lg:col-span-2 bg-[#1a1a2e] rounded-xl p-6 border border-white/5">
                    <h2 class="text-lg font-semibold text-white mb-4">Today's Schedule</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" v-if="todaysAppointments && todaysAppointments.length > 0">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="text-left py-3 text-gray-400 font-medium">Time</th>
                                    <th class="text-left py-3 text-gray-400 font-medium">Customer</th>
                                    <th class="text-left py-3 text-gray-400 font-medium hidden sm:table-cell">Service</th>
                                    <th class="text-left py-3 text-gray-400 font-medium hidden md:table-cell">Stylist</th>
                                    <th class="text-left py-3 text-gray-400 font-medium">Status</th>
                                    <th class="text-right py-3 text-gray-400 font-medium hidden sm:table-cell">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="appt in todaysAppointments" :key="appt.id" class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">
                                    <td class="py-3 text-gray-300">{{ formatTime(appt.starts_at) }}</td>
                                    <td class="py-3 text-white">
                                        {{ appt.customer?.first_name }} {{ appt.customer?.last_name }}
                                        <span v-if="appt.is_walkin" class="ml-1 text-xs text-[#D4A853]">(Walk-in)</span>
                                    </td>
                                    <td class="py-3 text-gray-300 hidden sm:table-cell">{{ appt.service?.name }}</td>
                                    <td class="py-3 text-gray-300 hidden md:table-cell">{{ appt.staff?.name }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium text-white" :class="statusConfig[appt.status]?.color || 'bg-gray-500'">
                                            {{ statusConfig[appt.status]?.label || appt.status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-white text-right hidden sm:table-cell">{{ formatCurrency(appt.price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="text-center py-12 text-gray-500">No appointments scheduled for today</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
