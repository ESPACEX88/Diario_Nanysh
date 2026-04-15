<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
    ? 'inline-flex items-center rounded-full border border-rose-200 bg-rose-100/90 px-3 py-2 text-sm font-semibold leading-5 text-rose-800 shadow-sm shadow-rose-500/10 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 transition duration-150 ease-in-out'
    : 'inline-flex items-center rounded-full border border-transparent px-3 py-2 text-sm font-medium leading-5 text-rose-700/80 transition duration-150 ease-in-out hover:border-rose-200 hover:bg-white/70 hover:text-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
