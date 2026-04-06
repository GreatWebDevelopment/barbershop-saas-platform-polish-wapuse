<template>
    <Head title="Services" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-[#f5f0e8] leading-tight">
                Services
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search and Filters -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search services..."
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-[#f5f0e8] placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @input="search"
                                />
                            </div>
                            <div class="w-full sm:w-48">
                                <select
                                    v-model="form.category"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-[#f5f0e8] focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @change="search"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <Link
                                :href="route('services.create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Add Service
                            </Link>
                        </div>

                        <!-- Grouped by Category (when no filters active) -->
                        <div v-if="services.length && !isFiltered">
                            <div v-for="category in groupedServices" :key="category.id" class="mb-8">
                                <div class="mb-3">
                                    <h3 class="text-lg font-semibold text-[#D4A853]">{{ category.name }}</h3>
                                    <p v-if="category.description" class="text-sm text-gray-400">{{ category.description }}</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        v-for="service in category.services"
                                        :key="service.id"
                                        class="bg-[#1a1a2e] rounded-lg p-5"
                                    >
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-[#f5f0e8]">{{ service.name }}</h4>
                                            <span
                                                :class="service.status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-400'"
                                                class="px-2 py-0.5 text-xs font-semibold rounded-full ml-2 flex-shrink-0"
                                            >
                                                {{ service.status }}
                                            </span>
                                        </div>
                                        <p v-if="service.description" class="text-sm text-gray-400 mb-3">{{ service.description }}</p>
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-[#D4A853] font-semibold">${{ service.price }}</span>
                                            <span class="text-sm text-gray-400 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ service.duration_minutes }} min
                                            </span>
                                        </div>
                                        <div v-if="service.appointments_count !== undefined" class="text-xs text-gray-400 mb-3">
                                            {{ service.appointments_count }} appointment{{ service.appointments_count !== 1 ? 's' : '' }}
                                        </div>
                                        <div class="flex justify-end space-x-3 pt-2 border-t border-gray-700">
                                            <Link
                                                :href="route('services.edit', service.id)"
                                                class="text-[#D4A853] hover:text-[#c49742] text-sm font-medium"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteService(service.id)"
                                                class="text-red-400 hover:text-red-300 text-sm font-medium"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flat Grid (when filtered) -->
                        <div v-else-if="services.length && isFiltered" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="service in services"
                                :key="service.id"
                                class="bg-[#1a1a2e] rounded-lg p-5"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-[#f5f0e8]">{{ service.name }}</h4>
                                    <span
                                        :class="service.status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-400'"
                                        class="px-2 py-0.5 text-xs font-semibold rounded-full ml-2 flex-shrink-0"
                                    >
                                        {{ service.status }}
                                    </span>
                                </div>
                                <p v-if="service.description" class="text-sm text-gray-400 mb-3">{{ service.description }}</p>
                                <div class="text-xs text-gray-400 mb-2">{{ service.category?.name }}</div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[#D4A853] font-semibold">${{ service.price }}</span>
                                    <span class="text-sm text-gray-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ service.duration_minutes }} min
                                    </span>
                                </div>
                                <div v-if="service.appointments_count !== undefined" class="text-xs text-gray-400 mb-3">
                                    {{ service.appointments_count }} appointment{{ service.appointments_count !== 1 ? 's' : '' }}
                                </div>
                                <div class="flex justify-end space-x-3 pt-2 border-t border-gray-700">
                                    <Link
                                        :href="route('services.edit', service.id)"
                                        class="text-[#D4A853] hover:text-[#c49742] text-sm font-medium"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteService(service.id)"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            <p class="text-lg font-medium">No services found</p>
                            <p class="mt-1 text-sm">Try adjusting your search or filter criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    services: Array,
    categories: Array,
    filters: Object
});

const form = ref({
    search: props.filters?.search || '',
    category: props.filters?.category || ''
});

const isFiltered = computed(() => {
    return form.value.search || form.value.category;
});

const groupedServices = computed(() => {
    const groups = [];
    const categoryMap = new Map();

    for (const cat of props.categories) {
        categoryMap.set(cat.id, { ...cat, services: [] });
    }

    // Uncategorized bucket
    const uncategorized = { id: null, name: 'Uncategorized', description: null, services: [] };

    for (const service of props.services) {
        const catId = service.service_category_id || service.category?.id;
        if (catId && categoryMap.has(catId)) {
            categoryMap.get(catId).services.push(service);
        } else {
            uncategorized.services.push(service);
        }
    }

    for (const cat of props.categories) {
        if (categoryMap.get(cat.id).services.length > 0) {
            groups.push(categoryMap.get(cat.id));
        }
    }

    if (uncategorized.services.length > 0) {
        groups.push(uncategorized);
    }

    return groups;
});

const search = debounce(() => {
    router.get(route('services.index'), {
        search: form.value.search,
        category: form.value.category
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

const deleteService = (id) => {
    if (confirm('Are you sure you want to delete this service?')) {
        router.delete(route('services.destroy', id));
    }
};
</script>
