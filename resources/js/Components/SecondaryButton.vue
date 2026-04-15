<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-white/85 px-5 py-3 text-sm font-semibold text-rose-700 shadow-sm shadow-rose-500/10 transition duration-200 ease-out hover:-translate-y-px hover:border-rose-300 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </button>
</template>
