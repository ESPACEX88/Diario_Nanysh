<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-show="message" class="mt-2 flex items-center gap-2">
            <span class="text-red-500">⚠️</span>
            <p class="text-sm font-semibold text-red-600">
                {{ message }}
            </p>
        </div>
    </Transition>
</template>
