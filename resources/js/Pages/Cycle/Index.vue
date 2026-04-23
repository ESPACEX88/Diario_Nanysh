<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

interface CycleTracking {
    id: number;
    date: string;
    phase?: string;
    cycle_day?: number;
    symptoms?: string[];
    mood?: string;
    flow_level?: number;
    notes?: string;
}

interface Stats {
    average_cycle_length?: number;
    next_period_predicted?: string | null;
    current_phase?: string | null;
    cycle_day?: number | null;
    advice?: {
        title: string;
        tips: string[];
        wellness: string[];
    };
    symptoms_summary?: Record<string, number>;
    mood_trend?: Array<{
        date: string;
        mood: string;
        phase?: string;
    }>;
    total_cycles?: number;
}

interface Props {
    trackings: CycleTracking[];
    stats?: Stats;
}

const props = defineProps<Props>();

const getPhaseColor = (phase?: string) => {
    const colors: Record<string, string> = {
        menstrual: 'from-red-400 to-pink-500',
        follicular: 'from-green-400 to-emerald-500',
        ovulation: 'from-yellow-400 to-orange-400',
        luteal: 'from-purple-400 to-indigo-500',
    };
    return colors[phase || ''] || 'from-pink-400 to-rose-400';
};

const getPhaseName = (phase?: string) => {
    const names: Record<string, string> = {
        menstrual: 'Menstrual',
        follicular: 'Folicular',
        ovulation: 'Ovulación',
        luteal: 'Lútea',
    };
    return names[phase || ''] || 'No especificado';
};

const showDeleteModal = ref(false);
const trackingToDelete = ref<number | null>(null);

const deleteTracking = (id: number) => {
    trackingToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (trackingToDelete.value) {
        router.delete(route('cycle.destroy', trackingToDelete.value));
    }
    showDeleteModal.value = false;
    trackingToDelete.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    trackingToDelete.value = null;
};

const getSymptomName = (symptom: string) => {
    const names: Record<string, string> = {
        cramps: 'Cólicos',
        bloating: 'Hinchazón',
        mood_swings: 'Cambios de humor',
        headache: 'Dolor de cabeza',
        fatigue: 'Fatiga',
        acne: 'Acné',
    };
    return names[symptom] || symptom;
};

