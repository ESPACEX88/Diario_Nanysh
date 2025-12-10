<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface HabitLog {
    id: number;
    completed_at: string;
    notes?: string;
}

interface Habit {
    id: number;
    name: string;
    description?: string;
    icon?: string;
    color?: string;
    frequency?: string;
    is_active: boolean;
    habit_logs: HabitLog[];
}

interface Props {
    habit: Habit;
    currentStreak: number;
    totalCompletions: number;
    thisMonthCompletions: number;
    bestStreak: number;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteHabit = () => {
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route('habits.destroy', props.habit.id));
    showDeleteModal.value = false;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
};

const toggleToday = () => {
    router.post(route('habits.log', props.habit.id), {
        date: new Date().toISOString().split('T')[0],
    }, {
        preserveScroll: true,
    });
};

const isTodayCompleted = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    return props.habit.habit_logs.some(log => log.completed_at === today);
});

const getDateStatus = (date: string) => {
    const today = new Date().toISOString().split('T')[0];
    const logDate = date;
    return props.habit.habit_logs.some(log => log.completed_at === logDate);
};

const getLast7Days = () => {
    const days = [];
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        days.push(date.toISOString().split('T')[0]);
    }
    return days;
};
</script>

<template>
    <Head :title="habit.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>{{ habit.icon || '🔄' }}</span>
                    {{ habit.name }}
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('habits.edit', habit.id)"
                        class="px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-full text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                    >
                        Editar
                    </Link>
                    <button
                        @click="deleteHabit"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-semibold hover:bg-red-200 transition-all"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-2xl border-2 border-pink-200 dark:border-gray-700 p-6 shadow-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Racha Actual</div>
                        <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">
                            {{ currentStreak }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">días seguidos</div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-2xl border-2 border-blue-200 dark:border-gray-700 p-6 shadow-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Mejor Racha</div>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            {{ bestStreak }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">días</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-2xl border-2 border-green-200 dark:border-gray-700 p-6 shadow-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Completado</div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ totalCompletions }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">veces</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 via-amber-50 to-orange-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-2xl border-2 border-yellow-200 dark:border-gray-700 p-6 shadow-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Este Mes</div>
                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ thisMonthCompletions }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">completados</div>
                    </div>
                </div>

                <!-- Descripción -->
                <div v-if="habit.description" class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Descripción</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ habit.description }}</p>
                </div>

                <!-- Marcar hoy -->
                <div class="mb-8 bg-gradient-to-r from-pink-100 to-rose-100 dark:from-gray-800 dark:to-gray-800 rounded-2xl border-2 border-pink-200 dark:border-gray-700 p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">¿Completaste este hábito hoy?</h3>
                    <button
                        @click="toggleToday"
                        :class="[
                            'px-8 py-4 rounded-full font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1',
                            isTodayCompleted
                                ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:from-green-600 hover:to-emerald-600'
                                : 'bg-gradient-to-r from-pink-500 to-rose-500 text-white hover:from-pink-600 hover:to-rose-600'
                        ]"
                    >
                        <span v-if="isTodayCompleted">✅ Ya completado hoy</span>
                        <span v-else>✨ Marcar como completado</span>
                    </button>
                </div>

                <!-- Últimos 7 días -->
                <div class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Últimos 7 días</h3>
                    <div class="flex gap-2">
                        <div
                            v-for="day in getLast7Days()"
                            :key="day"
                            :class="[
                                'w-12 h-12 rounded-lg flex items-center justify-center font-semibold text-sm',
                                getDateStatus(day)
                                    ? 'bg-gradient-to-r from-green-400 to-emerald-400 text-white'
                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                            ]"
                            :title="new Date(day).toLocaleDateString('es-ES')"
                        >
                            {{ new Date(day).getDate() }}
                        </div>
                    </div>
                </div>

                <!-- Historial -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Historial Reciente</h3>
                    <div v-if="habit.habit_logs.length > 0" class="space-y-2">
                        <div
                            v-for="log in habit.habit_logs.slice(0, 30)"
                            :key="log.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div>
                                <span class="text-green-500 mr-2">✅</span>
                                <span class="text-gray-900 dark:text-white font-semibold">
                                    {{ new Date(log.completed_at).toLocaleDateString('es-ES', {
                                        weekday: 'long',
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    }) }}
                                </span>
                                <p v-if="log.notes" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ log.notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p>No hay registros aún. ¡Marca tu primer día!</p>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Hábito"
            message="¿Estás segura de que quieres eliminar este hábito? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>
