<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    goals: {
        data: any[];
        links: any;
        meta: any;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Metas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎯</span>
                    <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                        Mis Metas
                    </h2>
                </div>
                <Link
                    :href="route('goals.create')"
                    class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    <span class="mr-2">✨</span>
                    Nueva Meta
                </Link>
            </div>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Dirección personal</p>
                        <p class="mt-1 text-sm text-rose-700">Define objetivos con fecha, mide tu progreso y celebra avances.</p>
                    </div>
                    <span class="rounded-full border border-rose-200/80 bg-white/80 px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm">
                        {{ goals.meta?.total ?? goals.data.length }} metas
                    </span>
                </section>

                <div v-if="goals.data.length > 0" class="space-y-4">
                    <Link
                        v-for="goal in goals.data"
                        :key="goal.id"
                        :href="route('goals.show', goal.id)"
                        class="group block feminine-surface relative overflow-hidden rounded-[1.5rem] border border-rose-100/80 p-6 shadow-[0_14px_36px_rgba(236,72,153,0.11)] transition-all duration-300 hover:-translate-y-1 hover:border-rose-300 hover:shadow-[0_20px_48px_rgba(236,72,153,0.16)]"
                    >
                        <div class="absolute -right-10 -top-10 h-24 w-24 rounded-full bg-gradient-to-br from-rose-200/35 to-fuchsia-200/30 blur-sm transition-transform duration-500 group-hover:scale-125"></div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ goal.title }}
                                </h3>
                                <p
                                    v-if="goal.description"
                                    class="mt-2 text-gray-600"
                                >
                                    {{ goal.description }}
                                </p>
                                <div class="mt-4">
                                    <div class="h-2.5 w-full rounded-full bg-rose-100">
                                        <div
                                            class="h-2.5 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500"
                                            :style="{ width: goal.progress_percentage + '%' }"
                                        />
                                    </div>
                                    <p class="mt-1 text-sm text-rose-700">
                                        {{ goal.progress_percentage }}% completado
                                    </p>
                                </div>
                            </div>
                            <span
                                v-if="goal.is_completed"
                                class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xl"
                            >
                                ✅
                            </span>
                        </div>
                    </Link>
                </div>
                <div
                    v-else
                    class="feminine-panel py-12 text-center"
                >
                    <span class="text-6xl mb-4 block">🎯</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay metas aún
                    </h3>
                    <Link
                        :href="route('goals.create')"
                        class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                    >
                        Crear Primera Meta
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

