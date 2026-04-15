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
                            class="relative w-full max-w-md transform overflow-hidden rounded-[2rem] border border-rose-100/80 bg-white/95 shadow-[0_24px_80px_rgba(236,72,153,0.18)] backdrop-blur-xl transition-all"
                            @click.stop
                        >
                            <!-- Background decoration -->
                            <div class="absolute top-0 right-0 h-40 w-40 rounded-full bg-gradient-to-br from-rose-200/30 to-fuchsia-200/30 -mr-20 -mt-20"></div>
                            
                            <div class="relative p-8">
                                <!-- Icon -->
                                <div class="flex justify-center mb-4">
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br text-4xl shadow-lg shadow-rose-500/10" :class="`${typeColors[type]}`">
                                        {{ typeIcons[type] }}
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="mb-3 text-center text-2xl font-bold text-rose-950">
                                    {{ title }}
                                </h3>

                                <!-- Message -->
                                <p class="mb-8 text-center text-rose-900/70">
                                    {{ message }}
                                </p>

                                <!-- Actions -->
                                <div class="flex gap-4">
                                    <button
                                        @click="handleCancel"
                                        class="flex-1 rounded-full bg-rose-50 px-6 py-3 font-semibold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        {{ cancelText }}
                                    </button>
                                    <button
                                        @click="handleConfirm"
                                        class="flex-1 rounded-full px-6 py-3 font-semibold text-white transition hover:shadow-lg hover:shadow-rose-500/20" :class="typeColors[type]"
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

