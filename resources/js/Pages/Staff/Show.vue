<template>
    <Head :title="staffMember.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('staff.index')" class="text-gray-400 hover:text-[#D4A853] transition">
                    &larr; Staff
                </Link>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    {{ staffMember.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] shadow-sm sm:rounded-lg p-8">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Photo -->
                        <div class="flex-shrink-0">
                            <div class="w-40 h-40 rounded-full bg-[#1a1a2e] flex items-center justify-center overflow-hidden border-2 border-[#D4A853]/30">
                                <img
                                    v-if="staffMember.photo_path || staffMember.avatar_url"
                                    :src="staffMember.photo_path || staffMember.avatar_url"
                                    :alt="staffMember.name"
                                    class="w-full h-full object-cover"
                                />
                                <span v-else class="text-4xl text-gray-500">
                                    {{ initials }}
                                </span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 space-y-5">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-100">{{ staffMember.name }}</h1>
                                <p v-if="staffMember.title" class="text-[#D4A853]">{{ staffMember.title }}</p>
                            </div>

                            <p v-if="staffMember.bio" class="text-gray-300 leading-relaxed">{{ staffMember.bio }}</p>

                            <!-- Specialties -->
                            <div v-if="specialties.length" class="flex flex-wrap gap-2">
                                <span
                                    v-for="s in specialties"
                                    :key="s"
                                    class="px-3 py-1 bg-[#D4A853]/20 text-[#D4A853] text-sm rounded-full"
                                >
                                    {{ s }}
                                </span>
                            </div>

                            <!-- Stats grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-gray-700">
                                <div>
                                    <p class="text-xs text-gray-500">Experience</p>
                                    <p class="text-gray-100 font-semibold">{{ staffMember.years_experience }} yrs</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Hourly Rate</p>
                                    <p class="text-[#D4A853] font-semibold">${{ staffMember.hourly_rate }}/hr</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Commission</p>
                                    <p class="text-gray-100 font-semibold">{{ staffMember.commission_percent }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Appointments</p>
                                    <p class="text-gray-100 font-semibold">{{ staffMember.appointments_count || 0 }}</p>
                                </div>
                            </div>

                            <!-- Weekly schedule preview -->
                            <div v-if="staffMember.availability_schedule" class="pt-4 border-t border-gray-700">
                                <h3 class="text-sm font-medium text-gray-400 mb-3">Weekly Availability</h3>
                                <div class="grid grid-cols-7 gap-2">
                                    <div
                                        v-for="day in dayLabels"
                                        :key="day.key"
                                        class="text-center"
                                    >
                                        <p class="text-xs text-gray-500 mb-1">{{ day.short }}</p>
                                        <div
                                            :class="isEnabled(day.key) ? 'bg-[#D4A853]/20 text-[#D4A853]' : 'bg-gray-800 text-gray-600'"
                                            class="rounded py-1 text-xs font-medium"
                                        >
                                            {{ isEnabled(day.key) ? 'On' : 'Off' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action links -->
                            <div class="flex gap-3 pt-4">
                                <Link
                                    :href="route('staff.schedule', staffMember.id)"
                                    class="px-4 py-2 bg-[#D4A853] text-gray-900 text-sm font-semibold rounded hover:bg-[#c49742] transition"
                                >
                                    Edit Schedule
                                </Link>
                                <Link
                                    :href="route('staff.commissions', staffMember.id)"
                                    class="px-4 py-2 bg-[#1a1a2e] text-[#D4A853] text-sm font-semibold rounded border border-[#D4A853]/30 hover:border-[#D4A853] transition"
                                >
                                    View Commissions
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    staffMember: Object,
});

const initials = computed(() => {
    return props.staffMember.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const specialties = computed(() => {
    const s = props.staffMember.specialties;
    if (!s) return [];
    if (Array.isArray(s)) return s.filter(Boolean);
    return s.split(',').map(x => x.trim()).filter(Boolean);
});

const dayLabels = [
    { key: 'monday', short: 'Mon' },
    { key: 'tuesday', short: 'Tue' },
    { key: 'wednesday', short: 'Wed' },
    { key: 'thursday', short: 'Thu' },
    { key: 'friday', short: 'Fri' },
    { key: 'saturday', short: 'Sat' },
    { key: 'sunday', short: 'Sun' },
];

const isEnabled = (day) => {
    return props.staffMember.availability_schedule?.[day]?.enabled ?? false;
};
</script>
