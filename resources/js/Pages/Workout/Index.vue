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
      <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
          Mis Entrenamientos 💪
        </h2>
        <a href="/workouts/create">
          <PrimaryButton>
            + Registrar entrenamiento
          </PrimaryButton>
        </a>
      </div>
    </template>

    <div class="py-10 sm:py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div class="feminine-surface rounded-3xl border border-rose-100/80 p-6 shadow-[0_18px_45px_rgba(236,72,153,0.12)]">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-rose-700">Entrenamientos este mes</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">
                  {{ stats.total_workouts }}
                </p>
              </div>
              <div class="text-4xl">💪</div>
            </div>
          </div>

          <div class="feminine-surface rounded-3xl border border-rose-100/80 p-6 shadow-[0_18px_45px_rgba(236,72,153,0.12)]">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-rose-700">Minutos totales</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">
                  {{ stats.total_minutes }}
                </p>
              </div>
              <div class="text-4xl">⏱️</div>
            </div>
          </div>

          <div class="feminine-surface rounded-3xl border border-rose-100/80 p-6 shadow-[0_18px_45px_rgba(236,72,153,0.12)]">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-rose-700">Racha actual</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">
                  {{ stats.streak }}
                  <span class="text-base text-rose-600">días</span>
                </p>
              </div>
              <div class="text-4xl">🔥</div>
            </div>
          </div>
        </div>

        <!-- Calendar -->
        <div class="feminine-panel p-6">
          <WorkoutCalendar
            :workouts="workouts"
            :current-month="currentMonth"
            :current-year="currentYear"
            @month-change="handleMonthChange"
          />
        </div>

        <!-- Recent Workouts List -->
        <div class="feminine-panel p-6">
          <h3 class="mb-4 text-lg font-semibold text-gray-900">
            Entrenamientos recientes
          </h3>

          <div v-if="workouts.length === 0" class="py-8 text-center text-gray-500">
            <p class="text-lg mb-2">No hay entrenamientos registrados</p>
            <p class="text-sm">¡Comienza registrando tu primer entrenamiento!</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="workout in workouts.slice(0, 5)"
              :key="workout.id"
              class="rounded-2xl border border-rose-100 bg-white/80 p-4 shadow-sm shadow-rose-400/10 transition-all hover:-translate-y-0.5 hover:border-rose-300 hover:shadow-md"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h4 class="text-lg font-medium text-gray-900">
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
                  
                  <p class="mb-2 text-sm text-rose-700">
                    {{ new Date(workout.date).toLocaleDateString('es-ES', {
                      weekday: 'long',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    }) }}
                  </p>

                  <div v-if="workout.duration_minutes" class="text-sm text-gray-500">
                    ⏱️ {{ workout.duration_minutes }} minutos
                  </div>

                  <div v-if="workout.exercises && workout.exercises.length > 0" class="mt-3">
                    <p class="mb-1 text-sm text-gray-600">
                      Ejercicios ({{ workout.exercises.length }}):
                    </p>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="(exercise, index) in workout.exercises"
                        :key="index"
                        class="rounded-full border border-rose-100 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700"
                      >
                        {{ exercise.name }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="flex gap-2 ml-4">
                  <a
                    :href="`/workouts/${workout.id}/edit`"
                    class="text-sm font-semibold text-rose-600 transition-colors hover:text-rose-800"
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
