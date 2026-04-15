<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface MediaItem {
    id: number;
    type: string;
    title: string;
    author?: string;
    description?: string;
    status: string;
    rating?: number;
    review?: string;
    started_date?: string;
    completed_date?: string;
    url?: string;
}

interface PaginatedResponse<T> {
    data: T[];
}

interface Props {
    items: PaginatedResponse<MediaItem> | MediaItem[];
}

const props = defineProps<Props>();

const getTypeIcon = (type: string) => {
    const icons: Record<string, string> = {
        book: '📚',
        movie: '🎬',
        series: '📺',
        music: '🎵',
        podcast: '🎙️',
    };
    return icons[type] || '📖';
};

const statusMap: Record<string, 'want' | 'in_progress' | 'completed'> = {
    want: 'want',
    wishlist: 'want',
    pendiente: 'want',
    por_ver: 'want',
    in_progress: 'in_progress',
    progress: 'in_progress',
    leyendo: 'in_progress',
    viendo: 'in_progress',
    completed: 'completed',
    done: 'completed',
    terminado: 'completed',
    finalizado: 'completed',
};

const mediaItems = computed<MediaItem[]>(() =>
    Array.isArray(props.items) ? props.items : (props.items?.data ?? [])
);

const normalizeStatus = (status: string) => statusMap[(status || '').toLowerCase()] || 'want';

const wantItems = computed(() => mediaItems.value.filter((item: MediaItem) => normalizeStatus(item.status) === 'want'));
const inProgressItems = computed(() => mediaItems.value.filter((item: MediaItem) => normalizeStatus(item.status) === 'in_progress'));
const completedItems = computed(() => mediaItems.value.filter((item: MediaItem) => normalizeStatus(item.status) === 'completed'));

const showDeleteModal = ref(false);
const itemToDelete = ref<number | null>(null);

const deleteItem = (id: number) => {
    itemToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (itemToDelete.value) {
        router.delete(route('media.destroy', itemToDelete.value));
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
    <Head title="Libros y Películas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-2xl font-bold text-rose-950">
                    <span>📚</span>
                    Mis Libros y Películas
                </h2>
                <Link
                    :href="route('media.create')"
                    class="rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-6 py-3 font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
                >
                    + Agregar
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl space-y-10 px-4 sm:px-6 lg:px-8">
                <!-- Want to Read/Watch -->
                <div v-if="wantItems.length > 0" class="mb-10">
                    <h3 class="mb-6 flex items-center gap-3 text-2xl font-bold text-rose-950">
                        <span class="text-3xl">📖</span>
                        Quiero Ver/Leer ({{ wantItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in wantItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 shadow-[0_18px_50px_rgba(236,72,153,0.08)] transition-all hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(236,72,153,0.14)]"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="mb-2 text-xl font-bold text-rose-950">{{ item.title }}</h4>
                                <p v-if="item.author" class="mb-4 text-sm text-rose-900/65">por {{ item.author }}</p>
                                <p v-if="item.description" class="mb-4 line-clamp-2 text-sm text-rose-900/65">
                                    {{ item.description }}
                                </p>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('media.edit', item.id)"
                                        class="flex-1 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:-translate-y-px"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="deleteItem(item.id)"
                                        class="rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-200"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In Progress -->
                <div v-if="inProgressItems.length > 0" class="mb-10">
                    <h3 class="mb-6 flex items-center gap-3 text-2xl font-bold text-rose-950">
                        <span class="text-3xl">📖</span>
                        En Progreso ({{ inProgressItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in inProgressItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-fuchsia-50 to-rose-50 shadow-[0_18px_50px_rgba(236,72,153,0.08)] transition-all hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(236,72,153,0.14)]"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="mb-2 text-xl font-bold text-rose-950">{{ item.title }}</h4>
                                <p v-if="item.author" class="mb-4 text-sm text-rose-900/65">por {{ item.author }}</p>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('media.edit', item.id)"
                                        class="flex-1 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:-translate-y-px"
                                    >
                                        Editar
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed -->
                <div v-if="completedItems.length > 0" class="mb-10">
                    <h3 class="mb-6 flex items-center gap-3 text-2xl font-bold text-rose-950">
                        <span class="text-3xl">✅</span>
                        Completados ({{ completedItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in completedItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 shadow-[0_18px_50px_rgba(236,72,153,0.08)] transition-all hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(236,72,153,0.14)]"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="mb-2 text-xl font-bold text-rose-950">{{ item.title }}</h4>
                                <p v-if="item.author" class="mb-2 text-sm text-rose-900/65">por {{ item.author }}</p>
                                <div v-if="item.rating" class="mb-4">
                                    <div class="flex gap-1">
                                        <span v-for="i in 5" :key="i" class="text-xl">
                                            {{ i <= item.rating! ? '⭐' : '☆' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('media.edit', item.id)"
                                        class="flex-1 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:-translate-y-px"
                                    >
                                        Ver
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="mediaItems.length === 0"
                    class="rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 px-6 py-20 text-center shadow-[0_24px_70px_rgba(236,72,153,0.12)]"
                >
                    <span class="mb-6 block text-8xl">📚</span>
                    <h3 class="mb-3 text-3xl font-bold text-rose-950">No hay libros o películas aún</h3>
                    <p class="mx-auto mb-8 max-w-md text-lg text-rose-900/70">
                        Agrega los libros, películas, series o música que quieres disfrutar
                    </p>
                    <Link
                        :href="route('media.create')"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-8 py-4 text-lg font-bold text-white shadow-xl shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-2xl"
                    >
                        <span class="text-2xl" aria-hidden="true">✨</span>
                        Agregar Mi Primera Lista
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Artículo"
            message="¿Estás segura de que quieres eliminar este artículo? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

