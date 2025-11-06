<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Achievement {
    id: number;
    code: string;
    name: string;
    description: string;
    icon: string;
    color: string;
    points: number;
    type: string;
}

interface Props {
    achievements: Achievement[];
    unlockedAchievements: number[];
}

const props = defineProps<Props>();

const isUnlocked = (achievementId: number) => {
    return props.unlockedAchievements.includes(achievementId);
};

const unlockedCount = computed(() => props.unlockedAchievements.length);
const totalCount = computed(() => props.achievements.length);
</script>

<template>
    <Head title="Logros" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                🏆 Mis Logros
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Progress -->
                <div class="mb-8 bg-white rounded-2xl shadow-lg border border-pink-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Progreso</h3>
                        <span class="text-2xl font-bold text-pink-600">
                            {{ unlockedCount }} / {{ totalCount }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div
                            class="bg-gradient-to-r from-pink-500 to-rose-500 h-4 rounded-full transition-all duration-500"
                            :style="{ width: `${(unlockedCount / totalCount) * 100}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Achievements Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="achievement in achievements"
                        :key="achievement.id"
                        :class="[
                            'rounded-2xl p-6 border-2 transition-all',
                            isUnlocked(achievement.id)
                                ? 'bg-gradient-to-br from-pink-50 to-rose-50 border-pink-300 shadow-lg'
                                : 'bg-gray-50 border-gray-200 opacity-60'
                        ]"
                    >
                        <div class="text-center">
                            <div class="text-6xl mb-4" :class="{ 'grayscale': !isUnlocked(achievement.id) }">
                                {{ achievement.icon }}
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                {{ achievement.name }}
                            </h3>
                            <p class="text-gray-600 mb-4 text-sm">
                                {{ achievement.description }}
                            </p>
                            <div class="flex items-center justify-center gap-2">
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                                    🪙 {{ achievement.points }} puntos
                                </span>
                                <span
                                    v-if="isUnlocked(achievement.id)"
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold"
                                >
                                    ✅ Desbloqueado
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

