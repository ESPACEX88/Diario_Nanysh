<template>
  <div class="w-full rounded-[2rem] border border-rose-100/80 bg-white/85 p-4 shadow-[0_20px_60px_rgba(236,72,153,0.08)] backdrop-blur-xl sm:p-6">
    <div class="mb-6 flex items-center justify-between">
      <button
        @click="previousMonth"
        class="rounded-full border border-rose-200 bg-white/90 p-2 text-rose-600 transition hover:-translate-y-px hover:bg-rose-50"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <h2 class="text-xl font-bold text-rose-950">
        {{ currentMonthName }} {{ currentYear }}
      </h2>
      
      <button
        @click="nextMonth"
        class="rounded-full border border-rose-200 bg-white/90 p-2 text-rose-600 transition hover:-translate-y-px hover:bg-rose-50"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Days of the week -->
    <div class="mb-2 grid grid-cols-7 gap-2">
      <div
        v-for="day in daysOfWeek"
        :key="day"
        class="py-2 text-center text-sm font-semibold text-rose-800/70"
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
          'aspect-square cursor-pointer rounded-2xl border transition-all duration-200',
          getDayClasses(day)
        ]"
        @click="selectDay(day)"
      >
        <div class="flex h-full flex-col items-center justify-center p-2">
          <span
            :class="[
              'mb-1 text-sm font-semibold',
              day.isCurrentMonth ? 'text-rose-950' : 'text-rose-300'
            ]"
          >
            {{ day.dayNumber }}
          </span>
          
          <!-- Indicator for workout day -->
          <div v-if="day.workout" class="flex flex-col items-center gap-1 w-full">
            <div
              :class="[
                'h-2 w-2 rounded-full',
                getIntensityColor(day.workout.intensity)
              ]"
            />
            <div v-if="day.workout.duration_minutes" class="text-xs text-rose-700/70">
              {{ day.workout.duration_minutes }}m
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 text-sm">
      <div class="flex items-center gap-2">
        <div class="h-3 w-3 rounded-full bg-emerald-400"></div>
        <span class="text-rose-800/70">Ligero</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="h-3 w-3 rounded-full bg-amber-400"></div>
        <span class="text-rose-800/70">Moderado</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="h-3 w-3 rounded-full bg-rose-500"></div>
        <span class="text-rose-800/70">Intenso</span>
      </div>
    </div>

    <!-- Day detail modal -->
    <Modal :show="showDetailModal" @close="showDetailModal = false">
      <div v-if="selectedDay?.workout" class="p-6">
        <h3 class="mb-4 text-lg font-bold text-rose-950">
          {{ selectedDay.workout.routine_name }}
        </h3>
        
        <div class="space-y-3">
          <div>
            <span class="text-sm font-semibold text-rose-800/70">Fecha:</span>
            <p class="text-rose-950">{{ formatDate(selectedDay.date) }}</p>
          </div>
          
          <div v-if="selectedDay.workout.duration_minutes">
            <span class="text-sm font-semibold text-rose-800/70">Duración:</span>
            <p class="text-rose-950">{{ selectedDay.workout.duration_minutes }} minutos</p>
          </div>
          
          <div>
            <span class="text-sm font-semibold text-rose-800/70">Intensidad:</span>
            <p class="capitalize text-rose-950">{{ getIntensityLabel(selectedDay.workout.intensity) }}</p>
          </div>
          
          <div v-if="selectedDay.workout.exercises && selectedDay.workout.exercises.length > 0">
            <span class="text-sm font-semibold text-rose-800/70">Ejercicios:</span>
            <ul class="mt-2 space-y-2">
              <li
                v-for="(exercise, index) in selectedDay.workout.exercises"
                :key="index"
                class="rounded-2xl border border-rose-100 bg-rose-50/70 p-3"
              >
                <p class="font-semibold text-rose-950">{{ exercise.name }}</p>
                <p class="text-sm text-rose-800/70">
                  <span v-if="exercise.sets">{{ exercise.sets }} series</span>
                  <span v-if="exercise.reps"> × {{ exercise.reps }} reps</span>
                  <span v-if="exercise.weight"> - {{ exercise.weight }}</span>
                </p>
              </li>
            </ul>
          </div>
          
          <div v-if="selectedDay.workout.notes">
            <span class="text-sm font-semibold text-rose-800/70">Notas:</span>
            <p class="mt-1 text-rose-900/80">{{ selectedDay.workout.notes }}</p>
          </div>
        </div>

        <div class="mt-6 flex gap-3">
          <a
            :href="`/workouts/${selectedDay.workout.id}/edit`"
            class="flex-1 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-4 py-2 text-center font-semibold text-white transition hover:-translate-y-px"
          >
            Editar
          </a>
          <button
            @click="showDetailModal = false"
            class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700 transition hover:bg-rose-200"
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
    classes.push('border-rose-400 ring-2 ring-rose-400');
  } else if (day.workout) {
    classes.push('border-rose-200 bg-rose-50/80');
  } else {
    classes.push('border-rose-100 bg-white/85');
  }
  
  if (day.isCurrentMonth && !day.workout) {
    classes.push('hover:bg-rose-50');
  }
  
  if (day.workout) {
    classes.push('hover:border-rose-300');
  }
  
  return classes.join(' ');
}

function getIntensityColor(intensity: string): string {
  const colors = {
    light: 'bg-emerald-400',
    moderate: 'bg-amber-400',
    intense: 'bg-rose-500',
  };
  return colors[intensity as keyof typeof colors] || 'bg-rose-400';
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
