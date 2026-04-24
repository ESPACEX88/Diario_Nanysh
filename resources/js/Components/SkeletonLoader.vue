<script setup lang="ts">
interface SkeletonProps {
  variant?: 'text' | 'circular' | 'rectangular' | 'rounded' | 'card';
  width?: string;
  height?: string;
  animated?: boolean;
}

withDefaults(defineProps<SkeletonProps>(), {
  variant: 'text',
  animated: true,
});
</script>

<template>
  <div
    class="relative overflow-hidden bg-gray-200/80"
    :class="[
      {
        'rounded-full': variant === 'circular' || variant === 'rounded',
        'rounded-lg': variant === 'rectangular' || variant === 'card',
        'h-4 w-full': variant === 'text' && !height,
        'p-4 space-y-3': variant === 'card',
      }
    ]"
    :style="{
      width: width || (variant === 'circular' ? '48px' : undefined),
      height: height || (variant === 'circular' ? '48px' : undefined),
    }"
  >
    <!-- Card variant inner skeletons -->
    <template v-if="variant === 'card'">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gray-300/80"></div>
        <div class="flex-1 space-y-2">
          <div class="h-3 bg-gray-300/80 rounded w-3/4"></div>
          <div class="h-2 bg-gray-300/80 rounded w-1/2"></div>
        </div>
      </div>
      <div class="h-20 bg-gray-300/80 rounded"></div>
    </template>
    
    <!-- Shimmer effect -->
    <div
      v-if="animated"
      class="absolute inset-0 -translate-x-full animate-shimmer"
      style="background: linear-gradient(to right, transparent, rgba(255,255,255,0.6), transparent);"
    ></div>
  </div>
</template>

<style scoped>
@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 1.5s infinite;
}
</style>