const daysUntilPeriod = computed(() => {
    if (!props.stats?.next_period_predicted) return null;
    const today = new Date();
    const nextPeriod = new Date(props.stats.next_period_predicted);
    const diff = Math.ceil((nextPeriod.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    return diff;
});
</script>

<template>
    <Head title="Seguimiento de Ciclo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>🌸</span>
                    Mi Seguimiento
                </h2>
                <Link
                    :href="route('cycle.create')"
                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    + Nuevo Registro
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Control de ciclo</p>
                        <p class="mt-1 text-sm text-rose-700">Visualiza fase actual, predicción y registra cómo te sientes cada día.</p>
                    </div>
                    <span class="rounded-full border border-rose-200/80 bg-white/80 px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm">
                        {{ trackings.length }} registros
                    </span>
                </section>

                <!-- Estadísticas y Consejos -->
                <div v-if="stats" class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Estado Actual -->
                    <div class="feminine-surface lg:col-span-2 rounded-2xl border border-rose-100/80 p-6 shadow-[0_16px_42px_rgba(236,72,153,0.12)]">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span>📊</span>
                            Tu Ciclo Actual
                        </h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="rounded-xl border border-rose-100 bg-white/80 p-4">
                                <div class="mb-1 text-sm text-gray-600">Fase Actual</div>
                                <div class="text-2xl font-bold text-rose-600">
                                    {{ stats.current_phase ? getPhaseName(stats.current_phase) : 'N/A' }}
                                </div>
                            </div>
                            <div class="rounded-xl border border-rose-100 bg-white/80 p-4">
                                <div class="mb-1 text-sm text-gray-600">Día del Ciclo</div>
                                <div class="text-2xl font-bold text-rose-600">
                                    {{ stats.cycle_day || 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div v-if="stats.next_period_predicted && daysUntilPeriod !== null" class="rounded-xl border border-rose-100 bg-white/80 p-4">
                            <div class="mb-1 text-sm text-gray-600">Próximo Período</div>
                            <div class="text-lg font-bold text-rose-600">
                                {{ daysUntilPeriod > 0 ? `En ${daysUntilPeriod} días` : daysUntilPeriod === 0 ? 'Hoy' : 'Ya pasó' }}
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                {{ new Date(stats.next_period_predicted).toLocaleDateString('es-ES') }}
                            </div>
                        </div>
                        <div v-if="stats.average_cycle_length" class="mt-4 rounded-xl border border-rose-100 bg-white/80 p-4">
                            <div class="mb-1 text-sm text-gray-600">Ciclo Promedio</div>
                            <div class="text-lg font-bold text-rose-600">
                                {{ stats.average_cycle_length }} días
                            </div>
                        </div>
                    </div>

                    <!-- Consejos Personalizados -->
                    <div v-if="stats.advice" class="feminine-surface rounded-2xl border border-indigo-100/80 p-6 shadow-[0_16px_42px_rgba(99,102,241,0.14)]">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span>💡</span>
                            Consejos para Ti
                        </h3>
                        <div class="mb-3">
                            <h4 class="mb-2 font-semibold text-indigo-900">{{ stats.advice.title }}</h4>
                            <ul class="space-y-2">
                                <li v-for="(tip, index) in stats.advice.tips.slice(0, 3)" :key="index" class="text-sm text-gray-700">
                                    {{ tip }}
                                </li>
                            </ul>
                        </div>
                        <div v-if="stats.advice.wellness.length > 0" class="mt-4 border-t border-indigo-100 pt-4">
                            <h5 class="mb-2 text-xs font-semibold text-indigo-800">Bienestar:</h5>
                            <ul class="space-y-1">
                                <li v-for="(wellness, index) in stats.advice.wellness" :key="index" class="text-xs text-gray-600">
                                    • {{ wellness }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Síntomas -->
                <div v-if="stats?.symptoms_summary && Object.keys(stats.symptoms_summary).length > 0" class="feminine-panel mb-8 p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>📈</span>
                        Síntomas Más Frecuentes
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <div
                            v-for="(count, symptom) in stats.symptoms_summary"
                            :key="symptom"
                            class="rounded-xl bg-gradient-to-r from-pink-100 to-rose-100 px-4 py-2"
                        >
                            <span class="font-semibold text-pink-700">{{ getSymptomName(symptom) }}</span>
                            <span class="ml-2 text-sm text-gray-600">({{ count }}x)</span>
                        </div>
                    </div>
                </div>

                <!-- Registros -->
                <div v-if="trackings.length > 0" class="space-y-4">
                    <div
                        v-for="tracking in trackings"
                        :key="tracking.id"
                        class="group relative overflow-hidden rounded-2xl border border-rose-100/80 bg-white/80 transition-all shadow-[0_14px_36px_rgba(236,72,153,0.11)] hover:-translate-y-1 hover:border-rose-300 hover:shadow-[0_20px_48px_rgba(236,72,153,0.16)]"
                    >
                        <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b" :class="getPhaseColor(tracking.phase)"></div>
                        <div class="relative p-6 ml-4">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                                        {{ new Date(tracking.date).toLocaleDateString('es-ES', {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        }) }}
                                    </h3>
                                    <span
                                        v-if="tracking.phase"
                                        :class="['px-4 py-2 rounded-full text-sm font-bold text-white bg-gradient-to-r', getPhaseColor(tracking.phase)]"
                                    >
                                        {{ getPhaseName(tracking.phase) }}
                                    </span>
                                </div>
                                <span v-if="tracking.cycle_day" class="text-sm font-semibold text-rose-700">
                                    Día {{ tracking.cycle_day }}
                                </span>
                            </div>
                            <div v-if="tracking.mood" class="mb-3">
                                <span class="text-2xl">{{ tracking.mood }}</span>
                            </div>
                            <div v-if="tracking.symptoms && tracking.symptoms.length > 0" class="mb-3">
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="symptom in tracking.symptoms"
                                        :key="symptom"
                                        class="rounded-full border border-pink-200 bg-pink-50 px-3 py-1 text-xs font-semibold text-pink-700"
                                    >
                                        {{ symptom }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="tracking.flow_level" class="mb-3">
                                <span class="text-sm text-gray-600">Flujo: </span>
                                <span class="text-pink-600 font-semibold">
                                    {{ tracking.flow_level }}/5
                                </span>
                            </div>
                            <p v-if="tracking.notes" class="text-gray-700 text-sm mb-4">
                                {{ tracking.notes }}
                            </p>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('cycle.edit', tracking.id)"
                                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-sm font-semibold text-white transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteTracking(tracking.id)"
                                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition-all hover:bg-red-100"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="feminine-panel py-20 text-center"
                >
                    <span class="text-8xl block mb-6">🌸</span>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">No hay registros aún</h3>
                    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                        Comienza a registrar tu ciclo para llevar un mejor seguimiento
                    </p>
                    <Link
                        :href="route('cycle.create')"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-8 py-4 text-lg font-bold text-white shadow-xl shadow-rose-400/30 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Crear Mi Primer Registro
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Registro"
            message="¿Estás segura de que quieres eliminar este registro de ciclo? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

