<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Meal {
    id: number;
    name: string;
    description?: string;
    type: string;
    rating?: number;
    prep_time?: number;
    cook_time?: number;
    servings?: number;
}

interface Props {
    meals: Meal[];
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

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        breakfast: 'from-yellow-400 to-orange-400',
        lunch: 'from-green-400 to-emerald-400',
        dinner: 'from-purple-400 to-indigo-400',
        snack: 'from-pink-400 to-rose-400',
        dessert: 'from-pink-500 to-rose-500',
        drink: 'from-blue-400 to-cyan-400',
    };
    return colors[type] || 'from-pink-400 to-rose-400';
};

const mealsByType = computed(() => {
    const grouped: Record<string, Meal[]> = {};
    props.meals.forEach(meal => {
        if (!grouped[meal.type]) {
            grouped[meal.type] = [];
        }
        grouped[meal.type].push(meal);
    });
    return grouped;
});

const deleteMeal = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar esta comida favorita?')) {
        router.delete(route('meals.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Comidas Favoritas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>🍽️</span>
                    Mis Comidas Favoritas
                </h2>
                <Link
                    :href="route('meals.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Agregar Comida
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div v-if="meals.length > 0">
                    <div
                        v-for="(meals, type) in mealsByType"
                        :key="type"
                        class="mb-10"
                    >
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                            <span class="text-3xl">{{ getTypeIcon(type) }}</span>
                            {{ type === 'breakfast' ? 'Desayunos' : type === 'lunch' ? 'Almuerzos' : type === 'dinner' ? 'Cenas' : type === 'snack' ? 'Snacks' : type === 'dessert' ? 'Postres' : 'Bebidas' }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div
                                v-for="meal in meals"
                                :key="meal.id"
                                class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-2 border-pink-200 hover:border-pink-400 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-2"
                            >
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-16 -mt-16"></div>
                                <div class="relative p-6">
                                    <div class="text-5xl mb-4">{{ getTypeIcon(meal.type) }}</div>
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ meal.name }}</h4>
                                    <p v-if="meal.description" class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ meal.description }}
                                    </p>
                                    <div v-if="meal.rating" class="mb-4">
                                        <div class="flex gap-1">
                                            <span v-for="i in 5" :key="i" class="text-lg">
                                                {{ i <= meal.rating! ? '⭐' : '☆' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="meal.prep_time || meal.cook_time" class="text-xs text-gray-500 mb-4">
                                        <span v-if="meal.prep_time">⏱️ Prep: {{ meal.prep_time }}min</span>
                                        <span v-if="meal.cook_time" class="ml-2">🍳 Cocción: {{ meal.cook_time }}min</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('meals.show', meal.id)"
                                            class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-lg text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all text-center"
                                        >
                                            Ver
                                        </Link>
                                        <button
                                            @click="deleteMeal(meal.id)"
                                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 rounded-3xl border-4 border-pink-300"
                >
                    <span class="text-8xl block mb-6">🍽️</span>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">No hay comidas favoritas aún</h3>
                    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                        Guarda tus recetas y comidas favoritas aquí
                    </p>
                    <Link
                        :href="route('meals.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Agregar Mi Primera Comida
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

