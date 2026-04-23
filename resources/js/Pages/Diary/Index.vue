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
const searchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

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
            <div class="flex flex-wrap items-center justify-between gap-4">
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

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Search and Filters -->
                <section class="feminine-panel space-y-4 p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-rose-500">Explora tus recuerdos</p>
                        <span class="rounded-full border border-rose-200/80 bg-white/80 px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm">{{ entries.meta?.total ?? entries.data.length }} entradas</span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <label for="diary-search" class="sr-only">Buscar en el diario</label>
                            <input
                                id="diary-search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="🔍 Buscar en el diario..."
                                class="w-full rounded-2xl border border-rose-200/80 bg-white/85 px-4 py-3 pl-12 text-gray-900 shadow-sm shadow-rose-500/10 transition-all focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
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
                                'px-6 py-3 rounded-2xl font-semibold transition-all border focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2',
                                showFavorites
                                    ? 'bg-gradient-to-r from-rose-500 to-fuchsia-500 text-white border-rose-500 shadow-lg shadow-rose-400/30'
                                    : 'bg-white/85 text-gray-700 border-rose-200 hover:border-rose-400 hover:bg-rose-50'
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
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <select
                            v-model="selectedTag"
                            @change="applyFilters"
                            class="px-4 py-2.5 rounded-2xl border border-rose-200 bg-white/85 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
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
                            class="px-4 py-2.5 rounded-2xl border border-rose-200 bg-white/85 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                        />
                        
                        <input
                            v-model="dateTo"
                            @change="applyFilters"
                            type="date"
                            placeholder="Hasta"
                            class="px-4 py-2.5 rounded-2xl border border-rose-200 bg-white/85 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                        />
                        
                        <select
                            v-model="selectedMood"
                            @change="applyFilters"
                            class="px-4 py-2.5 rounded-2xl border border-rose-200 bg-white/85 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                        >
                            <option value="">Todos los estados</option>
                            <option value="😊">😊 Feliz</option>
                            <option value="😢">😢 Triste</option>
                            <option value="😡">😡 Enojado</option>
                            <option value="😌">😌 Tranquilo</option>
                        </select>
                    </div>
                    
                    <div v-if="searchQuery || showFavorites || selectedTag || dateFrom || dateTo || selectedMood" class="flex flex-wrap items-center gap-2 text-sm text-rose-700">
                        <span>Filtros activos:</span>
                        <span v-if="searchQuery" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1">
                            Búsqueda: "{{ searchQuery }}"
                        </span>
                        <span v-if="showFavorites" class="rounded-full border border-fuchsia-200 bg-fuchsia-50 px-3 py-1 text-fuchsia-700">
                            Solo favoritos
                        </span>
                        <span v-if="selectedTag" class="rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-violet-700">
                            Etiqueta: {{ props.tags?.find(t => t.id === selectedTag)?.name }}
                        </span>
                        <span v-if="dateFrom || dateTo" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sky-700">
                            {{ dateFrom || 'Inicio' }} - {{ dateTo || 'Hoy' }}
                        </span>
                        <span v-if="selectedMood" class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-emerald-700">
                            {{ selectedMood }}
                        </span>
                        <button
                            @click="clearSearch"
                            class="rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold text-rose-700 transition-colors hover:bg-rose-50"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </section>

                <!-- Entries List -->
                <div v-if="entries.data.length > 0" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" role="list" aria-label="Lista de entradas del diario">
                        <Link
                            v-for="entry in entries.data"
                            :key="entry.id"
                            :href="route('diary.show', entry.id)"
                            class="group feminine-surface relative overflow-hidden rounded-[1.75rem] border border-rose-100/80 p-0 shadow-[0_16px_45px_rgba(236,72,153,0.12)] transition-all duration-300 hover:-translate-y-1.5 hover:border-rose-300 hover:shadow-[0_22px_55px_rgba(236,72,153,0.18)] focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2"
                            role="listitem"
                            :aria-label="`Entrada del diario: ${entry.title}`"
                            tabindex="0"
                        >
                            <div class="absolute right-0 top-0 h-32 w-32 -mr-14 -mt-14 rounded-full bg-gradient-to-br from-rose-200/40 to-fuchsia-200/30 blur-sm transition-transform duration-500 group-hover:scale-150"></div>
                            <div class="relative p-5">
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="mb-2 flex items-center gap-2">
                                            <span class="text-3xl transform group-hover:scale-125 transition-transform">{{ entry.mood }}</span>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 line-clamp-1">
                                                {{ entry.title }}
                                            </h3>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-3 leading-relaxed">
                                            {{ entry.content }}
                                        </p>
                                        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white/80 px-2.5 py-1 text-xs font-medium text-rose-700">
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
                                                class="rounded-full px-2 py-0.5 text-xs font-semibold text-white shadow-sm"
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
                        class="mt-8 flex flex-wrap justify-center gap-2"
                    >
                        <Link
                            v-for="link in entries.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-xl border px-4 py-2 font-semibold transition-all',
                                link.active
                                    ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white border-pink-600 shadow-lg'
                                    : 'bg-white/90 text-gray-700 border-rose-200 hover:border-rose-400 hover:bg-rose-50',
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

