<template>
    <Head title="Services" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Services
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
                                    placeholder="Search services..."
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @input="search"
                                />
                            </div>
                            <div class="w-full sm:w-48">
                                <select
                                    v-model="form.category"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @change="search"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('service-categories.index')"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                                >
                                    Categories
                                </Link>
                                <Link
                                    :href="route('services.create')"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                                >
                                    Add Service
                                </Link>
                            </div>
                        </div>

                        <!-- Accordion grouped by category -->
                        <div class="space-y-4">
                            <div
                                v-for="group in groupedServices"
                                :key="group.category.id"
                                class="border border-gray-700 rounded-lg overflow-hidden"
                            >
                                <!-- Category Header -->
                                <button
                                    @click="toggleCategory(group.category.id)"
                                    class="w-full flex items-center justify-between px-6 py-4 bg-[#1a1a2e] hover:bg-[#1e1e36] transition text-left"
                                >
                                    <div class="flex items-center gap-3">
                                        <svg
                                            :class="{ 'rotate-90': openCategories[group.category.id] }"
                                            class="w-4 h-4 text-gray-400 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span class="text-gray-100 font-semibold text-sm uppercase tracking-wider">
                                            {{ group.category.name }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            ({{ group.services.length }} service{{ group.services.length !== 1 ? 's' : '' }})
                                        </span>
                                    </div>
                                </button>

                                <!-- Services List -->
                                <div v-show="openCategories[group.category.id]" class="divide-y divide-gray-700">
                                    <div
                                        v-for="(service, index) in group.services"
                                        :key="service.id"
                                        class="flex items-center gap-4 px-6 py-4 hover:bg-[#1a1a2e] transition"
                                    >
                                        <!-- Sort Handle -->
                                        <div class="flex flex-col gap-0.5">
                                            <button
                                                @click="moveService(group, index, -1)"
                                                :disabled="index === 0"
                                                class="text-gray-500 hover:text-gray-300 disabled:opacity-20 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button
                                                @click="moveService(group, index, 1)"
                                                :disabled="index === group.services.length - 1"
                                                class="text-gray-500 hover:text-gray-300 disabled:opacity-20 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>

                                        <!-- Drag Handle Icon -->
                                        <div class="text-gray-600 cursor-grab">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z"/>
                                            </svg>
                                        </div>

                                        <!-- Service Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-100 font-medium">{{ service.name }}</span>
                                                <span :class="skillBadgeClass(service.skill_level)" class="px-2 py-0.5 text-xs font-semibold rounded-full">
                                                    {{ service.skill_level }}
                                                </span>
                                                <span v-if="service.status === 'inactive'" class="bg-gray-100 text-gray-800 px-2 py-0.5 text-xs font-semibold rounded-full">
                                                    inactive
                                                </span>
                                            </div>
                                            <div v-if="service.description" class="text-gray-400 text-xs mt-1 truncate">
                                                {{ service.description }}
                                            </div>
                                            <div v-if="service.add_ons?.length" class="flex flex-wrap gap-1 mt-1">
                                                <span
                                                    v-for="addOn in service.add_ons"
                                                    :key="addOn.id"
                                                    class="text-xs bg-[#D4A853]/10 text-[#D4A853] px-2 py-0.5 rounded"
                                                >
                                                    + {{ addOn.name }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Duration -->
                                        <div class="text-sm text-gray-400 whitespace-nowrap">
                                            {{ service.duration_minutes }} min
                                        </div>

                                        <!-- Price -->
                                        <div class="text-sm text-[#D4A853] font-semibold whitespace-nowrap">
                                            ${{ Number(service.price).toFixed(2) }}
                                        </div>

                                        <!-- Popularity -->
                                        <div class="text-xs text-gray-400 whitespace-nowrap flex items-center gap-1" :title="service.booking_count + ' bookings'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            {{ service.booking_count || 0 }}
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-3 whitespace-nowrap">
                                            <Link
                                                :href="route('services.edit', service.id)"
                                                class="text-[#D4A853] hover:text-[#c49742] text-sm"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteService(service.id)"
                                                class="text-red-400 hover:text-red-300 text-sm"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="services.length === 0" class="text-center py-12 text-gray-400">
                            No services found.
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

const openCategories = ref({});

// Initialize all categories as open
props.categories.forEach(c => { openCategories.value[c.id] = true; });

const toggleCategory = (id) => {
    openCategories.value[id] = !openCategories.value[id];
};

const groupedServices = computed(() => {
    const groups = [];
    const categoryMap = {};

    props.categories.forEach(cat => {
        const group = { category: cat, services: [] };
        categoryMap[cat.id] = group;
        groups.push(group);
    });

    props.services.forEach(service => {
        const group = categoryMap[service.service_category_id];
        if (group) {
            group.services.push(service);
        }
    });

    return groups.filter(g => g.services.length > 0);
});

const skillBadgeClass = (level) => {
    const map = {
        junior: 'bg-blue-100 text-blue-800',
        intermediate: 'bg-yellow-100 text-yellow-800',
        master: 'bg-purple-100 text-purple-800',
    };
    return map[level] || 'bg-gray-100 text-gray-800';
};

const search = debounce(() => {
    router.get(route('services.index'), {
        search: form.value.search,
        category: form.value.category
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

const moveService = (group, index, direction) => {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= group.services.length) return;

    const services = [...group.services];
    [services[index], services[newIndex]] = [services[newIndex], services[index]];

    const orders = services.map((s, i) => ({ id: s.id, sort_order: i }));
    router.post(route('services.reorder'), { orders }, { preserveState: true });
};

const deleteService = (id) => {
    if (confirm('Are you sure you want to delete this service?')) {
        router.delete(route('services.destroy', id));
    }
};
</script>
