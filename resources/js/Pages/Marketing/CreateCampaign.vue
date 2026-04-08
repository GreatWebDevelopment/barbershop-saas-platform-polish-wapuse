<template>
    <Head title="Create Campaign" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Create Campaign</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#16162a] border border-gray-800 rounded-xl p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Subject Line</label>
                            <input
                                v-model="form.subject"
                                type="text"
                                required
                                placeholder="e.g. Spring Special — 20% Off All Services"
                                class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-2.5"
                            />
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-400">{{ form.errors.subject }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Customer Segment</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    v-for="seg in segments"
                                    :key="seg.value"
                                    type="button"
                                    @click="form.segment = seg.value"
                                    class="p-4 rounded-lg border text-left transition"
                                    :class="form.segment === seg.value
                                        ? 'border-[#D4A853] bg-[#D4A853]/10'
                                        : 'border-gray-700 bg-[#1a1a2e] hover:border-gray-600'"
                                >
                                    <div class="text-sm font-medium" :class="form.segment === seg.value ? 'text-[#D4A853]' : 'text-gray-200'">{{ seg.label }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ seg.desc }}</div>
                                    <div class="text-xs mt-1" :class="form.segment === seg.value ? 'text-[#D4A853]' : 'text-gray-500'">
                                        {{ segmentCounts[seg.value] }} customers
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Email Body (HTML)</label>
                            <textarea
                                v-model="form.body_html"
                                rows="12"
                                required
                                placeholder="Write your email content here. You can use HTML for formatting..."
                                class="w-full rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-100 placeholder-gray-500 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-4 py-3 font-mono text-sm"
                            ></textarea>
                            <p v-if="form.errors.body_html" class="mt-1 text-sm text-red-400">{{ form.errors.body_html }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <Link
                                :href="route('marketing.campaigns.index')"
                                class="text-gray-400 hover:text-gray-200 text-sm transition"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2.5 bg-[#D4A853] text-[#0f0f1a] font-semibold rounded-lg hover:bg-[#c49742] active:bg-[#b58831] disabled:opacity-50 transition"
                            >
                                Save as Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    segmentCounts: Object,
});

const segments = [
    { value: 'all', label: 'All Customers', desc: 'Every customer with an email' },
    { value: 'regulars', label: 'Regulars', desc: '5+ visits' },
    { value: 'lapsed', label: 'Lapsed', desc: 'No visit in 60+ days' },
    { value: 'new', label: 'New Customers', desc: 'First visit in last 30 days' },
];

const form = useForm({
    subject: '',
    body_html: '',
    segment: 'all',
});

function submit() {
    form.post(route('marketing.campaigns.store'));
}
</script>
