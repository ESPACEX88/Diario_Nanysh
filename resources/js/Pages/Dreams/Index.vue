<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Dream {
    id: number;
    title: string;
    content: string;
    date: string;
    type: string;
    mood?: string;
    tags?: string[];
}

interface Props {
    dreams: Dream[];
}

const props = defineProps<Props>();

const getTypeIcon = (type: string) => {
    const icons: Record<string, string> = {
        normal: '🌙',
        lucid: '✨',
        nightmare: '😰',
        recurring: '🔄',
    };
    return icons[type] || '🌙';
};

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        normal: 'from-blue-400 to-indigo-500',
        lucid: 'from-purple-400 to-pink-500',
        nightmare: 'from-red-400 to-rose-500',
        recurring: 'from-amber-400 to-orange-500',
    };
    return colors[type] || 'from-pink-400 to-rose-400';
};

const deleteDream = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar este sueño?')) {
        router.delete(route('dreams.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Sueños" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>🌙</span>
                    Mis Sueños
                </h2>
                <Link
                    :href="route('dreams.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Registrar Sueño
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="dreams.length > 0" class="max-h-96 overflow-y-auto space-y-3 pr-2">
                    <div
                        v-for="dream in dreams"
                        :key="dream.id"
                        class="group relative overflow-hidden rounded-lg bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 border-2 border-pink-200 dark:border-gray-700 hover:border-pink-400 dark:hover:border-pink-500 transition-all shadow-sm hover:shadow-md"
                    >
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-pink-200/20 to-purple-200/20 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ getTypeIcon(dream.type) }}</span>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">{{ dream.title }}</h3>
                                        <span
                                            :class="['px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r', getTypeColor(dream.type)]"
                                        >
                                            {{ dream.type === 'normal' ? 'Normal' : dream.type === 'lucid' ? 'Lúcido' : dream.type === 'nightmare' ? 'Pesadilla' : 'Recurrente' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 block">
                                        📅 {{ new Date(dream.date).toLocaleDateString('es-ES', {
                                            day: 'numeric',
                                            month: 'short',
                                            year: 'numeric'
                                        }) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ dream.created_at ? new Date(dream.created_at).toLocaleDateString('es-ES', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : '' }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 mb-2 text-sm line-clamp-2">
                                {{ dream.content }}
                            </p>
                            <div v-if="dream.mood" class="mb-2">
                                <span class="text-xl">{{ dream.mood }}</span>
                            </div>
                            <div v-if="dream.tags && dream.tags.length > 0" class="flex flex-wrap gap-1 mb-2">
                                <span
                                    v-for="tag in dream.tags"
                                    :key="tag"
                                    class="px-3 py-1 bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-300 rounded-full text-xs font-semibold"
                                >
                                    #{{ tag }}
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('dreams.show', dream.id)"
                                    class="px-3 py-1 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-lg text-xs font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                                >
                                    Ver más
                                </Link>
                                <Link
                                    :href="route('dreams.edit', dream.id)"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-200 transition-all"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteDream(dream.id)"
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold hover:bg-red-200 transition-all"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-purple-100 to-indigo-100 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 rounded-3xl border-4 border-pink-300 dark:border-gray-700"
                >
                    <span class="text-8xl block mb-6 animate-pulse">🌙</span>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-3">No has registrado sueños aún</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Registra tus sueños para recordarlos y analizarlos
                    </p>
                    <Link
                        :href="route('dreams.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Registrar Mi Primer Sueño
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

