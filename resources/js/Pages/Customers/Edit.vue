<template>
    <Head title="Edit Customer" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Edit Customer
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">
                                    First Name
                                </label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    required
                                />
                                <div v-if="form.errors.first_name" class="text-red-400 text-sm mt-1">
                                    {{ form.errors.first_name }}
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">
                                    Last Name
                                </label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                    required
                                />
                                <div v-if="form.errors.last_name" class="text-red-400 text-sm mt-1">
                                    {{ form.errors.last_name }}
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                                Email
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            />
                            <div v-if="form.errors.email" class="text-red-400 text-sm mt-1">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                                Phone
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                            />
                            <div v-if="form.errors.phone" class="text-red-400 text-sm mt-1">
                                {{ form.errors.phone }}
                            </div>
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
                                placeholder="Any special preferences or notes..."
                            ></textarea>
                            <div v-if="form.errors.notes" class="text-red-400 text-sm mt-1">
                                {{ form.errors.notes }}
                            </div>
                        </div>

                        <!-- Customer Stats (read-only display) -->
                        <div class="bg-[#1a1a2e] p-4 rounded-lg space-y-2">
                            <h3 class="text-sm font-medium text-gray-300 mb-3">Customer Statistics</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-[#D4A853]">{{ customer.visit_count || 0 }}</div>
                                    <div class="text-xs text-gray-400">Total Visits</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-[#D4A853]">{{ customer.loyalty_points || 0 }}</div>
                                    <div class="text-xs text-gray-400">Loyalty Points</div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-100">{{ customer.last_visit ? formatDate(customer.last_visit) : 'Never' }}</div>
                                    <div class="text-xs text-gray-400">Last Visit</div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                :href="route('customers.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] disabled:opacity-50 transition ease-in-out duration-150"
                            >
                                Update Customer
                            </button>
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
    customer: Object
});

const form = useForm({
    first_name: props.customer.first_name,
    last_name: props.customer.last_name,
    email: props.customer.email,
    phone: props.customer.phone || '',
    notes: props.customer.notes || ''
});

const submit = () => {
    form.put(route('customers.update', props.customer.id));
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>
