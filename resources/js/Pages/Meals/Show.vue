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
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="flex items-center gap-2 bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-2xl font-bold text-transparent">
                    <span>{{ getTypeIcon(meal.type) }}</span>
                    {{ meal.name }}
                </h2>
                <Link
                    :href="route('meals.edit', meal.id)"
                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Comida favorita</p>
                        <p class="mt-1 text-sm text-rose-700">Conserva tus preparaciones favoritas para repetirlas cuando quieras.</p>
                    </div>
                    <span class="text-2xl">🍽️</span>
                </section>

                <div class="feminine-panel p-8">
                    <div class="mb-6">
                        <span class="text-6xl">{{ getTypeIcon(meal.type) }}</span>
                        <span class="ml-4 rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700">
                            {{ getTypeName(meal.type) }}
                        </span>
                    </div>

                    <div v-if="meal.description" class="mb-6">
                        <h3 class="mb-2 text-lg font-bold text-gray-900">Descripción</h3>
                        <p class="text-gray-700">{{ meal.description }}</p>
                    </div>

                    <div v-if="meal.rating" class="mb-6">
                        <h3 class="mb-2 text-lg font-bold text-gray-900">Calificación</h3>
                        <div class="flex gap-1">
                            <span v-for="i in 5" :key="i" class="text-2xl">
                                {{ i <= meal.rating! ? '⭐' : '☆' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div v-if="meal.prep_time" class="rounded-xl border border-rose-100 bg-white/80 p-4">
                            <div class="mb-1 text-sm text-gray-600">⏱️ Tiempo de Preparación</div>
                            <div class="text-xl font-bold text-gray-900">{{ meal.prep_time }} min</div>
                        </div>
                        <div v-if="meal.cook_time" class="rounded-xl border border-rose-100 bg-white/80 p-4">
                            <div class="mb-1 text-sm text-gray-600">🍳 Tiempo de Cocción</div>
                            <div class="text-xl font-bold text-gray-900">{{ meal.cook_time }} min</div>
                        </div>
                        <div v-if="meal.servings" class="rounded-xl border border-rose-100 bg-white/80 p-4">
                            <div class="mb-1 text-sm text-gray-600">👥 Porciones</div>
                            <div class="text-xl font-bold text-gray-900">{{ meal.servings }}</div>
                        </div>
                    </div>

                    <div v-if="meal.ingredients && meal.ingredients.length > 0" class="mb-6">
                        <h3 class="mb-3 text-lg font-bold text-gray-900">Ingredientes</h3>
                        <ul class="list-disc list-inside space-y-2 rounded-xl border border-rose-100 bg-white/80 p-4 text-gray-700">
                            <li v-for="(ingredient, index) in meal.ingredients" :key="index">
                                {{ ingredient }}
                            </li>
                        </ul>
                    </div>

                    <div v-if="meal.recipe" class="mb-6">
                        <h3 class="mb-3 text-lg font-bold text-gray-900">Receta</h3>
                        <div class="whitespace-pre-line rounded-xl border border-rose-100 bg-white/80 p-4 text-gray-700">
                            {{ meal.recipe }}
                        </div>
                    </div>

                    <div class="flex gap-4 border-t border-rose-100 pt-4">
                        <Link
                            :href="route('meals.index')"
                            class="rounded-xl px-6 py-2 font-semibold text-gray-600 transition-colors hover:bg-rose-50 hover:text-rose-700"
                        >
                            ← Volver
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

