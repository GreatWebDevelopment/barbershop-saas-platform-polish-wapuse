<script setup>
defineProps({
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    animate: {
        type: Boolean,
        default: true,
    },
});

const sizes = {
    sm: { width: 'w-6', height: 'h-6', radius: 'rounded-full' },
    md: { width: 'w-8', height: 'h-8', radius: 'rounded-lg' },
    lg: { width: 'w-10', height: 'h-10', radius: 'rounded-xl' },
    pole: { width: 'w-6', height: 'h-16', radius: 'rounded-full' },
    tall: { width: 'w-8', height: 'h-48', radius: 'rounded-full' },
};
</script>

<template>
    <div
        class="barber-pole overflow-hidden relative"
        :class="[sizes[size]?.width || 'w-8', sizes[size]?.height || 'h-8', sizes[size]?.radius || 'rounded-lg', { 'animate-pole': animate }]"
    >
        <div class="barber-stripes absolute inset-0"></div>
        <div class="absolute inset-0 rounded-[inherit] ring-1 ring-white/20"></div>
    </div>
</template>

<style scoped>
.barber-stripes {
    background: repeating-linear-gradient(
        -45deg,
        #ffffff 0px,
        #ffffff 6px,
        #dc2626 6px,
        #dc2626 12px,
        #ffffff 12px,
        #ffffff 18px,
        #2563eb 18px,
        #2563eb 24px
    );
    background-size: 34px 34px;
}

.animate-pole .barber-stripes {
    animation: pole-spin 2s linear infinite;
}

@keyframes pole-spin {
    0% { background-position: 0 0; }
    100% { background-position: 0 34px; }
}
</style>
