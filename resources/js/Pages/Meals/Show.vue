<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Meal {
    id: number;
    name: string;
    description?: string;
    recipe?: string;
    type: string;
    ingredients?: string[];
    prep_time?: number;
    cook_time?: number;
    servings?: number;
    rating?: number;
}

interface Props {
    meal: Meal;
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
</script>

<template>
    <Head :title="`${meal.name} - Comida Favorita`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>{{ getTypeIcon(meal.type) }}</span>
                    {{ meal.name }}
                </h2>
                <Link
                    :href="route('meals.edit', meal.id)"
                    class="px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-full text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <div class="mb-6">
                        <span class="text-6xl">{{ getTypeIcon(meal.type) }}</span>
                        <span class="ml-4 px-4 py-2 bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-200 rounded-full text-sm font-semibold">
                            {{ getTypeName(meal.type) }}
                        </span>
                    </div>

                    <div v-if="meal.description" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Descripción</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ meal.description }}</p>
                    </div>

                    <div v-if="meal.rating" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Calificación</h3>
                        <div class="flex gap-1">
                            <span v-for="i in 5" :key="i" class="text-2xl">
                                {{ i <= meal.rating! ? '⭐' : '☆' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div v-if="meal.prep_time" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">⏱️ Tiempo de Preparación</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ meal.prep_time }} min</div>
                        </div>
                        <div v-if="meal.cook_time" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">🍳 Tiempo de Cocción</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ meal.cook_time }} min</div>
                        </div>
                        <div v-if="meal.servings" class="bg-pink-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">👥 Porciones</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ meal.servings }}</div>
                        </div>
                    </div>

                    <div v-if="meal.ingredients && meal.ingredients.length > 0" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Ingredientes</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                            <li v-for="(ingredient, index) in meal.ingredients" :key="index">
                                {{ ingredient }}
                            </li>
                        </ul>
                    </div>

                    <div v-if="meal.recipe" class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Receta</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ meal.recipe }}
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 border-t border-pink-100 dark:border-gray-700">
                        <Link
                            :href="route('meals.index')"
                            class="px-6 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            ← Volver
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

