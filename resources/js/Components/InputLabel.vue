<script setup lang="ts">
defineProps<{
    value?: string;
}>();
</script>

<template>
    <label class="block text-sm font-semibold text-rose-900/90">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
