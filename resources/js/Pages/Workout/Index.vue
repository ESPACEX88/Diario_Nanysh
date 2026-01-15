<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import WorkoutCalendar from '@/Components/WorkoutCalendar.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Exercise {
  name: string;
  sets?: number;
  reps?: string;
  weight?: string;
}

interface Workout {
  id: number;
  date: string;
  routine_name: string;
  intensity: 'light' | 'moderate' | 'intense';
  duration_minutes?: number;
  exercises?: Exercise[];
  notes?: string;
}

interface Stats {
  total_workouts: number;
  total_minutes: number;
  streak: number;
  current_month: string;
}

interface Props {
  workouts: Workout[];
  stats: Stats;
  currentMonth: number;
  currentYear: number;
}

const props = defineProps<Props>();

const handleMonthChange = ({ month, year }: { month: number; year: number }) => {
  router.get(route('workouts.index'), { month, year }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const intensityLabel = (intensity: string): string => {
  const labels: Record<string, string> = {
    light: 'Ligero',
    moderate: 'Moderado',
    intense: 'Intenso',
  };
  return labels[intensity] || intensity;
};

const intensityColor = (intensity: string): string => {
  const colors: Record<string, string> = {
    light: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    moderate: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    intense: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  };
  return colors[intensity] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
  <Head title="Entrenamientos" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Mis Entrenamientos 💪
        </h2>
        <a href="/workouts/create">
          <PrimaryButton>
            + Registrar entrenamiento
          </PrimaryButton>
        </a>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Entrenamientos este mes</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                  {{ stats.total_workouts }}
                </p>
              </div>
              <div class="text-4xl">💪</div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Minutos totales</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                  {{ stats.total_minutes }}
                </p>
              </div>
              <div class="text-4xl">⏱️</div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Racha actual</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                  {{ stats.streak }}
                  <span class="text-base text-gray-600 dark:text-gray-400">días</span>
                </p>
              </div>
              <div class="text-4xl">🔥</div>
            </div>
          </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
          <WorkoutCalendar
            :workouts="workouts"
            :current-month="currentMonth"
            :current-year="currentYear"
            @month-change="handleMonthChange"
          />
        </div>

        <!-- Recent Workouts List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Entrenamientos recientes
          </h3>

          <div v-if="workouts.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p class="text-lg mb-2">No hay entrenamientos registrados</p>
            <p class="text-sm">¡Comienza registrando tu primer entrenamiento!</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="workout in workouts.slice(0, 5)"
              :key="workout.id"
              class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                      {{ workout.routine_name }}
                    </h4>
                    <span
                      :class="[
                        'px-2 py-1 rounded-full text-xs font-medium',
                        intensityColor(workout.intensity)
                      ]"
                    >
                      {{ intensityLabel(workout.intensity) }}
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    {{ new Date(workout.date).toLocaleDateString('es-ES', {
                      weekday: 'long',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    }) }}
                  </p>

                  <div v-if="workout.duration_minutes" class="text-sm text-gray-500 dark:text-gray-400">
                    ⏱️ {{ workout.duration_minutes }} minutos
                  </div>

                  <div v-if="workout.exercises && workout.exercises.length > 0" class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                      Ejercicios ({{ workout.exercises.length }}):
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="(exercise, index) in workout.exercises"
                        :key="index"
                        class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded"
                      >
                        {{ exercise.name }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="flex gap-2 ml-4">
                  <a
                    :href="`/workouts/${workout.id}/edit`"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                  >
                    Editar
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
