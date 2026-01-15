<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { ref } from 'vue';

interface Exercise {
  name: string;
  sets?: number;
  reps?: string;
  weight?: string;
}

interface Workout {
  id: number;
  workout_date: string;
  routine_name: string;
  exercises: Exercise[];
  duration_minutes: number | null;
  notes: string;
  intensity: 'light' | 'moderate' | 'intense';
}

interface Props {
  workout: Workout;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const form = useForm({
  workout_date: props.workout.workout_date,
  routine_name: props.workout.routine_name,
  exercises: props.workout.exercises || [],
  duration_minutes: props.workout.duration_minutes,
  notes: props.workout.notes || '',
  intensity: props.workout.intensity,
});

const deleteForm = useForm({});

const addExercise = () => {
  form.exercises.push({
    name: '',
    sets: undefined,
    reps: '',
    weight: '',
  });
};

const removeExercise = (index: number) => {
  form.exercises.splice(index, 1);
};

const submit = () => {
  form.put(route('workouts.update', props.workout.id), {
    onSuccess: () => {
      // Redirect handled by controller
    },
  });
};

const deleteWorkout = () => {
  deleteForm.delete(route('workouts.destroy', props.workout.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};

const intensityOptions = [
  { value: 'light', label: 'Ligero 🟢', color: 'bg-green-100 text-green-800 border-green-300' },
  { value: 'moderate', label: 'Moderado 🟡', color: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
  { value: 'intense', label: 'Intenso 🔴', color: 'bg-red-100 text-red-800 border-red-300' },
];
</script>

<template>
  <Head title="Editar Entrenamiento" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Editar Entrenamiento
        </h2>
        <a
          href="/workouts"
          class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
        >
          ← Volver al calendario
        </a>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Date -->
            <div>
              <InputLabel for="workout_date" value="Fecha" />
              <TextInput
                id="workout_date"
                type="date"
                v-model="form.workout_date"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.workout_date" />
            </div>

            <!-- Routine Name -->
            <div>
              <InputLabel for="routine_name" value="Nombre de la rutina" />
              <TextInput
                id="routine_name"
                type="text"
                v-model="form.routine_name"
                class="mt-1 block w-full"
                placeholder="Ej: Piernas, Tren Superior, Cardio..."
                required
              />
              <InputError class="mt-2" :message="form.errors.routine_name" />
            </div>

            <!-- Intensity -->
            <div>
              <InputLabel value="Intensidad" />
              <div class="mt-2 grid grid-cols-3 gap-3">
                <button
                  v-for="option in intensityOptions"
                  :key="option.value"
                  type="button"
                  @click="form.intensity = option.value as 'light' | 'moderate' | 'intense'"
                  :class="[
                    'py-3 px-4 rounded-lg border-2 font-medium transition-all',
                    form.intensity === option.value
                      ? option.color
                      : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                  ]"
                >
                  {{ option.label }}
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.intensity" />
            </div>

            <!-- Duration -->
            <div>
              <InputLabel for="duration_minutes" value="Duración (minutos)" />
              <TextInput
                id="duration_minutes"
                type="number"
                v-model="form.duration_minutes"
                class="mt-1 block w-full"
                placeholder="60"
                min="1"
              />
              <InputError class="mt-2" :message="form.errors.duration_minutes" />
            </div>

            <!-- Exercises -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <InputLabel value="Ejercicios" />
                <SecondaryButton type="button" @click="addExercise">
                  + Agregar ejercicio
                </SecondaryButton>
              </div>

              <div v-if="form.exercises.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">
                No hay ejercicios añadidos. Haz clic en "Agregar ejercicio" para comenzar.
              </div>

              <div v-else class="space-y-4">
                <div
                  v-for="(exercise, index) in form.exercises"
                  :key="index"
                  class="p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700"
                >
                  <div class="flex items-start justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                      Ejercicio {{ index + 1 }}
                    </span>
                    <button
                      type="button"
                      @click="removeExercise(index)"
                      class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm"
                    >
                      Eliminar
                    </button>
                  </div>

                  <div class="space-y-3">
                    <div>
                      <InputLabel :for="`exercise_name_${index}`" value="Nombre" />
                      <TextInput
                        :id="`exercise_name_${index}`"
                        type="text"
                        v-model="exercise.name"
                        class="mt-1 block w-full"
                        placeholder="Ej: Sentadillas"
                        required
                      />
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                      <div>
                        <InputLabel :for="`exercise_sets_${index}`" value="Series" />
                        <TextInput
                          :id="`exercise_sets_${index}`"
                          type="number"
                          v-model="exercise.sets"
                          class="mt-1 block w-full"
                          placeholder="3"
                          min="1"
                        />
                      </div>

                      <div>
                        <InputLabel :for="`exercise_reps_${index}`" value="Reps" />
                        <TextInput
                          :id="`exercise_reps_${index}`"
                          type="text"
                          v-model="exercise.reps"
                          class="mt-1 block w-full"
                          placeholder="10-12"
                        />
                      </div>

                      <div>
                        <InputLabel :for="`exercise_weight_${index}`" value="Peso" />
                        <TextInput
                          :id="`exercise_weight_${index}`"
                          type="text"
                          v-model="exercise.weight"
                          class="mt-1 block w-full"
                          placeholder="20kg"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div>
              <InputLabel for="notes" value="Notas (opcional)" />
              <textarea
                id="notes"
                v-model="form.notes"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                rows="3"
                placeholder="Cómo te sentiste, observaciones, etc..."
              ></textarea>
              <InputError class="mt-2" :message="form.errors.notes" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
              <DangerButton type="button" @click="showDeleteModal = true">
                Eliminar
              </DangerButton>

              <div class="flex items-center gap-3">
                <a
                  href="/workouts"
                  class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                >
                  Cancelar
                </a>
                <PrimaryButton :disabled="form.processing">
                  Guardar cambios
                </PrimaryButton>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteWorkout"
    >
      <template #title>Eliminar entrenamiento</template>
      <template #content>
        ¿Estás segura de que quieres eliminar este entrenamiento? Esta acción no se puede deshacer.
      </template>
    </ConfirmModal>
  </AuthenticatedLayout>
</template>
