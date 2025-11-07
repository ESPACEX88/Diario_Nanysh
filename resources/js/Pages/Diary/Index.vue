<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    entries: {
        data: any[];
        links: any;
        meta: any;
    };
}

const props = defineProps<Props>();
const searchQuery = ref('');

const search = () => {
    router.get(route('diary.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Diario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">📖</span>
                    <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        Mi Diario
                    </h2>
                </div>
                <Link
                    :href="route('diary.create')"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 border border-transparent rounded-full font-bold text-sm text-white shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                >
                    <span class="mr-2">✨</span>
                    Nueva Entrada
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Search -->
                <div class="mb-6">
                    <div class="flex gap-2">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar en el diario..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            @keyup.enter="search"
                        />
                        <button
                            @click="search"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                        >
                            Buscar
                        </button>
                    </div>
                </div>

                <!-- Entries List -->
                <div v-if="entries.data.length > 0" class="max-h-96 overflow-y-auto space-y-2 pr-2">
                    <div
                        v-for="entry in entries.data"
                        :key="entry.id"
                        class="group relative overflow-hidden rounded-lg bg-gradient-to-br from-pink-50 to-rose-50 shadow-sm hover:shadow-md border-2 border-pink-100 hover:border-pink-300 transition-all"
                    >
                        <Link
                            :href="route('diary.show', entry.id)"
                            class="block p-3"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                            {{ entry.title }}
                                        </h3>
                                        <span class="text-xl">{{ entry.mood }}</span>
                                        <span
                                            v-if="entry.is_favorite"
                                            class="text-yellow-500"
                                            title="Favorito"
                                        >
                                            ⭐
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-1 mb-1">
                                        {{ entry.content }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ new Date(entry.date).toLocaleDateString('es-ES', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        }) }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="entries.links.length > 3"
                        class="flex justify-center gap-2 mt-6"
                    >
                        <Link
                            v-for="link in entries.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-4 py-2 rounded-md',
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-100 via-purple-50 to-rose-50 shadow-xl text-center py-16 border-2 border-pink-200"
                >
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-300/30 to-purple-300/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-6">
                        <span class="text-7xl mb-6 block transform hover:scale-110 transition-transform inline-block">✨</span>
                        <h3 class="text-3xl font-bold text-gray-800 mb-3">
                            ¡Comienza tu diario, hermosa!
                        </h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                            Crea tu primera entrada para comenzar a documentar tus días especiales.
                        </p>
                        <Link
                            :href="route('diary.create')"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-base shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-1 transition-all"
                        >
                            <span class="mr-2">💕</span>
                            Crear Primera Entrada
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

