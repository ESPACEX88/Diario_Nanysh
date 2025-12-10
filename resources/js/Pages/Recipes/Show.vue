<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Recipe {
    id: number;
    name: string;
    description?: string;
    recipe: string;
    type: string;
    ingredients?: string[];
    prep_time?: number;
    cook_time?: number;
    servings?: number;
    difficulty: string;
    category?: string;
    calories?: number;
}

interface Props {
    recipe: Recipe;
}

const props = defineProps<Props>();

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
        easy: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
        medium: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
        hard: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200',
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
</script>

<template>
    <Head :title="`${recipe.name} - Receta`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>{{ getTypeIcon(recipe.type) }}</span>
                {{ recipe.name }}
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <div class="mb-6">
                        <span class="text-6xl">{{ getTypeIcon(recipe.type) }}</span>
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="px-4 py-2 bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-200 rounded-full text-sm font-semibold">
                                {{ getTypeName(recipe.type) }}
                            </span>
                            <span :class="['px-4 py-2 rounded-full text-sm font-semibold', getDifficultyColor(recipe.difficulty)]">
                                {{ getDifficultyName(recipe.difficulty) }}
                            </span>
                            <span v-if="recipe.category" class="px-4 py-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded-full text-sm font-semibold">
                                {{ recipe.category }}
                            </span>
                        </div>
                    </div>

                    <div v-if="recipe.description" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Descripción</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ recipe.description }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div v-if="recipe.prep_time" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">⏱️ Preparación</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ recipe.prep_time }} min</div>
                        </div>
                        <div v-if="recipe.cook_time" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">🍳 Cocción</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ recipe.cook_time }} min</div>
                        </div>
                        <div v-if="recipe.servings" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">👥 Porciones</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ recipe.servings }}</div>
                        </div>
                        <div v-if="recipe.calories" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">🔥 Calorías</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ recipe.calories }}</div>
                        </div>
                    </div>

                    <div v-if="recipe.ingredients && recipe.ingredients.length > 0" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Ingredientes</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <li v-for="(ingredient, index) in recipe.ingredients" :key="index">
                                {{ ingredient }}
                            </li>
                        </ul>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Instrucciones</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ recipe.recipe }}
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 border-t border-pink-100 dark:border-gray-700">
                        <Link
                            :href="route('recipes.index')"
                            class="px-6 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            ← Volver al Recetario
                        </Link>
                        <Link
                            :href="route('meals.index')"
                            class="px-6 py-2 text-pink-600 hover:text-pink-800 dark:text-pink-400 dark:hover:text-pink-300 font-semibold rounded-lg hover:bg-pink-50 dark:hover:bg-pink-900 transition-colors"
                        >
                            Ver Mis Comidas Favoritas →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

