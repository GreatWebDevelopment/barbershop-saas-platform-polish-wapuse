<template>
    <Head :title="`${staffMember.name} — Schedule`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('staff.index')" class="text-gray-400 hover:text-[#D4A853] transition">
                    &larr; Staff
                </Link>
                <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                    {{ staffMember.name }} — Weekly Schedule
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 rounded bg-green-900/50 text-green-300 border border-green-700">
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-[#16162a] shadow-sm sm:rounded-lg p-6">
                    <div class="space-y-6">
                        <div
                            v-for="day in days"
                            :key="day.key"
                            class="bg-[#1a1a2e] rounded-lg p-5"
                        >
                            <!-- Day header -->
                            <div class="flex items-center justify-between mb-4">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="form.availability_schedule[day.key].enabled"
                                        class="rounded border-gray-600 bg-[#0f0f1a] text-[#D4A853] focus:ring-[#D4A853]"
                                    />
                                    <span class="text-lg font-semibold text-gray-100">{{ day.label }}</span>
                                </label>
                            </div>

                            <div v-if="form.availability_schedule[day.key].enabled" class="space-y-4">
                                <!-- Time blocks -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-400">Working Hours</span>
                                        <button
                                            type="button"
                                            @click="addBlock(day.key)"
                                            class="text-xs text-[#D4A853] hover:text-[#c49742] transition"
                                        >
                                            + Add Block
                                        </button>
                                    </div>
                                    <div
                                        v-for="(block, bi) in form.availability_schedule[day.key].blocks"
                                        :key="'block-' + bi"
                                        class="flex items-center gap-3 mb-2"
                                    >
                                        <input
                                            type="time"
                                            v-model="block.start"
                                            class="rounded border-gray-600 bg-[#0f0f1a] text-gray-100 text-sm focus:border-[#D4A853] focus:ring-[#D4A853]"
                                        />
                                        <span class="text-gray-500">to</span>
                                        <input
                                            type="time"
                                            v-model="block.end"
                                            class="rounded border-gray-600 bg-[#0f0f1a] text-gray-100 text-sm focus:border-[#D4A853] focus:ring-[#D4A853]"
                                        />
                                        <button
                                            v-if="form.availability_schedule[day.key].blocks.length > 1"
                                            type="button"
                                            @click="removeBlock(day.key, bi)"
                                            class="text-red-400 hover:text-red-300 text-sm"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <!-- Breaks -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-400">Breaks</span>
                                        <button
                                            type="button"
                                            @click="addBreak(day.key)"
                                            class="text-xs text-[#D4A853] hover:text-[#c49742] transition"
                                        >
                                            + Add Break
                                        </button>
                                    </div>
                                    <div
                                        v-for="(brk, bi) in form.availability_schedule[day.key].breaks"
                                        :key="'break-' + bi"
                                        class="flex items-center gap-3 mb-2"
                                    >
                                        <input
                                            type="time"
                                            v-model="brk.start"
                                            class="rounded border-gray-600 bg-[#0f0f1a] text-gray-100 text-sm focus:border-[#D4A853] focus:ring-[#D4A853]"
                                        />
                                        <span class="text-gray-500">to</span>
                                        <input
                                            type="time"
                                            v-model="brk.end"
                                            class="rounded border-gray-600 bg-[#0f0f1a] text-gray-100 text-sm focus:border-[#D4A853] focus:ring-[#D4A853]"
                                        />
                                        <button
                                            type="button"
                                            @click="removeBreak(day.key, bi)"
                                            class="text-red-400 hover:text-red-300 text-sm"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                    <p v-if="form.availability_schedule[day.key].breaks.length === 0" class="text-xs text-gray-500">
                                        No breaks scheduled
                                    </p>
                                </div>
                            </div>

                            <p v-else class="text-sm text-gray-500">Day off</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button
                            @click="save"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-[#D4A853] text-gray-900 font-semibold rounded-md hover:bg-[#c49742] focus:outline-none focus:ring-2 focus:ring-[#D4A853] focus:ring-offset-2 focus:ring-offset-[#16162a] transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Schedule' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    staffMember: Object,
});

const days = [
    { key: 'monday', label: 'Monday' },
    { key: 'tuesday', label: 'Tuesday' },
    { key: 'wednesday', label: 'Wednesday' },
    { key: 'thursday', label: 'Thursday' },
    { key: 'friday', label: 'Friday' },
    { key: 'saturday', label: 'Saturday' },
    { key: 'sunday', label: 'Sunday' },
];

const defaultDay = () => ({
    enabled: false,
    blocks: [{ start: '09:00', end: '17:00' }],
    breaks: [],
});

const existing = props.staffMember.availability_schedule || {};
const schedule = {};
days.forEach(d => {
    schedule[d.key] = existing[d.key]
        ? { ...defaultDay(), ...existing[d.key] }
        : defaultDay();
});

const form = useForm({
    availability_schedule: schedule,
});

const addBlock = (day) => {
    form.availability_schedule[day].blocks.push({ start: '09:00', end: '17:00' });
};

const removeBlock = (day, index) => {
    form.availability_schedule[day].blocks.splice(index, 1);
};

const addBreak = (day) => {
    form.availability_schedule[day].breaks.push({ start: '12:00', end: '13:00' });
};

const removeBreak = (day, index) => {
    form.availability_schedule[day].breaks.splice(index, 1);
};

const save = () => {
    form.patch(route('staff.schedule.update', props.staffMember.id));
};
</script>
