<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-[#f5f0e8] leading-tight">
                Customers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search and Actions -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search customers..."
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-[#f5f0e8] placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @input="search"
                                />
                            </div>
                            <Link
                                :href="route('customers.create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Add Customer
                            </Link>
                        </div>

                        <!-- Card Grid -->
                        <div v-if="customers.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div
                                v-for="customer in customers.data"
                                :key="customer.id"
                                class="bg-[#1a1a2e] rounded-lg p-6"
                            >
                                <!-- Avatar and Name -->
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 rounded-full bg-[#D4A853] flex items-center justify-center text-gray-900 font-bold text-lg flex-shrink-0">
                                        {{ getInitials(customer) }}
                                    </div>
                                    <div class="ml-4 min-w-0">
                                        <div class="font-bold text-[#f5f0e8] truncate">{{ customer.first_name }} {{ customer.last_name }}</div>
                                        <div class="text-sm text-gray-400 truncate">{{ customer.email }}</div>
                                        <div v-if="customer.phone" class="text-sm text-gray-400">{{ customer.phone }}</div>
                                    </div>
                                </div>

                                <!-- Stats Row -->
                                <div class="flex justify-between items-center mb-4 py-3 border-t border-b border-gray-700">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-[#f5f0e8]">{{ customer.visit_count || 0 }}</div>
                                        <div class="text-xs text-gray-400">Visits</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-[#D4A853]">${{ formatCurrency(customer.total_spend) }}</div>
                                        <div class="text-xs text-gray-400">Total Spend</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-[#f5f0e8] flex items-center justify-center">
                                            <svg class="w-4 h-4 text-[#D4A853] mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {{ customer.loyalty_points || 0 }}
                                        </div>
                                        <div class="text-xs text-gray-400">Points</div>
                                    </div>
                                </div>

                                <!-- Last Visit -->
                                <div class="text-sm text-gray-400 mb-4">
                                    Last visit: {{ customer.last_visit ? formatDate(customer.last_visit) : 'Never' }}
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end space-x-3">
                                    <Link
                                        :href="route('customers.edit', customer.id)"
                                        class="text-[#D4A853] hover:text-[#c49742] text-sm font-medium"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteCustomer(customer.id)"
                                        class="text-red-400 hover:text-red-300 text-sm font-medium"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12 text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-lg font-medium">No customers found</p>
                            <p class="mt-1 text-sm">Try adjusting your search criteria.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="customers.links.length > 3" class="mt-6 flex justify-center space-x-1">
                            <Link
                                v-for="(link, index) in customers.links"
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
    customers: Object,
    filters: Object
});

const form = ref({
    search: props.filters?.search || ''
});

const search = debounce(() => {
    router.get(route('customers.index'), {
        search: form.value.search
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        router.delete(route('customers.destroy', id));
    }
};

const getInitials = (customer) => {
    const first = customer.first_name ? customer.first_name.charAt(0).toUpperCase() : '';
    const last = customer.last_name ? customer.last_name.charAt(0).toUpperCase() : '';
    return first + last;
};

const formatCurrency = (amount) => {
    const num = parseFloat(amount) || 0;
    return num.toFixed(2);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>
