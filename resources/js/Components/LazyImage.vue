<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface Props {
    src: string;
    alt: string;
    thumbnail?: string;
    class?: string;
    loading?: 'lazy' | 'eager';
}

const props = withDefaults(defineProps<Props>(), {
    loading: 'lazy',
});

const imageRef = ref<HTMLImageElement | null>(null);
const isLoaded = ref(false);
const isInView = ref(false);
const observer = ref<IntersectionObserver | null>(null);

onMounted(() => {
    if (props.loading === 'lazy' && 'IntersectionObserver' in window) {
        observer.value = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        isInView.value = true;
                        observer.value?.disconnect();
                    }
                });
            },
            {
                rootMargin: '50px',
            }
        );

        if (imageRef.value) {
            observer.value.observe(imageRef.value);
        }
    } else {
        // Si no hay soporte para IntersectionObserver o es eager, cargar inmediatamente
        isInView.value = true;
    }
});

onUnmounted(() => {
    if (observer.value) {
        observer.value.disconnect();
    }
});

const handleLoad = () => {
    isLoaded.value = true;
};

const handleError = () => {
    // Fallback si la imagen no carga
    if (imageRef.value) {
        imageRef.value.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect fill="%23f3f4f6" width="300" height="300"/%3E%3Ctext fill="%239ca3af" font-family="sans-serif" font-size="18" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImagen no disponible%3C/text%3E%3C/svg%3E';
    }
};
</script>

<template>
    <div
        ref="imageRef"
        :class="['relative overflow-hidden', props.class]"
        role="img"
        :aria-label="alt"
    >
        <!-- Thumbnail placeholder mientras carga -->
        <img
            v-if="props.thumbnail && !isLoaded"
            :src="props.thumbnail"
            :alt="alt"
            class="absolute inset-0 w-full h-full object-cover blur-sm"
            aria-hidden="true"
        />
        
        <!-- Imagen principal -->
        <img
            v-if="isInView"
            :src="src"
            :alt="alt"
            :class="[
                'transition-opacity duration-300',
                isLoaded ? 'opacity-100' : 'opacity-0',
                props.class
            ]"
            @load="handleLoad"
            @error="handleError"
            loading="lazy"
        />
        
        <!-- Placeholder mientras no está en vista -->
        <div
            v-else
            class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center"
            aria-hidden="true"
        >
            <div class="animate-pulse text-gray-400 text-sm">Cargando...</div>
        </div>
    </div>
</template>

