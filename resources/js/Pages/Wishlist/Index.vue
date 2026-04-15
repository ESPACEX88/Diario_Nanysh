<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface WishlistItem {
    id: number;
    name: string;
    description?: string;
    category: string;
    price?: number;
    url?: string;
    priority: string;
    is_obtained: boolean;
    obtained_date?: string;
}

interface Props {
    items: WishlistItem[];
}

const props = defineProps<Props>();

const pendingItems = computed(() => 
    props.items.filter(item => !item.is_obtained)
);

const obtainedItems = computed(() => 
    props.items.filter(item => item.is_obtained)
);

const getCategoryIcon = (category: string) => {
    const icons: Record<string, string> = {
        product: '🛍️',
        experience: '✨',
        book: '📚',
        movie: '🎬',
        other: '💝',
    };
    return icons[category] || '💝';
};

const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'high': return 'from-red-400 to-pink-500';
        case 'medium': return 'from-amber-400 to-orange-400';
        case 'low': return 'from-green-400 to-emerald-400';
        default: return 'from-pink-400 to-rose-400';
    }
};

const showDeleteModal = ref(false);
const itemToDelete = ref<number | null>(null);

const deleteItem = (id: number) => {
    itemToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (itemToDelete.value) {
        router.delete(route('wishlist.destroy', itemToDelete.value));
    }
    showDeleteModal.value = false;
    itemToDelete.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    itemToDelete.value = null;
};
</script>

<template>
    <Head title="Lista de Deseos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-2xl font-bold text-rose-950">
                    <span>💝</span>
                    Mi Lista de Deseos
                </h2>
                <Link
                    :href="route('wishlist.create')"
                    class="rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-6 py-3 font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
                >
                    + Agregar Deseo
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
                <!-- Pending Items -->
                <div v-if="pendingItems.length > 0" class="mb-6">
                    <h3 class="mb-3 flex items-center gap-2 text-lg font-bold text-rose-950">
                        <span class="text-2xl">✨</span>
                        Deseos Pendientes ({{ pendingItems.length }})
                    </h3>
                    <div class="max-h-96 overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div
                                v-for="item in pendingItems"
                                :key="item.id"
                                class="group relative overflow-hidden rounded-[1.75rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 shadow-[0_16px_40px_rgba(236,72,153,0.08)] transition hover:-translate-y-px hover:shadow-[0_22px_60px_rgba(236,72,153,0.14)]"
                            >
                                <div class="absolute top-0 right-0 h-20 w-20 rounded-full bg-gradient-to-br from-rose-200/30 to-fuchsia-200/30 -mr-10 -mt-10"></div>
                                <div class="relative p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="text-3xl">{{ getCategoryIcon(item.category) }}</div>
                                        <span
                                            :class="['px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r', getPriorityColor(item.priority)]"
                                        >
                                            {{ item.priority === 'high' ? 'Alta' : item.priority === 'medium' ? 'Media' : 'Baja' }}
                                        </span>
                                    </div>
                                    <h4 class="mb-1 text-base font-bold text-rose-950">{{ item.name }}</h4>
                                    <p v-if="item.description" class="mb-2 line-clamp-1 text-xs text-rose-900/65">
                                        {{ item.description }}
                                    </p>
                                    <div class="flex items-center justify-between mb-2">
                                        <span v-if="item.price" class="text-lg font-bold text-rose-600">
                                            💰 ${{ item.price.toLocaleString() }}
                                        </span>
                                        <span class="text-xs capitalize text-rose-800/60">
                                            {{ item.category }}
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('wishlist.edit', item.id)"
                                            class="flex-1 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-3 py-1.5 text-center text-xs font-semibold text-white transition hover:-translate-y-px"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteItem(item.id)"
                                            class="rounded-full bg-rose-100 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-200"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                    <a
                                        v-if="item.url"
                                        :href="item.url"
                                        target="_blank"
                                        class="mt-2 block text-center text-xs font-semibold text-rose-600 hover:text-rose-700"
                                    >
                                        Ver enlace →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Obtained Items -->
                <div v-if="obtainedItems.length > 0" class="mb-6">
                    <h3 class="mb-3 flex items-center gap-2 text-lg font-bold text-rose-950">
                        <span class="text-2xl">🎉</span>
                        Deseos Cumplidos ({{ obtainedItems.length }})
                    </h3>
                    <div class="max-h-64 overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div
                                v-for="item in obtainedItems"
                                :key="item.id"
                                class="relative overflow-hidden rounded-[1.75rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 opacity-80 shadow-[0_12px_30px_rgba(236,72,153,0.06)]"
                            >
                                <div class="absolute top-2 right-2 text-xl">✅</div>
                                <div class="p-4">
                                    <div class="text-3xl mb-2 opacity-50">{{ getCategoryIcon(item.category) }}</div>
                                    <h4 class="mb-1 text-sm font-bold text-rose-900 line-through">{{ item.name }}</h4>
                                    <p v-if="item.obtained_date" class="text-xs text-rose-800/60">
                                        Obtenido: {{ new Date(item.obtained_date).toLocaleDateString('es-ES') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="items.length === 0"
                    class="rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 px-6 py-20 text-center shadow-[0_24px_70px_rgba(236,72,153,0.12)]"
                >
                    <div class="relative">
                        <span class="mb-6 block text-8xl">💝</span>
                        <h3 class="mb-3 text-3xl font-bold text-rose-950">Tu lista de deseos está vacía</h3>
                        <p class="mx-auto mb-8 max-w-md text-lg text-rose-900/70">
                            Agrega tus sueños y deseos aquí para recordarlos siempre
                        </p>
                        <Link
                            :href="route('wishlist.create')"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-8 py-4 text-lg font-bold text-white shadow-xl shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-2xl"
                        >
                            <span class="text-2xl" aria-hidden="true">✨</span>
                            Agregar Mi Primer Deseo
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Deseo"
            message="¿Estás segura de que quieres eliminar este artículo de tu lista de deseos? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

