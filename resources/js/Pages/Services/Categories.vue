<template>
    <Head title="Service Categories" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Service Categories</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Add Category Form -->
                        <form @submit.prevent="addCategory" class="mb-6 flex gap-4">
                            <input v-model="createForm.name" type="text" placeholder="Category name..." class="flex-1 rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" required />
                            <input v-model="createForm.description" type="text" placeholder="Description (optional)" class="flex-1 rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" />
                            <input v-model="createForm.sort_order" type="number" min="0" placeholder="Order" class="w-20 rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50" />
                            <button type="submit" :disabled="createForm.processing" class="inline-flex items-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] disabled:opacity-50 transition ease-in-out duration-150">Add</button>
                        </form>
                        <div v-if="createForm.errors.name" class="text-red-400 text-sm mb-4">{{ createForm.errors.name }}</div>

                        <!-- Categories Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-[#1a1a2e]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Services</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#16162a] divide-y divide-gray-700">
                                    <tr v-for="category in categories" :key="category.id" class="hover:bg-[#1a1a2e] transition">
                                        <td class="px-6 py-4 text-sm text-gray-100">
                                            <input v-if="editing === category.id" v-model="editForm.sort_order" type="number" min="0" class="w-16 rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 text-sm" />
                                            <template v-else>{{ category.sort_order }}</template>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-100">
                                            <input v-if="editing === category.id" v-model="editForm.name" type="text" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 text-sm" required />
                                            <span v-else class="font-medium">{{ category.name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            <input v-if="editing === category.id" v-model="editForm.description" type="text" class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 text-sm" />
                                            <template v-else>{{ category.description || '—' }}</template>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-100">{{ category.services_count }}</td>
                                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                                            <template v-if="editing === category.id">
                                                <button @click="saveEdit(category)" class="text-green-400 hover:text-green-300">Save</button>
                                                <button @click="editing = null" class="text-gray-400 hover:text-gray-300">Cancel</button>
                                            </template>
                                            <template v-else>
                                                <button @click="startEdit(category)" class="text-[#D4A853] hover:text-[#c49742]">Edit</button>
                                                <button v-if="category.services_count === 0" @click="deleteCategory(category.id)" class="text-red-400 hover:text-red-300">Delete</button>
                                                <span v-else class="text-gray-600 text-xs">Has services</span>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="categories.length === 0" class="text-center py-12 text-gray-400">No categories yet. Create one above.</div>

                        <div class="mt-6">
                            <Link :href="route('services.index')" class="text-[#D4A853] hover:text-[#c49742] text-sm">&larr; Back to Services</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ categories: Array });

const createForm = useForm({ name: '', description: '', sort_order: 0 });
const editing = ref(null);
const editForm = ref({ name: '', description: '', sort_order: 0 });

const addCategory = () => {
    createForm.post(route('service-categories.store'), { onSuccess: () => createForm.reset() });
};

const startEdit = (category) => {
    editing.value = category.id;
    editForm.value = { name: category.name, description: category.description || '', sort_order: category.sort_order };
};

const saveEdit = (category) => {
    router.put(route('service-categories.update', category.id), editForm.value, { onSuccess: () => { editing.value = null; } });
};

const deleteCategory = (id) => {
    if (confirm('Delete this category?')) {
        router.delete(route('service-categories.destroy', id));
    }
};
</script>
