<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';

interface Props {
  value: number;
  duration?: number;
  prefix?: string;
  suffix?: string;
  decimals?: number;
  startFromZero?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 2000,
  prefix: '',
  suffix: '',
  decimals: 0,
  startFromZero: true,
});

const currentValue = ref(0);
const isVisible = ref(false);

const formattedValue = computed(() => {
  const fixed = currentValue.value.toFixed(props.decimals);
  return `${props.prefix}${fixed}${props.suffix}`;
});

const animate = () => {
  if (!props.startFromZero) {
    currentValue.value = props.value;
    isVisible.value = true;
    return;
  }

  const startTime = performance.now();
  const startValue = 0;
  const endValue = props.value;

  const easeOutQuart = (t: number): number => {
    return 1 - Math.pow(1 - t, 4);
  };

  const update = (currentTime: number) => {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / props.duration, 1);
    const easedProgress = easeOutQuart(progress);

    currentValue.value = startValue + (endValue - startValue) * easedProgress;

    if (progress < 1) {
      requestAnimationFrame(update);
    } else {
      currentValue.value = endValue;
      isVisible.value = true;
    }
  };

  requestAnimationFrame(update);
};

onMounted(() => {
  animate();
});

watch(() => props.value, () => {
  animate();
});
</script>

<template>
  <span 
    class="inline-block font-semibold text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-purple-600"
    :class="{ 'opacity-0': !isVisible, 'opacity-100 transition-opacity duration-300': isVisible }"
  >
    {{ formattedValue }}
  </span>
</template>
