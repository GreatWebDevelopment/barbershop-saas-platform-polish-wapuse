<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Customers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search, Sort & Actions -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <div class="flex-1">
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Search by name, email, or phone..."
                            class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-2.5"
                            @input="search"
                        />
                    </div>
                    <select
                        v-model="sortOption"
                        @change="applySort"
                        class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-300 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2.5 text-sm"
                    >
                        <option value="first_name:asc">Name A-Z</option>
                        <option value="first_name:desc">Name Z-A</option>
                        <option value="last_visit_at:desc">Recent Visit</option>
                        <option value="total_spent:desc">Top Spenders</option>
                        <option value="loyalty_points:desc">Most Points</option>
                        <option value="visit_count:desc">Most Visits</option>
                    </select>
                    <Link
                        :href="route('customers.create')"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-[#D4A853] border border-transparent rounded-lg font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#0f0f1a] transition"
                    >
                        + Add Customer
                    </Link>
                </div>

                <!-- Card Grid -->
                <div v-if="customers.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="bg-[#16162a] border border-gray-800 rounded-xl p-5 hover:border-[#D4A853]/40 transition group"
                    >
                        <!-- Header: Avatar + Name -->
                        <div class="flex items-start gap-3 mb-4">
                            <div
                                class="w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
                                :style="{ backgroundColor: avatarColor(customer.first_name + customer.last_name), color: '#0f0f1a' }"
                            >
                                {{ initials(customer) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <Link
                                    :href="route('customers.edit', customer.id)"
                                    class="text-gray-100 font-semibold text-sm hover:text-[#D4A853] transition truncate block"
                                >
                                    {{ customer.first_name }} {{ customer.last_name }}
                                </Link>
                                <div v-if="customer.email" class="text-gray-500 text-xs truncate">{{ customer.email }}</div>
                                <div v-if="customer.phone" class="text-gray-500 text-xs">{{ customer.phone }}</div>
                            </div>
                            <!-- Actions -->
                            <div class="opacity-0 group-hover:opacity-100 transition flex gap-1 shrink-0">
                                <Link
                                    :href="route('customers.edit', customer.id)"
                                    class="p-1.5 rounded-md hover:bg-[#1a1a2e] text-gray-400 hover:text-[#D4A853] transition"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </Link>
                                <button
                                    @click="deleteCustomer(customer.id)"
                                    class="p-1.5 rounded-md hover:bg-red-900/30 text-gray-400 hover:text-red-400 transition"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-[#0f0f1a] rounded-lg px-3 py-2">
                                <div class="text-[#D4A853] font-bold text-lg leading-tight">{{ customer.visit_count || 0 }}</div>
                                <div class="text-gray-500 text-[10px] uppercase tracking-wider">Visits</div>
                            </div>
                            <div class="bg-[#0f0f1a] rounded-lg px-3 py-2">
                                <div class="text-[#D4A853] font-bold text-lg leading-tight">{{ customer.loyalty_points || 0 }}</div>
                                <div class="text-gray-500 text-[10px] uppercase tracking-wider">Points</div>
                            </div>
                            <div class="bg-[#0f0f1a] rounded-lg px-3 py-2">
                                <div class="text-gray-100 font-semibold text-sm leading-tight">${{ formatMoney(customer.total_spent) }}</div>
                                <div class="text-gray-500 text-[10px] uppercase tracking-wider">Spent</div>
                            </div>
                            <div class="bg-[#0f0f1a] rounded-lg px-3 py-2">
                                <div class="text-gray-100 font-semibold text-sm leading-tight">{{ customer.last_visit_at ? formatDate(customer.last_visit_at) : 'Never' }}</div>
                                <div class="text-gray-500 text-[10px] uppercase tracking-wider">Last Visit</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-[#16162a] border border-gray-800 rounded-xl p-12 text-center">
                    <div class="text-gray-500 text-lg mb-2">No customers found</div>
                    <div class="text-gray-600 text-sm">{{ form.search ? 'Try a different search term' : 'Add your first customer to get started' }}</div>
                </div>

                <!-- Pagination -->
                <div v-if="customers.links.length > 3" class="mt-8 flex justify-center space-x-1">
                    <Link
                        v-for="(link, index) in customers.links"
                        :key="index"
                        :href="link.url"
                        :class="[
                            'px-3 py-2 rounded-md text-sm font-medium transition',
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const form = ref({
    search: props.filters?.search || '',
});

const sortOption = ref(
    (props.filters?.sort || 'first_name') + ':' + (props.filters?.direction || 'asc')
);

const search = debounce(() => {
    applyFilters();
}, 300);

const applySort = () => {
    applyFilters();
};

const applyFilters = () => {
    const [sort, direction] = sortOption.value.split(':');
    router.get(route('customers.index'), {
        search: form.value.search,
        sort,
        direction,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        router.delete(route('customers.destroy', id));
    }
};

const initials = (customer) => {
    return ((customer.first_name?.[0] || '') + (customer.last_name?.[0] || '')).toUpperCase();
};

const avatarColor = (name) => {
    const colors = [
        '#D4A853', '#E8725C', '#5B8DEF', '#4ECDC4', '#A78BFA',
        '#F59E0B', '#EF4444', '#10B981', '#6366F1', '#EC4899',
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};

const formatMoney = (amount) => {
    const num = parseFloat(amount) || 0;
    return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
};
</script>
