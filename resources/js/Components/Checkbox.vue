<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
    checked: boolean;
    value?: any;
}>();

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="size-5 rounded-md border-rose-200 text-rose-500 shadow-sm shadow-rose-500/10 focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-white"
    />
</template>
