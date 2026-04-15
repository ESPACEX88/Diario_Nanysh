<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
    ? 'block w-full rounded-2xl border border-rose-200 bg-rose-100/90 px-4 py-3 text-start text-base font-semibold text-rose-800 shadow-sm shadow-rose-500/10 focus:outline-none focus:ring-2 focus:ring-rose-400 transition duration-150 ease-in-out'
    : 'block w-full rounded-2xl border border-transparent px-4 py-3 text-start text-base font-medium text-rose-800/80 hover:border-rose-200 hover:bg-white/70 hover:text-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-400 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
