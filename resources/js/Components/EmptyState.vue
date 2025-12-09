<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    icon?: string;
    title: string;
    description?: string;
    actionLabel?: string;
    actionRoute?: string;
    actionHref?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-100 via-purple-50 to-rose-50 shadow-xl text-center py-16 border-2 border-pink-200"
    >
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-300/30 to-purple-300/30 rounded-full -mr-32 -mt-32"
        ></div>
        <div class="relative p-6">
            <span
                v-if="icon"
                class="text-7xl mb-6 block transform hover:scale-110 transition-transform inline-block"
            >
                {{ icon }}
            </span>
            <h3 class="text-3xl font-bold text-gray-800 mb-3">
                {{ title }}
            </h3>
            <p
                v-if="description"
                class="text-lg text-gray-600 mb-8 max-w-md mx-auto"
            >
                {{ description }}
            </p>
            <Link
                v-if="actionRoute"
                :href="route(actionRoute)"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-base shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-1 transition-all"
            >
                <span class="mr-2">💕</span>
                {{ actionLabel || 'Comenzar' }}
            </Link>
            <a
                v-else-if="actionHref"
                :href="actionHref"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-base shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-1 transition-all"
            >
                <span class="mr-2">💕</span>
                {{ actionLabel || 'Comenzar' }}
            </a>
        </div>
    </div>
</template>

