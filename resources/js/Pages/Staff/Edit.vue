<template>
    <Head title="Edit Staff" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                Edit Staff Member
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                                Name
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                required
                            />
                            <div v-if="form.errors.name" class="text-red-400 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                                Title
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                placeholder="e.g., Senior Barber, Stylist"
                                required
                            />
                            <div v-if="form.errors.title" class="text-red-400 text-sm mt-1">
                                {{ form.errors.title }}
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

                        <!-- Years of Experience -->
                        <div>
                            <label for="years_experience" class="block text-sm font-medium text-gray-300 mb-2">
                                Years of Experience
                            </label>
                            <input
                                id="years_experience"
                                v-model="form.years_experience"
                                type="number"
                                min="0"
                                step="1"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                            />
                            <div v-if="form.errors.years_experience" class="text-red-400 text-sm mt-1">
                                {{ form.errors.years_experience }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hourly Rate -->
                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-300 mb-2">
                                    Hourly Rate ($)
                                </label>
                                <input
                                    id="hourly_rate"
                                    v-model="form.hourly_rate"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                />
                                <div v-if="form.errors.hourly_rate" class="text-red-400 text-sm mt-1">
                                    {{ form.errors.hourly_rate }}
                                </div>
                            </div>

                            <!-- Commission Percent -->
                            <div>
                                <label for="commission_percent" class="block text-sm font-medium text-gray-300 mb-2">
                                    Commission (%)
                                </label>
                                <input
                                    id="commission_percent"
                                    v-model="form.commission_percent"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                />
                                <div v-if="form.errors.commission_percent" class="text-red-400 text-sm mt-1">
                                    {{ form.errors.commission_percent }}
                                </div>
                            </div>
                        </div>

                        <!-- Specialties -->
                        <div>
                            <label for="specialties" class="block text-sm font-medium text-gray-300 mb-2">
                                Specialties
                            </label>
                            <input
                                id="specialties"
                                v-model="form.specialties"
                                type="text"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                placeholder="e.g., Fades, Beard Trim, Hot Towel Shave (comma-separated)"
                            />
                            <p class="text-xs text-gray-400 mt-1">Enter specialties separated by commas</p>
                            <div v-if="form.errors.specialties" class="text-red-400 text-sm mt-1">
                                {{ form.errors.specialties }}
                            </div>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">
                                Bio
                            </label>
                            <textarea
                                id="bio"
                                v-model="form.bio"
                                rows="4"
                                class="w-full rounded-md border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-400 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50"
                                placeholder="Brief biography..."
                            ></textarea>
                            <div v-if="form.errors.bio" class="text-red-400 text-sm mt-1">
                                {{ form.errors.bio }}
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
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-400 text-sm mt-1">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                :href="route('staff.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-[#16162a] transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-[#D4A853] border border-transparent rounded-md font-semibold text-xs text-gray-900 uppercase tracking-widest hover:bg-[#c49742] focus:bg-[#c49742] active:bg-[#b58831] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] disabled:opacity-50 transition ease-in-out duration-150"
                            >
                                Update Staff Member
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
    staffMember: Object
});

const form = useForm({
    name: props.staffMember.name,
    title: props.staffMember.title,
    email: props.staffMember.email || '',
    phone: props.staffMember.phone || '',
    years_experience: props.staffMember.years_experience || '',
    hourly_rate: props.staffMember.hourly_rate || '',
    commission_percent: props.staffMember.commission_percent || '',
    specialties: props.staffMember.specialties || '',
    bio: props.staffMember.bio || '',
    status: props.staffMember.status
});

const submit = () => {
    form.put(route('staff.update', props.staffMember.id));
};
</script>
