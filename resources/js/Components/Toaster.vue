<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import Toast from './Toast.vue';

const { toasts, remove } = useToast();
</script>

<template>
    <div class="fixed top-4 right-4 z-50 space-y-3">
        <Toast
            v-for="toast in toasts"
            :key="toast.id"
            :message="toast.message"
            :type="toast.type"
            :duration="toast.duration"
            @close="remove(toast.id)"
        />
    </div>
</template>

