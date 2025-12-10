<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface Counter {
    id: number;
    name: string;
    description?: string;
    start_date: string;
    days_count: number;
    icon?: string;
    color: string;
    is_future?: boolean;
    days_remaining?: number;
}

interface Props {
    counters: Counter[];
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);
const counterToDelete = ref<number | null>(null);

const deleteCounter = (id: number) => {
    counterToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (counterToDelete.value) {
        router.delete(route('counters.destroy', counterToDelete.value));
    }
    showDeleteModal.value = false;
    counterToDelete.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    counterToDelete.value = null;
};
</script>

<template>
    <Head title="Contadores de Días" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📅</span>
                    Mis Contadores Especiales
                </h2>
                <Link
                    :href="route('counters.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Nuevo Contador
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div v-if="counters.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="counter in counters"
                        :key="counter.id"
                        class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 border-4 border-pink-300 hover:border-pink-400 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-2"
                    >
                        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-200/40 to-rose-200/40 rounded-full -mr-20 -mt-20"></div>
                        <div class="relative p-8 text-center">
                            <div class="text-7xl mb-4 transform group-hover:scale-110 transition-transform">
                                {{ counter.icon || '💕' }}
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ counter.name }}</h3>
                            <p v-if="counter.description" class="text-gray-600 text-sm mb-6">
                                {{ counter.description }}
                            </p>
                            <div class="mb-6">
                                <div class="text-5xl font-bold mb-2" :class="counter.days_count < 0 ? 'bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent' : 'bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent'">
                                    {{ Math.abs(counter.days_count) }}
                                </div>
                                <div class="text-sm font-semibold" :class="counter.days_count < 0 ? 'text-blue-600' : 'text-gray-600'">
                                    <span v-if="counter.days_count < 0">
                                        {{ Math.abs(counter.days_count) === 1 ? 'día faltante' : 'días faltantes' }}
                                    </span>
                                    <span v-else>
                                        {{ counter.days_count === 1 ? 'día transcurrido' : 'días transcurridos' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mb-4">
                                <span v-if="counter.days_count < 0">
                                    Hasta: {{ new Date(counter.start_date).toLocaleDateString('es-ES') }}
                                </span>
                                <span v-else>
                                    Desde: {{ new Date(counter.start_date).toLocaleDateString('es-ES') }}
                                </span>
                            </div>
                            <div class="flex gap-2 justify-center">
                                <Link
                                    :href="route('counters.edit', counter.id)"
                                    class="px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-full text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteCounter(counter.id)"
                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-semibold hover:bg-red-200 transition-all"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 rounded-3xl border-4 border-pink-300"
                >
                    <span class="text-8xl block mb-6 animate-pulse">💕</span>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">No hay contadores aún</h3>
                    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                        Crea contadores para días especiales como "Días juntos", "Días sin fumar", etc.
                    </p>
                    <Link
                        :href="route('counters.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Crear Mi Primer Contador
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Contador"
            message="¿Estás segura de que quieres eliminar este contador? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

