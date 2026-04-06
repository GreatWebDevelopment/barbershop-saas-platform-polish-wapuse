<template>
    <Head title="Staff" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Staff
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
                                    placeholder="Search staff..."
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    @input="search"
                                />
                            </div>
                            <Link
                                :href="route('staff.create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Add Staff
                            </Link>
                        </div>

                        <!-- Staff Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div
                                v-for="member in staff"
                                :key="member.id"
                                class="bg-[#1a1a2e] rounded-lg p-6 hover:shadow-lg hover:shadow-[#D4A853]/10 transition"
                            >
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-100">{{ member.name }}</h3>
                                        <p class="text-sm text-gray-400">{{ member.title }}</p>
                                    </div>
                                    <span
                                        :class="member.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ member.status }}
                                    </span>
                                </div>

                                <!-- Specialties -->
                                <div v-if="member.specialties" class="mb-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(specialty, index) in parseSpecialties(member.specialties)"
                                            :key="index"
                                            class="px-2 py-1 bg-[#D4A853]/20 text-[#D4A853] text-xs rounded-full"
                                        >
                                            {{ specialty }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Hourly Rate:</span>
                                        <span class="text-[#D4A853] font-semibold">${{ member.hourly_rate }}/hr</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Commission:</span>
                                        <span class="text-gray-100">{{ member.commission_percent }}%</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Appointments:</span>
                                        <span class="text-gray-100">{{ member.appointments_count || 0 }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2 pt-4 border-t border-gray-700">
                                    <Link
                                        :href="route('staff.edit', member.id)"
                                        class="flex-1 text-center px-3 py-2 bg-[#D4A853] text-gray-900 text-sm font-medium rounded hover:bg-[#c49742] transition"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteStaff(member.id)"
                                        class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-500 transition"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="staff.length === 0" class="text-center py-12 text-gray-400">
                            No staff members found.
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
    staff: Array,
    filters: Object
});

const form = ref({
    search: props.filters?.search || ''
});

const search = debounce(() => {
    router.get(route('staff.index'), {
        search: form.value.search
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

const deleteStaff = (id) => {
    if (confirm('Are you sure you want to delete this staff member?')) {
        router.delete(route('staff.destroy', id));
    }
};

const parseSpecialties = (specialties) => {
    if (!specialties) return [];
    if (Array.isArray(specialties)) return specialties;
    return specialties.split(',').map(s => s.trim()).filter(Boolean);
};
</script>
