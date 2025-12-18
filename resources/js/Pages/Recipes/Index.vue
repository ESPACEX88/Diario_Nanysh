<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Recipe {
    id: number;
    name: string;
    description?: string;
    type: string;
    category?: string;
    difficulty: string;
    prep_time?: number;
    cook_time?: number;
    servings?: number;
    calories?: number;
}

interface Props {
    recipes: {
        data: Recipe[];
        links: any;
        meta: any;
    };
    categories: string[];
    types: string[];
}

const props = defineProps<Props>();

// Initialize filters from query parameters if they exist
const searchQuery = ref(props.recipes?.meta?.search || '');
const selectedType = ref(props.recipes?.meta?.type || '');
const selectedCategory = ref(props.recipes?.meta?.category || '');
const selectedDifficulty = ref(props.recipes?.meta?.difficulty || '');

const getTypeIcon = (type: string) => {
    const icons: Record<string, string> = {
        breakfast: '🌅',
        lunch: '🍽️',
        dinner: '🌙',
        snack: '🍪',
        dessert: '🍰',
        drink: '🥤',
    };
    return icons[type] || '🍽️';
};

const getTypeName = (type: string) => {
    const names: Record<string, string> = {
        breakfast: 'Desayuno',
        lunch: 'Almuerzo',
        dinner: 'Cena',
        snack: 'Snack',
        dessert: 'Postre',
        drink: 'Bebida',
    };
    return names[type] || type;
};

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        easy: 'bg-green-100 text-green-700',
        medium: 'bg-yellow-100 text-yellow-700',
        hard: 'bg-red-100 text-red-700',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-700';
};

const getDifficultyName = (difficulty: string) => {
    const names: Record<string, string> = {
        easy: 'Fácil',
        medium: 'Medio',
        hard: 'Difícil',
    };
    return names[difficulty] || difficulty;
};

const applyFilters = () => {
    router.get(route('recipes.index'), {
        search: searchQuery.value || undefined,
        type: selectedType.value || undefined,
        category: selectedCategory.value || undefined,
        difficulty: selectedDifficulty.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedType.value = '';
    selectedCategory.value = '';
    selectedDifficulty.value = '';
    applyFilters();
};
</script>

<template>
    <Head title="Recetario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📖</span>
                    Recetario
                </h2>
                <Link
                    :href="route('meals.index')"
                    class="px-4 py-2 text-pink-600 hover:text-pink-800 font-semibold"
                >
                    Mis Comidas Favoritas →
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                            <input
                                type="text"
                                v-model="searchQuery"
                                @input="applyFilters"
                                placeholder="Buscar receta..."
                                class="w-full rounded-lg border-2 border-pink-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                            <select
                                v-model="selectedType"
                                @change="applyFilters"
                                class="w-full rounded-lg border-2 border-pink-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                            >
                                <option value="">Todos</option>
                                <option v-for="type in (types || [])" :key="type" :value="type">
                                    {{ getTypeName(type) }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Categoría</label>
                            <select
                                v-model="selectedCategory"
                                @change="applyFilters"
                                class="w-full rounded-lg border-2 border-pink-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                            >
                                <option value="">Todas</option>
                                <option v-for="category in (categories || [])" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Dificultad</label>
                            <select
                                v-model="selectedDifficulty"
                                @change="applyFilters"
                                class="w-full rounded-lg border-2 border-pink-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                            >
                                <option value="">Todas</option>
                                <option value="easy">Fácil</option>
                                <option value="medium">Medio</option>
                                <option value="hard">Difícil</option>
                            </select>
                        </div>
                    </div>
                    <button
                        @click="clearFilters"
                        class="mt-4 px-4 py-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-semibold"
                    >
                        Limpiar filtros
                    </button>
                </div>

                <!-- Debug: Mostrar información de recetas -->
                <div v-if="!recipes" class="mb-4 p-4 bg-yellow-100 rounded-lg">
                    <p class="text-yellow-800">⚠️ No se recibieron datos de recetas del servidor</p>
                </div>
                <div v-else-if="!recipes.data" class="mb-4 p-4 bg-yellow-100 rounded-lg">
                    <p class="text-yellow-800">⚠️ No hay propiedad 'data' en recipes. Total: {{ recipes.total || 'N/A' }}</p>
                </div>

                <!-- Recetas -->
                <div v-if="recipes && recipes.data && recipes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="recipe in recipes.data"
                        :key="recipe.id"
                        :href="route('recipes.show', recipe.id)"
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 border-2 border-pink-200 dark:border-gray-700 hover:border-pink-400 dark:hover:border-gray-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-2"
                    >
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-16 -mt-16"></div>
                        <div class="relative p-6">
                            <div class="text-5xl mb-4">{{ getTypeIcon(recipe.type) }}</div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ recipe.name }}</h3>
                            <p v-if="recipe.description" class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ recipe.description }}
                            </p>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', getDifficultyColor(recipe.difficulty)]">
                                    {{ getDifficultyName(recipe.difficulty) }}
                                </span>
                                <span v-if="recipe.category" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                    {{ recipe.category }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span v-if="recipe.prep_time">⏱️ {{ recipe.prep_time }}min</span>
                                <span v-if="recipe.cook_time">🍳 {{ recipe.cook_time }}min</span>
                                <span v-if="recipe.servings">👥 {{ recipe.servings }}</span>
                                <span v-if="recipe.calories">🔥 {{ recipe.calories }} cal</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div
                    v-else-if="recipes?.data?.length === 0"
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-3xl border-4 border-pink-300 dark:border-gray-700"
                >
                    <span class="text-8xl block mb-6">📖</span>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-3">No se encontraron recetas</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Intenta ajustar los filtros de búsqueda
                    </p>
                    <button
                        @click="clearFilters"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        Limpiar Filtros
                    </button>
                </div>
                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-3xl border-4 border-pink-300 dark:border-gray-700"
                >
                    <span class="text-8xl block mb-6">⏳</span>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-3">Cargando recetas...</h3>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

