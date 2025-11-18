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
    dream: Dream;
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
    <Head :title="dream.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>{{ getTypeIcon(dream.type) }}</span>
                    Ver Sueño
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('dreams.edit', dream.id)"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-all"
                    >
                        Editar
                    </Link>
                    <button
                        @click="deleteDream(dream.id)"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-all"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-purple-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-5xl">{{ getTypeIcon(dream.type) }}</span>
                                <div class="flex-1">
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ dream.title }}</h1>
                                    <span
                                        :class="['px-4 py-2 rounded-full text-sm font-bold text-white bg-gradient-to-r inline-block', getTypeColor(dream.type)]"
                                    >
                                        {{ dream.type === 'normal' ? 'Normal' : dream.type === 'lucid' ? 'Lúcido' : dream.type === 'nightmare' ? 'Pesadilla' : 'Recurrente' }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4">
                                📅 {{ new Date(dream.date).toLocaleDateString('es-ES', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) }}
                            </p>
                            <div v-if="dream.mood" class="mb-4">
                                <span class="text-4xl">{{ dream.mood }}</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Descripción del Sueño</h3>
                            <div class="bg-white/80 rounded-xl p-6 border-2 border-pink-200">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ dream.content }}</p>
                            </div>
                        </div>

                        <div v-if="dream.tags && dream.tags.length > 0" class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Etiquetas</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in dream.tags"
                                    :key="tag"
                                    class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm font-semibold"
                                >
                                    #{{ tag }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6 border-t-2 border-pink-200">
                            <Link
                                :href="route('dreams.index')"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
                            >
                                ← Volver
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

