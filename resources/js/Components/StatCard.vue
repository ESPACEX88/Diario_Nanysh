<script setup lang="ts">
import { ref, computed } from 'vue';

interface StatCardProps {
  title: string;
  value: number | string;
  icon?: string;
  trend?: number;
  trendLabel?: string;
  color?: 'rose' | 'purple' | 'blue' | 'green' | 'orange' | 'amber' | 'emerald' | 'fuchsia';
  suffix?: string;
  prefix?: string;
  animated?: boolean;
  href?: string;
}

const props = withDefaults(defineProps<StatCardProps>(), {
  icon: '📊',
  trend: 0,
  color: 'rose',
  animated: true,
});

const colorClasses: Record<string, string> = {
  rose: 'from-rose-500 to-pink-600 shadow-rose-200',
  purple: 'from-purple-500 to-violet-600 shadow-purple-200',
  blue: 'from-blue-500 to-cyan-600 shadow-blue-200',
  green: 'from-green-500 to-emerald-600 shadow-green-200',
  orange: 'from-orange-500 to-amber-600 shadow-orange-200',
  amber: 'from-amber-500 to-orange-600 shadow-amber-200',
  emerald: 'from-emerald-500 to-teal-600 shadow-emerald-200',
  fuchsia: 'from-fuchsia-500 to-purple-600 shadow-fuchsia-200',
};

const trendColor = computed(() => {
  if (props.trend > 0) return 'text-green-600 bg-green-50';
  if (props.trend < 0) return 'text-red-600 bg-red-50';
  return 'text-gray-600 bg-gray-50';
});

const formattedValue = computed(() => {
  if (typeof props.value === 'number') {
    const num = props.value.toFixed(0);
    return `${props.prefix}${num}${props.suffix}`;
  }
  return `${props.prefix}${props.value}${props.suffix}`;
});
</script>

<template>
  <div 
    class="group relative overflow-hidden rounded-3xl border border-white/70 bg-white/80 p-6 shadow-lg backdrop-blur-xl transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] hover:border-white/90"
  >
    <!-- Background gradient -->
    <div 
      class="absolute -right-10 -top-10 h-32 w-32 rounded-full opacity-20 blur-2xl transition-opacity group-hover:opacity-30"
      :class="`bg-gradient-to-br ${colorClasses[color]}`"
    ></div>

    <!-- Content -->
    <div class="relative z-10">
      <!-- Header -->
      <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span 
            class="flex h-12 w-12 items-center justify-center rounded-2xl text-2xl shadow-md"
            :class="`bg-gradient-to-br ${colorClasses[color]} text-white`"
          >
            {{ icon }}
          </span>
          <h3 class="text-sm font-medium text-gray-600">{{ title }}</h3>
        </div>
        
        <!-- Trend indicator -->
        <div 
          v-if="trend !== 0"
          class="flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
          :class="trendColor"
        >
          <span>{{ trend > 0 ? '↑' : '↓' }}</span>
          <span>{{ Math.abs(trend) }}%</span>
        </div>
      </div>

      <!-- Value -->
      <div class="flex items-baseline gap-2">
        <span 
          class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-gray-800 to-gray-600"
        >
          {{ formattedValue }}
        </span>
        <span v-if="trendLabel" class="text-sm text-gray-500">
          {{ trendLabel }}
        </span>
      </div>

      <!-- Decorative line -->
      <div 
        class="mt-4 h-1 w-20 rounded-full bg-gradient-to-r opacity-60"
        :class="colorClasses[color]"
      ></div>
    </div>
  </div>
</template>
