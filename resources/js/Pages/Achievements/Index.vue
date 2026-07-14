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
    requirement_value?: number | null;
}

interface AchievementProgress {
    current: number;
    target: number;
    percent: number;
}

interface Props {
    achievements: Achievement[];
    unlockedAchievements: number[];
    progress?: Record<string, AchievementProgress>;
}

const props = withDefaults(defineProps<Props>(), {
    progress: () => ({}),
});

const isUnlocked = (achievementId: number) => {
    return props.unlockedAchievements.includes(achievementId);
};

const getProgress = (code: string): AchievementProgress => {
    return props.progress[code] ?? { current: 0, target: 1, percent: 0 };
};

const unlockedCount = computed(() => props.unlockedAchievements.length);
const totalCount = computed(() => props.achievements.length);
</script>

<template>
    <Head title="Logros" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-rose-950">
                🏆 Mis Logros
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
                <!-- Progress -->
                <div class="rounded-[2rem] border border-rose-100/80 bg-white/85 p-6 shadow-[0_18px_50px_rgba(236,72,153,0.08)] backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-rose-950">Progreso</h3>
                        <span class="text-2xl font-bold text-rose-600">
                            {{ unlockedCount }} / {{ totalCount }}
                        </span>
                    </div>
                    <div class="h-4 w-full rounded-full bg-rose-100">
                        <div
                            class="h-4 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 transition-all duration-500"
                            :style="{ width: `${totalCount ? (unlockedCount / totalCount) * 100 : 0}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Achievements Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="achievement in achievements"
                        :key="achievement.id"
                        :class="[
                            'rounded-[2rem] border p-6 transition-all',
                            isUnlocked(achievement.id)
                                ? 'border-rose-200 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 shadow-[0_18px_50px_rgba(236,72,153,0.08)]'
                                : 'border-rose-100 bg-white/80'
                        ]"
                    >
                        <div class="text-center">
                            <div class="mb-4 text-6xl" :class="{ 'grayscale opacity-70': !isUnlocked(achievement.id) }">
                                {{ achievement.icon }}
                            </div>
                            <h3 class="mb-2 text-xl font-bold text-rose-950">
                                {{ achievement.name }}
                            </h3>
                            <p class="mb-4 text-sm text-rose-900/70">
                                {{ achievement.description }}
                            </p>
                            <div class="mb-4 flex items-center justify-center gap-2">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    🪙 {{ achievement.points }} puntos
                                </span>
                                <span
                                    v-if="isUnlocked(achievement.id)"
                                    class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"
                                >
                                    ✅ Desbloqueado
                                </span>
                            </div>

                            <!-- Progress per achievement -->
                            <div class="mt-2 text-left">
                                <div class="mb-1 flex items-center justify-between text-xs font-semibold text-rose-900/70">
                                    <span>Progreso</span>
                                    <span>
                                        {{ getProgress(achievement.code).current }} / {{ getProgress(achievement.code).target }}
                                    </span>
                                </div>
                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-rose-100">
                                    <div
                                        class="h-2.5 rounded-full transition-all duration-500"
                                        :class="isUnlocked(achievement.id)
                                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-600'
                                            : 'bg-gradient-to-r from-rose-400 via-fuchsia-500 to-purple-500'"
                                        :style="{ width: `${getProgress(achievement.code).percent}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
