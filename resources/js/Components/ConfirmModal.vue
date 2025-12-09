<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

interface Props {
    show: boolean;
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    type?: 'danger' | 'warning' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    type: 'danger',
});

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

const handleConfirm = () => {
    emit('confirm');
};

const handleCancel = () => {
    emit('cancel');
};

const handleEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        handleCancel();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
});

const typeColors = {
    danger: 'from-red-500 to-pink-500',
    warning: 'from-amber-500 to-orange-500',
    info: 'from-blue-500 to-indigo-500',
};

const typeIcons = {
    danger: '⚠️',
    warning: '⚠️',
    info: 'ℹ️',
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="handleCancel"
            >
                <div class="flex min-h-full items-center justify-center p-4">
                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="show"
                            class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all w-full max-w-md"
                            @click.stop
                        >
                            <!-- Background decoration -->
                            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-200/20 to-rose-200/20 rounded-full -mr-20 -mt-20"></div>
                            
                            <div class="relative p-8">
                                <!-- Icon -->
                                <div class="flex justify-center mb-4">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br flex items-center justify-center text-4xl shadow-lg" :class="`${typeColors[type]}`">
                                        {{ typeIcons[type] }}
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-3">
                                    {{ title }}
                                </h3>

                                <!-- Message -->
                                <p class="text-center text-gray-600 dark:text-gray-300 mb-8">
                                    {{ message }}
                                </p>

                                <!-- Actions -->
                                <div class="flex gap-4">
                                    <button
                                        @click="handleCancel"
                                        class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all"
                                    >
                                        {{ cancelText }}
                                    </button>
                                    <button
                                        @click="handleConfirm"
                                        class="flex-1 px-6 py-3 bg-gradient-to-r text-white rounded-full font-semibold hover:shadow-lg transition-all transform hover:scale-105" :class="typeColors[type]"
                                    >
                                        {{ confirmText }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

