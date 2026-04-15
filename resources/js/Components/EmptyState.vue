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
        class="relative overflow-hidden rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 px-6 py-16 text-center shadow-[0_20px_60px_rgba(236,72,153,0.1)]"
    >
        <div
            class="absolute top-0 right-0 h-64 w-64 rounded-full bg-gradient-to-br from-rose-200/40 to-fuchsia-200/40 -mr-32 -mt-32"
        ></div>
        <div class="relative mx-auto max-w-xl">
            <span
                v-if="icon"
                class="mb-6 block text-7xl transition-transform duration-300 hover:scale-110"
            >
                {{ icon }}
            </span>
            <h3 class="mb-3 text-3xl font-bold text-rose-950">
                {{ title }}
            </h3>
            <p
                v-if="description"
                class="mx-auto mb-8 max-w-md text-lg leading-8 text-rose-900/70"
            >
                {{ description }}
            </p>
            <Link
                v-if="actionRoute"
                :href="route(actionRoute)"
                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-8 py-4 text-base font-bold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
            >
                <span aria-hidden="true">💕</span>
                {{ actionLabel || 'Comenzar' }}
            </Link>
            <a
                v-else-if="actionHref"
                :href="actionHref"
                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-8 py-4 text-base font-bold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
            >
                <span aria-hidden="true">💕</span>
                {{ actionLabel || 'Comenzar' }}
            </a>
        </div>
    </div>
</template>

