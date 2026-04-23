<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted } from 'vue';

// Función para obtener la fecha local
const getLocalDate = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

interface Props {
    suggestedPhase?: string;
    suggestedCycleDay?: number;
    stats?: {
        average_cycle_length?: number;
        next_period_predicted?: string | null;
        current_phase?: string | null;
        cycle_day?: number | null;
    };
}

const props = defineProps<Props>();

const form = useForm({
    date: getLocalDate(),
    phase: props.suggestedPhase || '',
    cycle_day: props.suggestedCycleDay || null as number | null,
    symptoms: [] as string[],
    mood: '',
    flow_level: null as number | null,
    notes: '',
});

const symptomOptions = [
    { value: 'cramps', label: 'Cólicos', icon: '😣' },
    { value: 'bloating', label: 'Hinchazón', icon: '🤰' },
    { value: 'mood_swings', label: 'Cambios de humor', icon: '😤' },
    { value: 'headache', label: 'Dolor de cabeza', icon: '🤕' },
    { value: 'fatigue', label: 'Fatiga', icon: '😴' },
    { value: 'acne', label: 'Acné', icon: '😟' },
    { value: 'back_pain', label: 'Dolor de espalda', icon: '🫠' },
    { value: 'breast_tenderness', label: 'Sensibilidad en senos', icon: '💔' },
    { value: 'nausea', label: 'Náuseas', icon: '🤢' },
    { value: 'insomnia', label: 'Insomnio', icon: '🌙' },
    { value: 'cravings', label: 'Antojos', icon: '🍫' },
    { value: 'anxiety', label: 'Ansiedad', icon: '😰' },
];
const selectedSymptoms = ref<string[]>([]);

const toggleSymptom = (symptom: string) => {
    const index = selectedSymptoms.value.indexOf(symptom);
    if (index > -1) {
        selectedSymptoms.value.splice(index, 1);
    } else {
        selectedSymptoms.value.push(symptom);
    }
    form.symptoms = selectedSymptoms.value;
};

const getSymptomLabel = (value: string) => {
    const symptom = symptomOptions.find(s => s.value === value);
    return symptom ? symptom.label : value;
};

const useSuggested = () => {
    if (props.suggestedPhase) {
        form.phase = props.suggestedPhase;
    }
    if (props.suggestedCycleDay) {
        form.cycle_day = props.suggestedCycleDay;
    }
};

onMounted(() => {
    useSuggested();
});

const submit = () => {
    form.post(route('cycle.store'));
};

const phaseNames: Record<string, string> = {
    menstrual: 'Menstrual',
    follicular: 'Folicular',
    ovulation: 'Ovulación',
    luteal: 'Lútea',
};
</script>

