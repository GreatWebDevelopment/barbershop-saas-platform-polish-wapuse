<template>
    <Head title="Edit Customer" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Edit Customer
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-[#D4A853]">{{ customer.visit_count || 0 }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Total Visits</div>
                    </div>
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-[#D4A853]">{{ customer.loyalty_points || 0 }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Loyalty Points</div>
                    </div>
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-gray-100">${{ formatMoney(customer.total_spent) }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Total Spent</div>
                    </div>
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-4 text-center">
                        <div class="text-sm font-semibold text-gray-100">{{ customer.last_visit_at ? formatDate(customer.last_visit_at) : 'Never' }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Last Visit</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-[#16162a] border border-gray-800 rounded-xl overflow-hidden">
                            <form @submit.prevent="submit" class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">First Name</label>
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                            required
                                        />
                                        <div v-if="form.errors.first_name" class="text-red-400 text-sm mt-1">{{ form.errors.first_name }}</div>
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">Last Name</label>
                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                            required
                                        />
                                        <div v-if="form.errors.last_name" class="text-red-400 text-sm mt-1">{{ form.errors.last_name }}</div>
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                                    <input id="email" v-model="form.email" type="email" class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" />
                                    <div v-if="form.errors.email" class="text-red-400 text-sm mt-1">{{ form.errors.email }}</div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                                        <input id="phone" v-model="form.phone" type="tel" class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" />
                                        <div v-if="form.errors.phone" class="text-red-400 text-sm mt-1">{{ form.errors.phone }}</div>
                                    </div>
                                    <div>
                                        <label for="preferred_stylist_id" class="block text-sm font-medium text-gray-300 mb-2">Preferred Stylist</label>
                                        <select id="preferred_stylist_id" v-model="form.preferred_stylist_id" class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50">
                                            <option :value="null">No preference</option>
                                            <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                                    <textarea
                                        id="notes"
                                        v-model="form.notes"
                                        rows="3"
                                        class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                        placeholder="Any special preferences or notes..."
                                    ></textarea>
                                    <div v-if="form.errors.notes" class="text-red-400 text-sm mt-1">{{ form.errors.notes }}</div>
                                </div>

                                <div class="flex items-center justify-end space-x-4">
                                    <Link
                                        :href="route('customers.index')"
                                        class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-gray-600 transition"
                                    >
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-5 py-2 bg-[#D4A853] border border-transparent rounded-lg font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] active:bg-[#b58831] disabled:opacity-50 transition"
                                    >
                                        Update Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Visit History Sidebar -->
                    <div>
                        <div class="bg-[#16162a] border border-gray-800 rounded-xl overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-800">
                                <h3 class="text-sm font-semibold text-gray-100 uppercase tracking-wider">Visit History</h3>
                            </div>
                            <div v-if="customer.appointments && customer.appointments.length" class="divide-y divide-gray-800">
                                <div
                                    v-for="appt in customer.appointments"
                                    :key="appt.id"
                                    class="px-5 py-3"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm text-gray-100 font-medium">{{ appt.service?.name || 'Service' }}</span>
                                        <span
                                            :class="statusClass(appt.status)"
                                            class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full"
                                        >
                                            {{ appt.status }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ appt.staff?.name || 'Staff' }}</span>
                                        <span>{{ formatDate(appt.starts_at) }}</span>
                                    </div>
                                    <div v-if="appt.price" class="text-xs text-[#D4A853] mt-0.5">${{ parseFloat(appt.price).toFixed(2) }}</div>
                                </div>
                            </div>
                            <div v-else class="px-5 py-8 text-center text-gray-600 text-sm">
                                No visit history
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    customer: Object,
    staff: Array,
});

const form = useForm({
    first_name: props.customer.first_name,
    last_name: props.customer.last_name,
    email: props.customer.email,
    phone: props.customer.phone || '',
    notes: props.customer.notes || '',
    preferred_stylist_id: props.customer.preferred_stylist_id || null,
});

const submit = () => {
    form.put(route('customers.update', props.customer.id));
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const formatMoney = (amount) => {
    const num = parseFloat(amount) || 0;
    return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
};

const statusClass = (status) => {
    const classes = {
        completed: 'bg-green-900/40 text-green-400',
        confirmed: 'bg-blue-900/40 text-blue-400',
        pending: 'bg-yellow-900/40 text-yellow-400',
        cancelled: 'bg-red-900/40 text-red-400',
        'no-show': 'bg-gray-700/40 text-gray-400',
    };
    return classes[status] || 'bg-gray-700/40 text-gray-400';
};
</script>
