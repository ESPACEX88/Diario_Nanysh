<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    notes: {
        data: any[];
        links: any;
        meta: any;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Notas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mis Notas
                </h2>
                <Link
                    :href="route('notes.create')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                >
                    Nueva Nota
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="notes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="note in notes.data"
                        :key="note.id"
                        :href="route('notes.show', note.id)"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow p-6"
                        :style="{ borderLeft: `4px solid ${note.color || '#fbbf24'}` }"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ note.title }}
                            </h3>
                            <span
                                v-if="note.is_pinned"
                                class="text-yellow-500"
                                title="Fijada"
                            >
                                📌
                            </span>
                        </div>
                        <p
                            v-if="note.content"
                            class="text-gray-600 dark:text-gray-300 line-clamp-3 text-sm"
                        >
                            {{ note.content }}
                        </p>
                    </Link>
                </div>
                <div
                    v-else
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center py-12"
                >
                    <span class="text-6xl mb-4 block">📝</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay notas aún
                    </h3>
                    <Link
                        :href="route('notes.create')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                    >
                        Crear Primera Nota
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

