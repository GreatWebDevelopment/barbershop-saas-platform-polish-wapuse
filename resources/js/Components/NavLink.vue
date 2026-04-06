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
        ? 'bg-[#D4A853] text-[#1a1a2e]'
        : 'text-[#f5f0e8] hover:text-[#D4A853] hover:bg-[#16162a]',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
