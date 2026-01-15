<template>
  <div class="w-full">
    <div class="flex items-center justify-between mb-6">
      <button
        @click="previousMonth"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <h2 class="text-xl font-semibold">
        {{ currentMonthName }} {{ currentYear }}
      </h2>
      
      <button
        @click="nextMonth"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Days of the week -->
    <div class="grid grid-cols-7 gap-2 mb-2">
      <div
        v-for="day in daysOfWeek"
        :key="day"
        class="text-center text-sm font-medium text-gray-600 dark:text-gray-400 py-2"
      >
        {{ day }}
      </div>
    </div>

    <!-- Calendar days -->
    <div class="grid grid-cols-7 gap-2">
      <div
        v-for="day in calendarDays"
        :key="day.date"
        :class="[
          'aspect-square rounded-lg border-2 transition-all cursor-pointer',
          getDayClasses(day)
        ]"
        @click="selectDay(day)"
      >
        <div class="h-full flex flex-col items-center justify-center p-2">
          <span
            :class="[
              'text-sm font-medium mb-1',
              day.isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600'
            ]"
          >
            {{ day.dayNumber }}
          </span>
          
          <!-- Indicator for workout day -->
          <div v-if="day.workout" class="flex flex-col items-center gap-1 w-full">
            <div
              :class="[
                'w-2 h-2 rounded-full',
                getIntensityColor(day.workout.intensity)
              ]"
            />
            <div v-if="day.workout.duration_minutes" class="text-xs text-gray-500 dark:text-gray-400">
              {{ day.workout.duration_minutes }}m
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 text-sm">
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-green-500"></div>
        <span class="text-gray-600 dark:text-gray-400">Ligero</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
        <span class="text-gray-600 dark:text-gray-400">Moderado</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-red-500"></div>
        <span class="text-gray-600 dark:text-gray-400">Intenso</span>
      </div>
    </div>

    <!-- Day detail modal -->
    <Modal :show="showDetailModal" @close="showDetailModal = false">
      <div class="p-6" v-if="selectedDay?.workout">
        <h3 class="text-lg font-semibold mb-4">
          {{ selectedDay.workout.routine_name }}
        </h3>
        
        <div class="space-y-3">
          <div>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Fecha:</span>
            <p>{{ formatDate(selectedDay.date) }}</p>
          </div>
          
          <div v-if="selectedDay.workout.duration_minutes">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Duración:</span>
            <p>{{ selectedDay.workout.duration_minutes }} minutos</p>
          </div>
          
          <div>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Intensidad:</span>
            <p class="capitalize">{{ getIntensityLabel(selectedDay.workout.intensity) }}</p>
          </div>
          
          <div v-if="selectedDay.workout.exercises && selectedDay.workout.exercises.length > 0">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Ejercicios:</span>
            <ul class="mt-2 space-y-2">
              <li
                v-for="(exercise, index) in selectedDay.workout.exercises"
                :key="index"
                class="p-2 bg-gray-50 dark:bg-gray-800 rounded"
              >
                <p class="font-medium">{{ exercise.name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  <span v-if="exercise.sets">{{ exercise.sets }} series</span>
                  <span v-if="exercise.reps"> × {{ exercise.reps }} reps</span>
                  <span v-if="exercise.weight"> - {{ exercise.weight }}</span>
                </p>
              </li>
            </ul>
          </div>
          
          <div v-if="selectedDay.workout.notes">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Notas:</span>
            <p class="mt-1 text-gray-700 dark:text-gray-300">{{ selectedDay.workout.notes }}</p>
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <a
            :href="`/workouts/${selectedDay.workout.id}/edit`"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center"
          >
            Editar
          </a>
          <button
            @click="showDetailModal = false"
            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
          >
            Cerrar
          </button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

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

interface CalendarDay {
  date: string;
  dayNumber: number;
  isCurrentMonth: boolean;
  isToday: boolean;
  workout?: Workout;
}

const props = defineProps<{
  workouts: Workout[];
  currentMonth: number;
  currentYear: number;
}>();

const emit = defineEmits<{
  monthChange: [{ month: number; year: number }];
}>();

const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
const monthNames = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const currentMonth = ref(props.currentMonth);
const currentYear = ref(props.currentYear);
const showDetailModal = ref(false);
const selectedDay = ref<CalendarDay | null>(null);

const currentMonthName = computed(() => monthNames[currentMonth.value - 1]);

const calendarDays = computed(() => {
  const days: CalendarDay[] = [];
  const firstDay = new Date(currentYear.value, currentMonth.value - 1, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value, 0);
  const startingDayOfWeek = firstDay.getDay();
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // Previous month days
  const prevMonthLastDay = new Date(currentYear.value, currentMonth.value - 1, 0);
  for (let i = startingDayOfWeek - 1; i >= 0; i--) {
    const day = prevMonthLastDay.getDate() - i;
    const date = new Date(currentYear.value, currentMonth.value - 2, day);
    days.push({
      date: formatDateString(date),
      dayNumber: day,
      isCurrentMonth: false,
      isToday: false,
    });
  }

  // Current month days
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(currentYear.value, currentMonth.value - 1, day);
    const dateString = formatDateString(date);
    const workout = props.workouts.find(w => w.date === dateString);
    
    days.push({
      date: dateString,
      dayNumber: day,
      isCurrentMonth: true,
      isToday: date.getTime() === today.getTime(),
      workout,
    });
  }

  // Next month days
  const remainingDays = 42 - days.length; // 6 weeks × 7 days
  for (let day = 1; day <= remainingDays; day++) {
    const date = new Date(currentYear.value, currentMonth.value, day);
    days.push({
      date: formatDateString(date),
      dayNumber: day,
      isCurrentMonth: false,
      isToday: false,
    });
  }

  return days;
});

function formatDateString(date: Date): string {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function getDayClasses(day: CalendarDay): string {
  const classes = [];
  
  if (day.isToday) {
    classes.push('border-blue-500');
  } else if (day.workout) {
    classes.push('border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20');
  } else {
    classes.push('border-gray-200 dark:border-gray-700');
  }
  
  if (day.isCurrentMonth && !day.workout) {
    classes.push('hover:bg-gray-50 dark:hover:bg-gray-800');
  }
  
  if (day.workout) {
    classes.push('hover:border-green-400 dark:hover:border-green-600');
  }
  
  return classes.join(' ');
}

function getIntensityColor(intensity: string): string {
  const colors = {
    light: 'bg-green-500',
    moderate: 'bg-yellow-500',
    intense: 'bg-red-500',
  };
  return colors[intensity as keyof typeof colors] || 'bg-gray-500';
}

function getIntensityLabel(intensity: string): string {
  const labels = {
    light: 'Ligero',
    moderate: 'Moderado',
    intense: 'Intenso',
  };
  return labels[intensity as keyof typeof labels] || intensity;
}

function selectDay(day: CalendarDay) {
  if (day.workout) {
    selectedDay.value = day;
    showDetailModal.value = true;
  } else if (day.isCurrentMonth) {
    // Redirect to create workout with selected date
    window.location.href = `/workouts/create?date=${day.date}`;
  }
}

function previousMonth() {
  if (currentMonth.value === 1) {
    currentMonth.value = 12;
    currentYear.value--;
  } else {
    currentMonth.value--;
  }
  emit('monthChange', { month: currentMonth.value, year: currentYear.value });
}

function nextMonth() {
  if (currentMonth.value === 12) {
    currentMonth.value = 1;
    currentYear.value++;
  } else {
    currentMonth.value++;
  }
  emit('monthChange', { month: currentMonth.value, year: currentYear.value });
}

watch(() => [props.currentMonth, props.currentYear], ([month, year]) => {
  currentMonth.value = month;
  currentYear.value = year;
});
</script>
