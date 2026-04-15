<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="w-full rounded-2xl border-rose-200 bg-white/90 px-4 py-3 text-slate-900 shadow-sm shadow-rose-500/5 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-200"
        v-model="model"
        ref="input"
    />
</template>
