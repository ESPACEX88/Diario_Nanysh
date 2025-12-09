<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    statistics: {
        totals: {
            diary_entries: number;
            notes: number;
            photos: number;
            goals: number;
            habits: number;
            gratitudes: number;
        };
        mood_distribution: Record<string, number>;
        mood_trends: Record<string, string[]>;
        word_cloud: Record<string, number>;
        monthly_report: {
            diary_entries: number;
            notes: number;
            photos: number;
            goals_completed: number;
            habit_completions: number;
            gratitudes: number;
        };
    };
}

const props = defineProps<Props>();

const topWords = computed(() => {
    const words = Object.entries(props.statistics.word_cloud)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 20);
    return words;
});

const moodEntries = computed(() => {
    return Object.entries(props.statistics.mood_distribution)
        .sort((a, b) => b[1] - a[1]);
});

const maxMoodCount = computed(() => {
    return Math.max(...Object.values(props.statistics.mood_distribution), 1);
});
</script>

<template>
    <Head title="Estadísticas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-3xl">📊</span>
                <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    Mis Estadísticas
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Total Counts -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-6 border-2 border-pink-200 shadow-lg">
                        <div class="text-3xl mb-2">📖</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.diary_entries }}</div>
                        <div class="text-sm text-gray-600">Entradas</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg">
                        <div class="text-3xl mb-2">📝</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.notes }}</div>
                        <div class="text-sm text-gray-600">Notas</div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border-2 border-blue-200 shadow-lg">
                        <div class="text-3xl mb-2">📷</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.photos }}</div>
                        <div class="text-sm text-gray-600">Fotos</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200 shadow-lg">
                        <div class="text-3xl mb-2">🎯</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.goals }}</div>
                        <div class="text-sm text-gray-600">Metas</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border-2 border-yellow-200 shadow-lg">
                        <div class="text-3xl mb-2">🔄</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.habits }}</div>
                        <div class="text-sm text-gray-600">Hábitos</div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200 shadow-lg">
                        <div class="text-3xl mb-2">💖</div>
                        <div class="text-2xl font-bold text-gray-900">{{ statistics.totals.gratitudes }}</div>
                        <div class="text-sm text-gray-600">Gratitudes</div>
                    </div>
                </div>

                <!-- Mood Distribution -->
                <div class="bg-white rounded-2xl shadow-xl border-2 border-pink-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span>😊</span>
                        Distribución de Estados de Ánimo
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="[mood, count] in moodEntries"
                            :key="mood"
                            class="flex items-center gap-4"
                        >
                            <div class="text-3xl w-12 text-center">{{ mood }}</div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-semibold text-gray-700">{{ count }} veces</span>
                                    <span class="text-xs text-gray-500">{{ Math.round((count / maxMoodCount) * 100) }}%</span>
                                </div>
                                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-gradient-to-r from-pink-500 to-rose-500 rounded-full transition-all duration-500"
                                        :style="{ width: `${(count / maxMoodCount) * 100}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Word Cloud -->
                <div class="bg-white rounded-2xl shadow-xl border-2 border-pink-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span>☁️</span>
                        Palabras Más Usadas
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <span
                            v-for="[word, count] in topWords"
                            :key="word"
                            class="px-4 py-2 rounded-full bg-gradient-to-r from-pink-100 to-rose-100 border-2 border-pink-200 text-gray-800 font-semibold hover:scale-110 transition-transform"
                            :style="{ fontSize: `${12 + (count / 5)}px` }"
                        >
                            {{ word }} ({{ count }})
                        </span>
                    </div>
                </div>

                <!-- Monthly Report -->
                <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl shadow-xl border-2 border-pink-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span>📅</span>
                        Reporte de Este Mes
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-pink-600">{{ statistics.monthly_report.diary_entries }}</div>
                            <div class="text-sm text-gray-600">Entradas</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-purple-600">{{ statistics.monthly_report.notes }}</div>
                            <div class="text-sm text-gray-600">Notas</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-blue-600">{{ statistics.monthly_report.photos }}</div>
                            <div class="text-sm text-gray-600">Fotos</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-green-600">{{ statistics.monthly_report.goals_completed }}</div>
                            <div class="text-sm text-gray-600">Metas Completadas</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-yellow-600">{{ statistics.monthly_report.habit_completions }}</div>
                            <div class="text-sm text-gray-600">Hábitos Completados</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-pink-100">
                            <div class="text-2xl font-bold text-orange-600">{{ statistics.monthly_report.gratitudes }}</div>
                            <div class="text-sm text-gray-600">Gratitudes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

