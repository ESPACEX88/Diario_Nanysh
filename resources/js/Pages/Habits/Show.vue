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
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="flex items-center gap-2 bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-2xl font-bold text-transparent">
                    <span>{{ habit.icon || '🔄' }}</span>
                    {{ habit.name }}
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('habits.edit', habit.id)"
                        class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                    >
                        Editar
                    </Link>
                    <button
                        @click="deleteHabit"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition-all hover:-translate-y-0.5 hover:bg-red-100"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-6xl space-y-8 sm:px-6 lg:px-8">
                <section class="feminine-panel flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Seguimiento diario</p>
                        <p class="mt-1 text-sm text-rose-700">Cada check cuenta. Mantener constancia te acerca a tu mejor version.</p>
                    </div>
                    <span
                        :class="[
                            'rounded-full px-3 py-1 text-xs font-semibold shadow-sm',
                            habit.is_active
                                ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                                : 'border border-gray-300 bg-gray-100 text-gray-600'
                        ]"
                    >
                        {{ habit.is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </section>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                    <div class="feminine-surface rounded-2xl border border-rose-200/80 p-6 shadow-[0_12px_28px_rgba(236,72,153,0.12)]">
                        <div class="mb-1 text-sm text-gray-600">Racha Actual</div>
                        <div class="text-3xl font-bold text-rose-600">
                            {{ currentStreak }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">dias seguidos</div>
                    </div>
                    <div class="feminine-surface rounded-2xl border border-sky-200/80 p-6 shadow-[0_12px_28px_rgba(56,189,248,0.12)]">
                        <div class="mb-1 text-sm text-gray-600">Mejor Racha</div>
                        <div class="text-3xl font-bold text-sky-600">
                            {{ bestStreak }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">dias</div>
                    </div>
                    <div class="feminine-surface rounded-2xl border border-emerald-200/80 p-6 shadow-[0_12px_28px_rgba(16,185,129,0.12)]">
                        <div class="mb-1 text-sm text-gray-600">Total Completado</div>
                        <div class="text-3xl font-bold text-emerald-600">
                            {{ totalCompletions }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">veces</div>
                    </div>
                    <div class="feminine-surface rounded-2xl border border-amber-200/80 p-6 shadow-[0_12px_28px_rgba(245,158,11,0.12)]">
                        <div class="mb-1 text-sm text-gray-600">Este Mes</div>
                        <div class="text-3xl font-bold text-amber-600">
                            {{ thisMonthCompletions }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">completados</div>
                    </div>
                </div>

                <!-- Descripción -->
                <div v-if="habit.description" class="feminine-panel p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Descripción</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ habit.description }}</p>
                </div>

                <!-- Marcar hoy -->
                <div class="feminine-panel p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">¿Completaste este hábito hoy?</h3>
                    <button
                        @click="toggleToday"
                        :class="[
                            'rounded-2xl px-8 py-4 text-lg font-bold transition-all shadow-lg hover:-translate-y-1',
                            isTodayCompleted
                                ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white hover:from-emerald-600 hover:to-teal-600'
                                : 'bg-gradient-to-r from-rose-500 to-fuchsia-500 text-white hover:from-rose-600 hover:to-fuchsia-600'
                        ]"
                    >
                        <span v-if="isTodayCompleted">✅ Ya completado hoy</span>
                        <span v-else>✨ Marcar como completado</span>
                    </button>
                </div>

                <!-- Últimos 7 días -->
                <div class="feminine-panel p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Últimos 7 días</h3>
                    <div class="flex gap-2">
                        <div
                            v-for="day in getLast7Days()"
                            :key="day"
                            :class="[
                                'flex h-12 w-12 items-center justify-center rounded-xl text-sm font-semibold',
                                getDateStatus(day)
                                    ? 'bg-gradient-to-r from-emerald-400 to-teal-400 text-white'
                                    : 'bg-rose-50 text-rose-400'
                            ]"
                            :title="new Date(day).toLocaleDateString('es-ES')"
                        >
                            {{ new Date(day).getDate() }}
                        </div>
                    </div>
                </div>

                <!-- Historial -->
                <div class="feminine-panel p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Historial Reciente</h3>
                    <div v-if="habit.habit_logs.length > 0" class="space-y-2">
                        <div
                            v-for="log in habit.habit_logs.slice(0, 30)"
                            :key="log.id"
                            class="flex items-center justify-between rounded-xl border border-rose-100/80 bg-white/75 p-3"
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
                    <div v-else class="py-8 text-center text-gray-500">
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
