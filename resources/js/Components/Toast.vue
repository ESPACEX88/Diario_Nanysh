<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Props {
    message: string;
    type?: 'success' | 'error' | 'info' | 'warning';
    duration?: number;
    show?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
    duration: 3000,
    show: true,
});

const emit = defineEmits<{
    close: [];
}>();

const isVisible = ref(props.show);

const typeStyles = {
    success: 'bg-green-500 border-green-600',
    error: 'bg-red-500 border-red-600',
    info: 'bg-blue-500 border-blue-600',
    warning: 'bg-yellow-500 border-yellow-600',
};

const icons = {
    success: '✅',
    error: '❌',
    info: 'ℹ️',
    warning: '⚠️',
};

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(() => {
            close();
        }, props.duration);
    }
});

const close = () => {
    isVisible.value = false;
    setTimeout(() => {
        emit('close');
    }, 300);
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="isVisible"
            :class="[
                'fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl border-2 text-white font-semibold flex items-center gap-3 min-w-[300px] max-w-md',
                typeStyles[type],
            ]"
        >
            <span class="text-2xl">{{ icons[type] }}</span>
            <p class="flex-1">{{ message }}</p>
            <button
                @click="close"
                class="text-white/80 hover:text-white transition-colors"
            >
                ✕
            </button>
        </div>
    </Transition>
</template>

