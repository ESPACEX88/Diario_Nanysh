<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

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

interface Props {
    items: MediaItem[];
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

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        want: 'from-gray-400 to-gray-500',
        in_progress: 'from-blue-400 to-indigo-500',
        completed: 'from-green-400 to-emerald-500',
    };
    return colors[status] || 'from-pink-400 to-rose-400';
};

const wantItems = computed(() => props.items.filter(item => item.status === 'want'));
const inProgressItems = computed(() => props.items.filter(item => item.status === 'in_progress'));
const completedItems = computed(() => props.items.filter(item => item.status === 'completed'));

const deleteItem = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar este artículo?')) {
        router.delete(route('media.destroy', id));
    }
};
</script>

<template>
    <Head title="Libros y Películas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📚</span>
                    Mis Libros y Películas
                </h2>
                <Link
                    :href="route('media.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Agregar
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Want to Read/Watch -->
                <div v-if="wantItems.length > 0" class="mb-10">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="text-3xl">📖</span>
                        Quiero Ver/Leer ({{ wantItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in wantItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-2 border-pink-200 hover:border-pink-400 transition-all shadow-lg hover:shadow-xl"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ item.title }}</h4>
                                <p v-if="item.author" class="text-gray-600 text-sm mb-4">por {{ item.author }}</p>
                                <p v-if="item.description" class="text-gray-600 text-sm line-clamp-2 mb-4">
                                    {{ item.description }}
                                </p>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('media.edit', item.id)"
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-lg text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all text-center"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="deleteItem(item.id)"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all"
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
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="text-3xl">📖</span>
                        En Progreso ({{ inProgressItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in inProgressItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border-2 border-blue-200 hover:border-blue-400 transition-all shadow-lg hover:shadow-xl"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ item.title }}</h4>
                                <p v-if="item.author" class="text-gray-600 text-sm mb-4">por {{ item.author }}</p>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('media.edit', item.id)"
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-400 to-indigo-400 text-white rounded-lg text-sm font-semibold hover:from-blue-500 hover:to-indigo-500 transition-all text-center"
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
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="text-3xl">✅</span>
                        Completados ({{ completedItems.length }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in completedItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 border-2 border-green-200 hover:border-green-400 transition-all shadow-lg hover:shadow-xl"
                        >
                            <div class="p-6">
                                <div class="text-5xl mb-4">{{ getTypeIcon(item.type) }}</div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ item.title }}</h4>
                                <p v-if="item.author" class="text-gray-600 text-sm mb-2">por {{ item.author }}</p>
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
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-green-400 to-emerald-400 text-white rounded-lg text-sm font-semibold hover:from-green-500 hover:to-emerald-500 transition-all text-center"
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
                    v-if="items.length === 0"
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 rounded-3xl border-4 border-pink-300"
                >
                    <span class="text-8xl block mb-6">📚</span>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">No hay libros o películas aún</h3>
                    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                        Agrega los libros, películas, series o música que quieres disfrutar
                    </p>
                    <Link
                        :href="route('media.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Agregar Mi Primera Lista
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

