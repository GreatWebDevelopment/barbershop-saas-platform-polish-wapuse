<template>
    <Head title="Create Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Create New Appointment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Customer Select -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Customer
                            </label>
                            <select
                                id="customer_id"
                                v-model="form.customer_id"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            >
                                <option value="">Select a customer</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.first_name }} {{ customer.last_name }}
                                </option>
                            </select>
                            <div v-if="form.errors.customer_id" class="text-red-400 text-sm mt-1">
                                {{ form.errors.customer_id }}
                            </div>
                        </div>

                        <!-- Staff Select -->
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Stylist
                            </label>
                            <select
                                id="staff_id"
                                v-model="form.staff_id"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            >
                                <option value="">Select a stylist</option>
                                <option v-for="member in staff" :key="member.id" :value="member.id">
                                    {{ member.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.staff_id" class="text-red-400 text-sm mt-1">
                                {{ form.errors.staff_id }}
                            </div>
                        </div>

                        <!-- Service Select -->
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Service
                            </label>
                            <select
                                id="service_id"
                                v-model="form.service_id"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            >
                                <option value="">Select a service</option>
                                <optgroup v-for="category in groupedServices" :key="category.name" :label="category.name">
                                    <option v-for="service in category.services" :key="service.id" :value="service.id">
                                        {{ service.name }} - ${{ service.price }}
                                    </option>
                                </optgroup>
                            </select>
                            <div v-if="form.errors.service_id" class="text-red-400 text-sm mt-1">
                                {{ form.errors.service_id }}
                            </div>
                        </div>

                        <!-- Date/Time -->
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-300 mb-2">
                                Date & Time
                            </label>
                            <input
                                id="starts_at"
                                v-model="form.starts_at"
                                type="datetime-local"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            />
                            <div v-if="form.errors.starts_at" class="text-red-400 text-sm mt-1">
                                {{ form.errors.starts_at }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                                Status
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                            >
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no-show">No-Show</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-400 text-sm mt-1">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Walk-in Checkbox -->
                        <div class="flex items-center">
                            <input
                                id="is_walkin"
                                v-model="form.is_walkin"
                                type="checkbox"
                                class="rounded border-gray-600 bg-[#1a1a2e] text-[#D4A853] focus:ring-[#D4A853] focus:ring-opacity-50"
                            />
                            <label for="is_walkin" class="ml-2 block text-sm text-gray-300">
                                Walk-in appointment
                            </label>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                placeholder="Additional notes..."
                            ></textarea>
                            <div v-if="form.errors.notes" class="text-red-400 text-sm mt-1">
                                {{ form.errors.notes }}
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                :href="route('appointments.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] disabled:opacity-50 transition ease-in-out duration-150"
                            >
                                Create Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    customers: Array,
    staff: Array,
    services: Array
});

const form = useForm({
    customer_id: '',
    staff_id: '',
    service_id: '',
    starts_at: '',
    status: 'pending',
    is_walkin: false,
    notes: ''
});

const groupedServices = computed(() => {
    const groups = {};
    props.services.forEach(service => {
        const categoryName = service.category?.name || 'Uncategorized';
        if (!groups[categoryName]) {
            groups[categoryName] = {
                name: categoryName,
                services: []
            };
        }
        groups[categoryName].services.push(service);
    });
    return Object.values(groups);
});

const submit = () => {
    form.post(route('appointments.store'));
};
</script>
