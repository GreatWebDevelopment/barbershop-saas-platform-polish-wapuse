<template>
    <Head title="Appointments" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-[#f5f0e8] leading-tight">
                Appointments
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Tab Filters -->
                        <div class="mb-6 flex flex-wrap gap-2">
                            <button
                                v-for="tab in tabs"
                                :key="tab.value"
                                @click="switchTab(tab.value)"
                                :class="[
                                    'px-4 py-2 rounded-full text-sm font-medium transition',
                                    form.tab === tab.value
                                        ? 'bg-[#D4A853] text-gray-900'
                                        : 'bg-[#1a1a2e] text-gray-300 hover:bg-[#1a1a2e]/80'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </div>

                        <!-- Search, Status Filter, New Button -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search appointments..."
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-[#f5f0e8] placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @input="search"
                                />
                            </div>
                            <div class="w-full sm:w-48">
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-[#f5f0e8] focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @change="search"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="no-show">No-Show</option>
                                </select>
                            </div>
                            <Link
                                :href="route('appointments.create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                New Appointment
                            </Link>
                        </div>

                        <!-- Table -->
                        <div v-if="appointments.data.length" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-[#1a1a2e]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date/Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stylist</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#16162a] divide-y divide-gray-700">
                                    <tr v-for="appointment in appointments.data" :key="appointment.id" class="hover:bg-[#1a1a2e] transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#f5f0e8]">
                                            {{ formatDateTime(appointment.starts_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#f5f0e8]">
                                            {{ appointment.customer?.first_name }} {{ appointment.customer?.last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#f5f0e8]">
                                            {{ appointment.service?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#f5f0e8]">
                                            {{ appointment.staff?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#D4A853] font-semibold">
                                            ${{ appointment.price }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(appointment.status)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ appointment.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getPaymentClass(appointment.payment_status)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ appointment.payment_status || 'pending' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <Link
                                                    :href="route('appointments.edit', appointment.id)"
                                                    class="text-[#D4A853] hover:text-[#c49742]"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    v-if="appointment.status === 'pending'"
                                                    @click="changeStatus(appointment.id, 'confirmed')"
                                                    class="text-blue-400 hover:text-blue-300"
                                                >
                                                    Confirm
                                                </button>
                                                <button
                                                    v-if="appointment.status === 'pending' || appointment.status === 'confirmed'"
                                                    @click="changeStatus(appointment.id, 'completed')"
                                                    class="text-green-400 hover:text-green-300"
                                                >
                                                    Complete
                                                </button>
                                                <button
                                                    @click="deleteAppointment(appointment.id)"
                                                    class="text-red-400 hover:text-red-300"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12 text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-lg font-medium">No appointments found</p>
                            <p class="mt-1 text-sm">Try adjusting your search or filter criteria.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="appointments.links.length > 3" class="mt-6 flex justify-center space-x-1">
                            <Link
                                v-for="(link, index) in appointments.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 rounded-md text-sm font-medium',
                                    link.active
                                        ? 'bg-[#D4A853] text-gray-900'
                                        : 'bg-[#1a1a2e] text-gray-300 hover:bg-[#1a1a2e]/80',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    appointments: Object,
    filters: Object
});

const tabs = [
    { label: 'All', value: '' },
    { label: 'Today', value: 'today' },
    { label: 'Upcoming', value: 'upcoming' },
    { label: 'Past', value: 'past' },
];

const form = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    tab: props.filters?.tab || '',
});

const switchTab = (tab) => {
    form.value.tab = tab;
    applyFilters();
};

const search = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('appointments.index'), {
        search: form.value.search,
        status: form.value.status,
        tab: form.value.tab,
    }, {
        preserveState: true,
        replace: true
    });
};

const changeStatus = (id, status) => {
    router.put(route('appointments.update', id), { status }, {
        preserveState: true,
    });
};

const deleteAppointment = (id) => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        router.delete(route('appointments.destroy', id));
    }
};

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
};

const getStatusClass = (status) => {
    const classes = {
        completed: 'bg-green-900 text-green-300',
        pending: 'bg-yellow-900 text-yellow-300',
        confirmed: 'bg-blue-900 text-blue-300',
        cancelled: 'bg-red-900 text-red-300',
        'no-show': 'bg-gray-700 text-gray-400',
    };
    return classes[status] || 'bg-gray-700 text-gray-400';
};

const getPaymentClass = (status) => {
    const classes = {
        paid: 'bg-green-900 text-green-300',
        pending: 'bg-yellow-900 text-yellow-300',
        refunded: 'bg-purple-900 text-purple-300',
    };
    return classes[status] || 'bg-yellow-900 text-yellow-300';
};
</script>
