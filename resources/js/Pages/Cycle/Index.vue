<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

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

interface Props {
    trackings: CycleTracking[];
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

const deleteTracking = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar este registro?')) {
        router.delete(route('cycle.destroy', id));
    }
};
</script>

<template>
    <Head title="Seguimiento de Ciclo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>🌸</span>
                    Mi Seguimiento
                </h2>
                <Link
                    :href="route('cycle.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Nuevo Registro
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="trackings.length > 0" class="space-y-4">
                    <div
                        v-for="tracking in trackings"
                        :key="tracking.id"
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-2 border-pink-200 hover:border-pink-400 transition-all shadow-lg hover:shadow-xl"
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
                                <span v-if="tracking.cycle_day" class="text-sm text-gray-600 font-semibold">
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
                                        class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-semibold"
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
                                    class="px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-full text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="deleteTracking(tracking.id)"
                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-semibold hover:bg-red-200 transition-all"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 rounded-3xl border-4 border-pink-300"
                >
                    <span class="text-8xl block mb-6">🌸</span>
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">No hay registros aún</h3>
                    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                        Comienza a registrar tu ciclo para llevar un mejor seguimiento
                    </p>
                    <Link
                        :href="route('cycle.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Crear Mi Primer Registro
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

