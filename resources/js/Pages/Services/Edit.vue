<template>
    <Head title="Edit Service" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Edit Service
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Service Name</label>
                            <input id="name" v-model="form.name" type="text" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" required />
                            <div v-if="form.errors.name" class="text-red-400 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                            <textarea id="description" v-model="form.description" rows="3" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" placeholder="Brief description of the service..."></textarea>
                            <div v-if="form.errors.description" class="text-red-400 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="service_category_id" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                            <select id="service_category_id" v-model="form.service_category_id" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" required>
                                <option value="">Select a category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                            <div v-if="form.errors.service_category_id" class="text-red-400 text-sm mt-1">{{ form.errors.service_category_id }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ($)</label>
                                <input id="price" v-model="form.price" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" required />
                                <div v-if="form.errors.price" class="text-red-400 text-sm mt-1">{{ form.errors.price }}</div>
                            </div>
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-300 mb-2">Duration (minutes)</label>
                                <input id="duration_minutes" v-model="form.duration_minutes" type="number" min="1" step="1" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" required />
                                <div v-if="form.errors.duration_minutes" class="text-red-400 text-sm mt-1">{{ form.errors.duration_minutes }}</div>
                            </div>
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Sort Order</label>
                                <input id="sort_order" v-model="form.sort_order" type="number" min="0" step="1" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" />
                                <div v-if="form.errors.sort_order" class="text-red-400 text-sm mt-1">{{ form.errors.sort_order }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="skill_level" class="block text-sm font-medium text-gray-300 mb-2">Skill Level</label>
                                <select id="skill_level" v-model="form.skill_level" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50">
                                    <option value="junior">Junior</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="master">Master</option>
                                </select>
                                <div v-if="form.errors.skill_level" class="text-red-400 text-sm mt-1">{{ form.errors.skill_level }}</div>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                                <select id="status" v-model="form.status" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-400 text-sm mt-1">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Add-Ons -->
                        <div v-if="addOns.length > 0">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Add-Ons</label>
                            <div class="space-y-2 bg-[#1a1a2e] rounded-md p-4 border border-gray-600">
                                <label v-for="addOn in addOns" :key="addOn.id" class="flex items-center gap-3 cursor-pointer hover:bg-[#16162a] p-2 rounded transition">
                                    <input type="checkbox" :value="addOn.id" v-model="form.add_on_ids" class="rounded border-gray-600 bg-[#16162a] text-[#D4A853] focus:ring-[#D4A853]" />
                                    <span class="text-gray-100 text-sm">{{ addOn.name }}</span>
                                    <span class="text-[#D4A853] text-xs ml-auto">${{ Number(addOn.price).toFixed(2) }}</span>
                                    <span class="text-gray-400 text-xs">{{ addOn.duration_minutes }} min</span>
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <Link :href="route('services.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] disabled:opacity-50 transition ease-in-out duration-150">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    service: Object,
    categories: Array,
    addOns: Array
});

const form = useForm({
    name: props.service.name,
    description: props.service.description || '',
    service_category_id: props.service.service_category_id,
    price: props.service.price,
    duration_minutes: props.service.duration_minutes,
    status: props.service.status,
    skill_level: props.service.skill_level || 'intermediate',
    sort_order: props.service.sort_order || 0,
    add_on_ids: props.service.add_ons?.map(a => a.id) || [],
});

const submit = () => {
    form.put(route('services.update', props.service.id));
};
</script>