<template>
    <Head title="Nuevo Registro" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>🌸</span>
                Nuevo Registro
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <!-- Sugerencias inteligentes -->
                <div v-if="suggestedPhase || suggestedCycleDay" class="mb-6 rounded-2xl border border-indigo-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">💡</span>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-900 mb-2">Sugerencias Inteligentes</h3>
                            <p class="text-sm text-blue-800 mb-3">
                                Basándome en tus registros anteriores, hoy probablemente estás en:
                            </p>
                            <div class="flex gap-2 flex-wrap">
                                <span v-if="suggestedPhase" class="rounded-full border border-blue-300 bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900">
                                    Fase: {{ phaseNames[suggestedPhase] || suggestedPhase }}
                                </span>
                                <span v-if="suggestedCycleDay" class="rounded-full border border-indigo-300 bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-900">
                                    Día {{ suggestedCycleDay }} del ciclo
                                </span>
                            </div>
                            <button
                                type="button"
                                @click="useSuggested"
                                class="mt-3 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700"
                            >
                                Usar Sugerencias
                            </button>
                        </div>
                    </div>
                </div>

                <div class="feminine-panel relative overflow-hidden border border-rose-100/80 p-0 shadow-[0_18px_45px_rgba(236,72,153,0.12)]">
                    <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-gradient-to-br from-rose-200/35 to-fuchsia-200/30 blur-sm"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <!-- Primera fila: Fecha y Día del ciclo -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="date" value="Fecha *" class="text-pink-800 font-semibold mb-2" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                        v-model="form.date"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.date" />
                                </div>

                                <div>
                                    <InputLabel for="cycle_day" value="Día del ciclo" class="text-pink-800 font-semibold mb-2" />
                                    <input
                                        id="cycle_day"
                                        type="number"
                                        min="1"
                                        max="35"
                                        class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                        :value="form.cycle_day?.toString() || ''"
                                        @input="form.cycle_day = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                        placeholder="Auto-calculado"
                                    />
                                    <InputError class="mt-2" :message="form.errors.cycle_day" />
                                </div>
                            </div>

                            <!-- Segunda fila: Fase -->
                            <div class="mb-6">
                                <InputLabel for="phase" value="Fase" class="text-pink-800 font-semibold mb-2" />
                                <select
                                    id="phase"
                                    class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                    v-model="form.phase"
                                >
                                    <option value="">Seleccionar (se calculará automáticamente si se deja vacío)...</option>
                                    <option value="menstrual">Menstrual (Días 1-5)</option>
                                    <option value="follicular">Folicular (Días 6-13)</option>
                                    <option value="ovulation">Ovulación (Días 14-16)</option>
                                    <option value="luteal">Lútea (Días 17-28)</option>
                                </select>
                            </div>

                            <!-- Tercera fila: Síntomas -->
                            <div class="mb-6">
                                <InputLabel value="Síntomas" class="text-pink-800 font-semibold mb-2" />
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <button
                                        v-for="symptom in symptomOptions"
                                        :key="symptom.value"
                                        type="button"
                                        @click="toggleSymptom(symptom.value)"
                                        :class="[
                                            'px-4 py-3 rounded-xl text-sm font-semibold transition-all border-2 flex flex-col items-center gap-1',
                                            selectedSymptoms.includes(symptom.value)
                                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white border-pink-600 shadow-md transform scale-105'
                                                : 'bg-white text-pink-700 border-pink-200 hover:bg-pink-50 hover:border-pink-300'
                                        ]"
                                    >
                                        <span class="text-2xl">{{ symptom.icon }}</span>
                                        <span>{{ symptom.label }}</span>
                                    </button>
                                </div>
                                <div v-if="selectedSymptoms.length > 0" class="mt-3 rounded-xl border border-pink-200 bg-pink-50 p-3">
                                    <p class="mb-1 text-xs text-gray-600">Síntomas seleccionados:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="symptom in selectedSymptoms"
                                            :key="symptom"
                                            class="rounded-full border border-pink-300 bg-pink-100 px-2 py-1 text-xs font-semibold text-pink-800"
                                        >
                                            {{ getSymptomLabel(symptom) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuarta fila: Estado de ánimo y Flujo -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="mood" value="Estado de ánimo (emoji)" class="text-pink-800 font-semibold mb-2" />
                                    <TextInput
                                        id="mood"
                                        type="text"
                                        class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-center text-2xl focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                        v-model="form.mood"
                                        maxlength="2"
                                        placeholder="😊"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="flow_level" value="Nivel de flujo (1-5)" class="text-pink-800 font-semibold mb-2" />
                                    <input
                                        id="flow_level"
                                        type="number"
                                        min="1"
                                        max="5"
                                        class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                        :value="form.flow_level?.toString() || ''"
                                        @input="form.flow_level = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                        placeholder="1 = Ligero, 5 = Abundante"
                                    />
                                    <InputError class="mt-2" :message="form.errors.flow_level" />
                                </div>
                            </div>

                            <!-- Quinta fila: Notas -->
                            <div class="mb-6">
                                <InputLabel for="notes" value="Notas" class="text-pink-800 font-semibold mb-2" />
                                <textarea
                                    id="notes"
                                    class="mt-1 block w-full resize-none rounded-2xl border border-rose-200 bg-white/85 px-4 py-3 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Notas adicionales sobre cómo te sientes..."
                                ></textarea>
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end gap-4 border-t border-rose-100 pt-4">
                                <Link
                                    :href="route('cycle.index')"
                                    class="rounded-xl px-6 py-2.5 font-semibold text-gray-700 transition-colors hover:bg-rose-50 hover:text-rose-800"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-8 py-3 font-bold shadow-lg shadow-rose-400/25 hover:from-rose-600 hover:to-fuchsia-600"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">🌸</span>
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Registro</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

