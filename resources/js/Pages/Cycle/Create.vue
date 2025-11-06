<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const form = useForm({
    date: new Date().toISOString().split('T')[0],
    phase: '',
    cycle_day: null as number | null,
    symptoms: [] as string[],
    mood: '',
    flow_level: null as number | null,
    notes: '',
});

const symptomOptions = ['cramps', 'bloating', 'mood_swings', 'headache', 'fatigue', 'acne'];
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

const submit = () => {
    form.post(route('cycle.store'));
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
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="date" value="Fecha *" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.date"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.date" />
                                </div>

                                <div>
                                    <InputLabel for="cycle_day" value="Día del ciclo" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="cycle_day"
                                        type="number"
                                        min="1"
                                        max="35"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.cycle_day"
                                    />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="phase" value="Fase" class="text-pink-700 font-semibold" />
                                <select
                                    id="phase"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.phase"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="menstrual">Menstrual</option>
                                    <option value="follicular">Folicular</option>
                                    <option value="ovulation">Ovulación</option>
                                    <option value="luteal">Lútea</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <InputLabel value="Síntomas" class="text-pink-700 font-semibold" />
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button
                                        v-for="symptom in symptomOptions"
                                        :key="symptom"
                                        type="button"
                                        @click="toggleSymptom(symptom)"
                                        :class="[
                                            'px-4 py-2 rounded-full text-sm font-semibold transition-all',
                                            selectedSymptoms.includes(symptom)
                                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white'
                                                : 'bg-pink-100 text-pink-700 hover:bg-pink-200'
                                        ]"
                                    >
                                        {{ symptom === 'cramps' ? 'Cólicos' : symptom === 'bloating' ? 'Hinchazón' : symptom === 'mood_swings' ? 'Cambios de humor' : symptom === 'headache' ? 'Dolor de cabeza' : symptom === 'fatigue' ? 'Fatiga' : 'Acné' }}
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="mood" value="Estado de ánimo (emoji)" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="mood"
                                        type="text"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl text-center text-2xl"
                                        v-model="form.mood"
                                        maxlength="2"
                                        placeholder="😊"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="flow_level" value="Nivel de flujo (1-5)" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="flow_level"
                                        type="number"
                                        min="1"
                                        max="5"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.flow_level"
                                    />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="notes" value="Notas" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="notes"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Notas adicionales..."
                                ></textarea>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">🌸</span>
                                    Guardar Registro
                                </PrimaryButton>
                                <Link
                                    :href="route('cycle.index')"
                                    class="text-gray-600 hover:text-pink-600 font-semibold transition-colors"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

