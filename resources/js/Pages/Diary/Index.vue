<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import EmptyState from '@/Components/EmptyState.vue';

interface Tag {
    id: number;
    name: string;
    color: string;
}

interface Props {
    entries: {
        data: any[];
        links: any;
        meta: any;
    };
    tags?: Tag[];
    filters?: {
        search?: string;
        favorite?: boolean;
        tag?: number;
        date_from?: string;
        date_to?: string;
        mood?: string;
    };
}

const props = defineProps<Props>();
const searchQuery = ref(props.filters?.search || '');
const showFavorites = ref(props.filters?.favorite || false);
const selectedTag = ref<number | null>(props.filters?.tag || null);
const dateFrom = ref(props.filters?.date_from || '');
const dateTo = ref(props.filters?.date_to || '');
const selectedMood = ref(props.filters?.mood || '');
const searchTimeout = ref<NodeJS.Timeout | null>(null);

// Debounce search
watch(searchQuery, (newValue) => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    
    searchTimeout.value = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get(
        route('diary.index'),
        {
            search: searchQuery.value || undefined,
            favorite: showFavorites.value || undefined,
            tag: selectedTag.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
            mood: selectedMood.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const toggleFavorites = () => {
    showFavorites.value = !showFavorites.value;
    applyFilters();
};

const clearSearch = () => {
    searchQuery.value = '';
    showFavorites.value = false;
    selectedTag.value = null;
    dateFrom.value = '';
    dateTo.value = '';
    selectedMood.value = '';
    router.get(route('diary.index'), {}, {
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
                <!-- Search and Filters -->
                <div class="mb-6 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <label for="diary-search" class="sr-only">Buscar en el diario</label>
                            <input
                                id="diary-search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="🔍 Buscar en el diario..."
                                class="w-full rounded-xl border-2 border-pink-200 bg-white px-4 py-3 pl-12 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all"
                                aria-label="Buscar en el diario"
                                aria-describedby="search-description"
                            />
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl" aria-hidden="true">🔍</span>
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                aria-label="Limpiar búsqueda"
                                @keydown.enter="clearSearch"
                                @keydown.space.prevent="clearSearch"
                            >
                                ✕
                            </button>
                            <span id="search-description" class="sr-only">Busca por título o contenido en tus entradas del diario</span>
                        </div>
                        <button
                            @click="toggleFavorites"
                            :class="[
                                'px-6 py-3 rounded-xl font-semibold transition-all border-2 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2',
                                showFavorites
                                    ? 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white border-yellow-500 shadow-lg'
                                    : 'bg-white text-gray-700 border-pink-200 hover:border-pink-400 hover:bg-pink-50'
                            ]"
                            :aria-pressed="showFavorites"
                            aria-label="Filtrar por favoritos"
                            @keydown.enter="toggleFavorites"
                            @keydown.space.prevent="toggleFavorites"
                        >
                            <span class="mr-2" aria-hidden="true">{{ showFavorites ? '💖' : '🤍' }}</span>
                            {{ showFavorites ? 'Favoritos' : 'Todos' }}
                        </button>
                    </div>
                    
                    <!-- Advanced Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <select
                            v-model="selectedTag"
                            @change="applyFilters"
                            class="px-4 py-2 rounded-xl border-2 border-pink-200 bg-white text-gray-900 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                        >
                            <option :value="null">Todas las etiquetas</option>
                            <option
                                v-for="tag in props.tags"
                                :key="tag.id"
                                :value="tag.id"
                            >
                                {{ tag.name }}
                            </option>
                        </select>
                        
                        <input
                            v-model="dateFrom"
                            @change="applyFilters"
                            type="date"
                            placeholder="Desde"
                            class="px-4 py-2 rounded-xl border-2 border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-pink-500 dark:focus:border-pink-400 focus:ring-2 focus:ring-pink-500"
                        />
                        
                        <input
                            v-model="dateTo"
                            @change="applyFilters"
                            type="date"
                            placeholder="Hasta"
                            class="px-4 py-2 rounded-xl border-2 border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-pink-500 dark:focus:border-pink-400 focus:ring-2 focus:ring-pink-500"
                        />
                        
                        <select
                            v-model="selectedMood"
                            @change="applyFilters"
                            class="px-4 py-2 rounded-xl border-2 border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-pink-500 dark:focus:border-pink-400 focus:ring-2 focus:ring-pink-500"
                        >
                            <option value="">Todos los estados</option>
                            <option value="😊">😊 Feliz</option>
                            <option value="😢">😢 Triste</option>
                            <option value="😡">😡 Enojado</option>
                            <option value="😌">😌 Tranquilo</option>
                        </select>
                    </div>
                    
                    <div v-if="searchQuery || showFavorites || selectedTag || dateFrom || dateTo || selectedMood" class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2 flex-wrap">
                        <span>Filtros activos:</span>
                        <span v-if="searchQuery" class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full">
                            Búsqueda: "{{ searchQuery }}"
                        </span>
                        <span v-if="showFavorites" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full">
                            Solo favoritos
                        </span>
                        <span v-if="selectedTag" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full">
                            Etiqueta: {{ props.tags?.find(t => t.id === selectedTag)?.name }}
                        </span>
                        <span v-if="dateFrom || dateTo" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                            {{ dateFrom || 'Inicio' }} - {{ dateTo || 'Hoy' }}
                        </span>
                        <span v-if="selectedMood" class="px-3 py-1 bg-green-100 text-green-700 rounded-full">
                            {{ selectedMood }}
                        </span>
                        <button
                            @click="clearSearch"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors text-xs font-semibold"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Entries List -->
                <div v-if="entries.data.length > 0" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" role="list" aria-label="Lista de entradas del diario">
                        <Link
                            v-for="entry in entries.data"
                            :key="entry.id"
                            :href="route('diary.show', entry.id)"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 to-rose-50 dark:from-gray-800 dark:to-gray-700 shadow-lg hover:shadow-2xl border-2 border-pink-100 dark:border-gray-700 hover:border-pink-400 dark:hover:border-pink-500 transition-all duration-300 transform hover:-translate-y-2 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                            role="listitem"
                            :aria-label="`Entrada del diario: ${entry.title}`"
                            tabindex="0"
                        >
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative p-5">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-3xl transform group-hover:scale-125 transition-transform">{{ entry.mood }}</span>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 line-clamp-1">
                                                {{ entry.title }}
                                            </h3>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-3 leading-relaxed">
                                            {{ entry.content }}
                                        </p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                                            <span>📅</span>
                                            <span>
                                                {{ new Date(entry.date).toLocaleDateString('es-ES', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric'
                                                }) }}
                                            </span>
                                        </div>
                                        <div v-if="entry.tags && entry.tags.length > 0" class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="tag in entry.tags"
                                                :key="tag.id"
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                                :style="{ backgroundColor: tag.color }"
                                            >
                                                {{ tag.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <span
                                        v-if="entry.is_favorite"
                                        class="text-2xl transform group-hover:scale-125 transition-transform"
                                        title="Favorito"
                                    >
                                        💖
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="entries.links.length > 3"
                        class="flex flex-wrap justify-center gap-2 mt-8"
                    >
                        <Link
                            v-for="link in entries.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-4 py-2 rounded-xl font-semibold transition-all border-2',
                                link.active
                                    ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white border-pink-600 shadow-lg'
                                    : 'bg-white text-gray-700 border-pink-200 hover:border-pink-400 hover:bg-pink-50',
                                !link.url ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:scale-105'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>

                <!-- Empty State -->
                <EmptyState
                    v-else
                    icon="✨"
                    title="¡Comienza tu diario, hermosa!"
                    :description="searchQuery || showFavorites ? 'No se encontraron entradas con los filtros aplicados.' : 'Crea tu primera entrada para comenzar a documentar tus días especiales.'"
                    action-label="Crear Primera Entrada"
                    :action-route="'diary.create'"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

