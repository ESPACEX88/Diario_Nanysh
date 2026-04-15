<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    href: string;
}>();
</script>

<template>
    <Link
        :href="href"
        class="block w-full rounded-2xl px-4 py-2.5 text-start text-sm font-medium leading-5 text-rose-800 transition duration-150 ease-in-out hover:bg-rose-50 focus:bg-rose-50 focus:outline-none"
    >
        <slot />
    </Link>
</template>
