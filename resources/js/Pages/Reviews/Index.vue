<template>
    <Head title="Reviews" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Reviews</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-[#D4A853]">{{ stats.average ? Number(stats.average).toFixed(1) : '—' }}</div>
                        <div class="text-sm text-gray-400 mt-1">Average Rating</div>
                        <div class="flex justify-center mt-2">
                            <span v-for="i in 5" :key="i" class="text-lg" :class="i <= Math.round(stats.average || 0) ? 'text-[#D4A853]' : 'text-gray-600'">&#9733;</span>
                        </div>
                    </div>
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-gray-100">{{ stats.total }}</div>
                        <div class="text-sm text-gray-400 mt-1">Total Reviews</div>
                    </div>
                    <div class="bg-[#16162a] border border-gray-800 rounded-xl p-5 text-center col-span-2">
                        <div class="text-sm text-gray-400 mb-2">Rating Distribution</div>
                        <div v-for="r in [5,4,3,2,1]" :key="r" class="flex items-center gap-2 text-sm">
                            <span class="w-4 text-gray-300">{{ r }}</span>
                            <span class="text-[#D4A853]">&#9733;</span>
                            <div class="flex-1 bg-gray-800 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#D4A853] h-full rounded-full transition-all" :style="{ width: stats.total ? ((stats.distribution[r] || 0) / stats.total * 100) + '%' : '0%' }"></div>
                            </div>
                            <span class="w-8 text-right text-gray-500">{{ stats.distribution[r] || 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 flex flex-wrap gap-3">
                    <select
                        v-model="filterRating"
                        @change="applyFilters"
                        class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-300 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                    >
                        <option value="">All Ratings</option>
                        <option v-for="r in [5,4,3,2,1]" :key="r" :value="r">{{ r }} Stars</option>
                    </select>
                    <select
                        v-model="filterSource"
                        @change="applyFilters"
                        class="rounded-lg border-gray-600 bg-[#1a1a2e] text-gray-300 focus:border-[#D4A853] focus:ring focus:ring-[#D4A853] focus:ring-opacity-50 px-3 py-2 text-sm"
                    >
                        <option value="">All Sources</option>
                        <option value="internal">Internal</option>
                        <option value="google">Google</option>
                        <option value="yelp">Yelp</option>
                    </select>
                </div>

                <!-- Reviews List -->
                <div v-if="reviews.data.length" class="space-y-4">
                    <div
                        v-for="review in reviews.data"
                        :key="review.id"
                        class="bg-[#16162a] border border-gray-800 rounded-xl p-5"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#D4A853]/20 flex items-center justify-center text-[#D4A853] font-bold text-sm">
                                        {{ review.customer ? (review.customer.first_name?.[0] || '') + (review.customer.last_name?.[0] || '') : '?' }}
                                    </div>
                                    <div>
                                        <div class="text-gray-100 font-medium">
                                            {{ review.customer ? review.customer.first_name + ' ' + review.customer.last_name : 'Anonymous' }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span v-for="i in 5" :key="i" class="text-sm" :class="i <= review.rating ? 'text-[#D4A853]' : 'text-gray-600'">&#9733;</span>
                                            <span class="text-xs text-gray-500 ml-1">{{ formatDate(review.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium"
                                :class="{
                                    'bg-blue-900/40 text-blue-400': review.source === 'google',
                                    'bg-red-900/40 text-red-400': review.source === 'yelp',
                                    'bg-gray-800 text-gray-400': review.source === 'internal',
                                }"
                            >
                                {{ review.source }}
                            </span>
                        </div>
                        <p v-if="review.comment" class="mt-3 text-gray-300 text-sm leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>

                <div v-else class="bg-[#16162a] border border-gray-800 rounded-xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                    <p class="mt-4 text-gray-400">No reviews yet.</p>
                </div>

                <!-- Pagination -->
                <div v-if="reviews.links && reviews.last_page > 1" class="mt-6 flex justify-center gap-1">
                    <Link
                        v-for="link in reviews.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-3 py-1.5 rounded text-sm"
                        :class="link.active ? 'bg-[#D4A853] text-[#0f0f1a] font-semibold' : 'text-gray-400 hover:text-gray-200 bg-[#16162a]'"
                        v-html="link.label"
                        :preserve-scroll="true"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reviews: Object,
    stats: Object,
    filters: Object,
});

const filterRating = ref(props.filters?.rating || '');
const filterSource = ref(props.filters?.source || '');

function applyFilters() {
    router.get(route('reviews.index'), {
        rating: filterRating.value || undefined,
        source: filterSource.value || undefined,
    }, { preserveState: true });
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>
