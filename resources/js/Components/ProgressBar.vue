<script setup lang="ts">
import { ref, computed } from 'vue';

interface ProgressBarProps {
  progress: number;
  color?: 'rose' | 'purple' | 'blue' | 'green' | 'orange' | 'amber' | 'emerald' | 'fuchsia';
  size?: 'sm' | 'md' | 'lg';
  showLabel?: boolean;
  animated?: boolean;
  striped?: boolean;
  showValue?: boolean;
}

const props = withDefaults(defineProps<ProgressBarProps>(), {
  color: 'rose',
  size: 'md',
  showLabel: true,
  animated: true,
  striped: false,
});

const colorClasses: Record<string, string> = {
  rose: 'from-rose-400 to-pink-500',
  purple: 'from-purple-400 to-violet-500',
  blue: 'from-blue-400 to-cyan-500',
  green: 'from-green-400 to-emerald-500',
  orange: 'from-orange-400 to-amber-500',
  amber: 'from-amber-400 to-orange-500',
  emerald: 'from-emerald-400 to-teal-500',
  fuchsia: 'from-fuchsia-400 to-purple-500',
};

const sizeClasses = {
  sm: 'h-2 text-xs',
  md: 'h-3 text-sm',
  lg: 'h-4 text-base',
};

const clampedProgress = computed(() => {
  return Math.min(Math.max(props.progress, 0), 100);
});
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <div v-if="showLabel" class="mb-2 flex justify-between items-center">
      <slot name="label">
        <span class="text-sm font-medium text-gray-700">Progreso</span>
      </slot>
      <span class="text-sm font-semibold text-gray-600">
        {{ clampedProgress }}%
      </span>
    </div>

    <!-- Progress bar container -->
    <div 
      class="relative overflow-hidden rounded-full bg-gray-200/80 shadow-inner"
      :class="sizeClasses[size]"
    >
      <!-- Progress fill -->
      <div
        class="flex items-center justify-center text-white transition-all duration-1000 ease-out"
        :class="[
          `bg-gradient-to-r ${colorClasses[color]}`,
          sizeClasses[size],
          animated ? 'animate-pulse-slow' : '',
        ]"
        :style="{ width: `${clampedProgress}%` }"
      >
        <!-- Striped pattern -->
        <div 
          v-if="striped"
          class="absolute inset-0 opacity-20"
          style="background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent); background-size: 1rem 1rem;"
        ></div>
      </div>

      <!-- Shine effect -->
      <div 
        class="pointer-events-none absolute inset-0 -translate-x-full animate-shine"
        style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);"
      ></div>
    </div>
  </div>
</template>

<style scoped>
@keyframes shine {
  100% {
    transform: translateX(100%);
  }
}

.animate-shine {
  animation: shine 2s infinite;
}

.animate-pulse-slow {
  animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .8;
  }
}
</style>
