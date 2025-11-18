<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    entry: {
        id: number;
        title: string;
        content: string;
        mood: string;
        date: string;
        is_favorite: boolean;
    };
}

const props = defineProps<Props>();

// Estado local para el favorito
const isFavorite = ref(props.entry.is_favorite);

const deleteEntry = () => {
    if (confirm('¿Estás seguro de que quieres eliminar esta entrada?')) {
        router.delete(route('diary.destroy', props.entry.id));
    }
};

const toggleFavorite = () => {
    // Actualizar el estado local inmediatamente para feedback visual
    const previousValue = isFavorite.value;
    isFavorite.value = !isFavorite.value;
    
    router.post(route('diary.favorite', props.entry.id), {}, {
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // Si hay error, revertir el estado
            isFavorite.value = previousValue;
        }
    });
};
</script>

<template>
    <Head :title="entry.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ entry.mood }}</span>
                    <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        {{ entry.title }}
                    </h2>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="toggleFavorite"
                        :class="[
                            'px-5 py-2.5 rounded-full font-semibold shadow-md hover:shadow-lg transition-all',
                            isFavorite
                                ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white hover:from-yellow-500 hover:to-orange-600'
                                : 'bg-gradient-to-r from-gray-400 to-gray-500 text-white hover:from-gray-500 hover:to-gray-600'
                        ]"
                    >
                        {{ isFavorite ? '⭐ Favorito' : '☆ Marcar Favorito' }}
                    </button>
                    <Link
                        :href="route('diary.edit', entry.id)"
                        class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full font-semibold hover:from-purple-600 hover:to-pink-600 shadow-md hover:shadow-lg transition-all"
                    >
                        ✏️ Editar
                    </Link>
                    <button
                        @click="deleteEntry"
                        class="px-5 py-2.5 bg-gradient-to-r from-red-400 to-rose-500 text-white rounded-full font-semibold hover:from-red-500 hover:to-rose-600 shadow-md hover:shadow-lg transition-all"
                    >
                        🗑️ Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 shadow-xl border-2 border-pink-200">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-pink-200/40 to-purple-200/40 rounded-full -mr-24 -mt-24"></div>
                    <div class="relative p-8">
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b-2 border-pink-200">
                            <span class="text-5xl">{{ entry.mood }}</span>
                            <div>
                                <p class="text-base font-semibold text-gray-700">
                                    {{ new Date(entry.date).toLocaleDateString('es-ES', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) }}
                                </p>
                                <span
                                    v-if="isFavorite"
                                    class="inline-flex items-center gap-1 text-pink-600 font-semibold text-sm mt-1"
                                >
                                    💖 Favorito
                                </span>
                            </div>
                        </div>
                        <div class="prose max-w-none">
                            <p
                                class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap"
                                v-html="entry.content.replace(/\n/g, '<br>')"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

