<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

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
    tracking: CycleTracking;
}

const props = defineProps<Props>();

const form = useForm({
    date: props.tracking.date ? new Date(props.tracking.date).toISOString().split('T')[0] : '',
    phase: props.tracking.phase || '',
    cycle_day: props.tracking.cycle_day || null as number | null,
    symptoms: props.tracking.symptoms || [] as string[],
    mood: props.tracking.mood || '',
    flow_level: props.tracking.flow_level || null as number | null,
    notes: props.tracking.notes || '',
});

const symptomOptions = [
    { value: 'cramps', label: 'Cólicos', icon: '😣' },
    { value: 'bloating', label: 'Hinchazón', icon: '🎈' },
    { value: 'mood_swings', label: 'Cambios de humor', icon: '🎭' },
    { value: 'headache', label: 'Dolor de cabeza', icon: '🤕' },
    { value: 'fatigue', label: 'Fatiga', icon: '😴' },
    { value: 'acne', label: 'Acné', icon: '🌋' },
    { value: 'breast_tenderness', label: 'Sensibilidad en senos', icon: '🍈' },
    { value: 'back_pain', label: 'Dolor de espalda', icon: '🦴' },
    { value: 'nausea', label: 'Náuseas', icon: '🤢' },
    { value: 'insomnia', label: 'Insomnio', icon: '🦉' },
    { value: 'cravings', label: 'Antojos', icon: '🍫' },
    { value: 'energy_boost', label: 'Subidón de energía', icon: '⚡' },
];

const selectedSymptoms = ref<string[]>(form.symptoms);

const toggleSymptom = (symptom: string) => {
    const index = selectedSymptoms.value.indexOf(symptom);
    if (index > -1) {
        selectedSymptoms.value.splice(index, 1);
    } else {
        selectedSymptoms.value.push(symptom);
    }
    form.symptoms = selectedSymptoms.value;
};

const submit = () => {
    form.put(route('cycle.update', props.tracking.id));
};

const phaseNames: Record<string, string> = {
    menstrual: 'Menstrual',
    follicular: 'Folicular',
    ovulation: 'Ovulación',
    luteal: 'Lútea',
};
</script>

<template>
    <Head title="Editar Registro" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>🌸</span>
                Editar Registro
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-2xl bg-white border-2 border-pink-200 shadow-xl">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-20 -mt-20"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="date" value="Fecha *" class="text-pink-800 font-semibold mb-2" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
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
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                        :value="form.cycle_day?.toString() || ''"
                                        @input="form.cycle_day = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                        placeholder="Auto-calculado"
                                    />
                                    <InputError class="mt-2" :message="form.errors.cycle_day" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="phase" value="Fase" class="text-pink-800 font-semibold mb-2" />
                                <select
                                    id="phase"
                                    class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                    v-model="form.phase"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="menstrual">Menstrual (Días 1-5)</option>
                                    <option value="follicular">Folicular (Días 6-13)</option>
                                    <option value="ovulation">Ovulación (Días 14-16)</option>
                                    <option value="luteal">Lútea (Días 17-28)</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <InputLabel value="Síntomas" class="text-pink-800 font-semibold mb-2" />
                                <div class="mt-2 flex flex-wrap gap-2 bg-gray-50 p-4 rounded-lg border-2 border-pink-100">
                                    <button
                                        v-for="symptom in symptomOptions"
                                        :key="symptom.value"
                                        type="button"
                                        @click="toggleSymptom(symptom.value)"
                                        :class="[
                                            'px-4 py-2 rounded-lg text-sm font-semibold transition-all border-2 flex items-center gap-1',
                                            selectedSymptoms.includes(symptom.value)
                                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white border-pink-600 shadow-md'
                                                : 'bg-white text-pink-700 border-pink-200 hover:bg-pink-50 hover:border-pink-300'
                                        ]"
                                    >
                                        <span>{{ symptom.icon }}</span>
                                        <span>{{ symptom.label }}</span>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="mood" value="Estado de ánimo (emoji)" class="text-pink-800 font-semibold mb-2" />
                                    <TextInput
                                        id="mood"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-center text-2xl focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
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
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                        :value="form.flow_level?.toString() || ''"
                                        @input="form.flow_level = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                        placeholder="1 = Ligero, 5 = Abundante"
                                    />
                                    <InputError class="mt-2" :message="form.errors.flow_level" />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="notes" value="Notas" class="text-pink-800 font-semibold mb-2" />
                                <textarea
                                    id="notes"
                                    class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 resize-none"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Notas adicionales sobre cómo te sientes..."
                                ></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-4 border-t-2 border-pink-100">
                                <Link
                                    :href="route('cycle.index')"
                                    class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-lg font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">🌸</span>
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Cambios</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

