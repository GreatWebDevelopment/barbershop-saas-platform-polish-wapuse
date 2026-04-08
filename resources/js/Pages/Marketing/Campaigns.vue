<template>
    <Head title="Email Campaigns" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Email Campaigns</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-900/30 border border-green-800 text-green-400 rounded-lg px-4 py-3 text-sm">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 bg-red-900/30 border border-red-800 text-red-400 rounded-lg px-4 py-3 text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-400 text-sm">Create and send email campaigns to your customer segments.</p>
                    <Link
                        :href="route('marketing.campaigns.create')"
                        class="inline-flex items-center px-5 py-2.5 bg-[#D4A853] text-[#0f0f1a] font-semibold rounded-lg hover:bg-[#c49742] active:bg-[#b58831] transition text-sm"
                    >
                        + New Campaign
                    </Link>
                </div>

                <!-- Campaigns List -->
                <div v-if="campaigns.data.length" class="space-y-4">
                    <div
                        v-for="campaign in campaigns.data"
                        :key="campaign.id"
                        class="bg-[#16162a] border border-gray-800 rounded-xl p-5 flex items-center justify-between"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="text-gray-100 font-medium truncate">{{ campaign.subject }}</h3>
                                <span
                                    class="px-2 py-0.5 rounded text-xs font-medium shrink-0"
                                    :class="campaign.status === 'sent' ? 'bg-green-900/40 text-green-400' : 'bg-yellow-900/40 text-yellow-400'"
                                >
                                    {{ campaign.status }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 mt-1.5 text-sm text-gray-400">
                                <span class="capitalize">{{ campaign.segment }} customers</span>
                                <span v-if="campaign.sent_at">Sent {{ formatDate(campaign.sent_at) }}</span>
                                <span v-if="campaign.recipient_count">{{ campaign.recipient_count }} recipients</span>
                            </div>
                        </div>
                        <div v-if="campaign.status === 'draft'" class="ml-4">
                            <Link
                                :href="route('marketing.campaigns.send', campaign.id)"
                                method="post"
                                as="button"
                                class="px-4 py-2 bg-[#D4A853]/20 text-[#D4A853] font-medium rounded-lg hover:bg-[#D4A853]/30 transition text-sm"
                            >
                                Send
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-[#16162a] border border-gray-800 rounded-xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <p class="mt-4 text-gray-400">No campaigns yet. Create your first one!</p>
                </div>

                <!-- Pagination -->
                <div v-if="campaigns.links && campaigns.last_page > 1" class="mt-6 flex justify-center gap-1">
                    <Link
                        v-for="link in campaigns.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-3 py-1.5 rounded text-sm"
                        :class="link.active ? 'bg-[#D4A853] text-[#0f0f1a] font-semibold' : 'text-gray-400 hover:text-gray-200 bg-[#16162a]'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    campaigns: Object,
});

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>
