<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#D4A853] text-start text-base font-medium text-[#D4A853] bg-[#16162a] focus:outline-none focus:text-[#f5f0e8] focus:bg-[#16162a] focus:border-[#D4A853] transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a] hover:border-gray-700 focus:outline-none focus:text-[#D4A853] focus:bg-[#16162a] focus:border-gray-700 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
