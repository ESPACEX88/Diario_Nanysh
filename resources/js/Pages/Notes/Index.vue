<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    notes: {
        data: any[];
        links: any;
        meta: any;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Notas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📝</span>
                    <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                        Mis Notas
                    </h2>
                </div>
                <Link
                    :href="route('notes.create')"
                    class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    <span class="mr-2">✨</span>
                    Nueva Nota
                </Link>
            </div>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Centro de ideas</p>
                        <p class="mt-1 text-sm text-rose-700">Anota pensamientos, listas o recordatorios con estilo.</p>
                    </div>
                    <span class="rounded-full border border-rose-200/80 bg-white/80 px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm">
                        {{ notes.meta?.total ?? notes.data.length }} notas
                    </span>
                </section>

                <div v-if="notes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="note in notes.data"
                        :key="note.id"
                        :href="route('notes.show', note.id)"
                        class="group feminine-surface relative overflow-hidden rounded-[1.5rem] border border-rose-100/80 p-6 shadow-[0_14px_36px_rgba(236,72,153,0.11)] transition-all duration-300 hover:-translate-y-1 hover:border-rose-300 hover:shadow-[0_20px_48px_rgba(236,72,153,0.16)]"
                        :style="{ boxShadow: `inset 4px 0 0 ${note.color || '#fbbf24'}` }"
                    >
                        <div class="absolute -right-10 -top-10 h-24 w-24 rounded-full bg-gradient-to-br from-rose-200/35 to-fuchsia-200/30 blur-sm transition-transform duration-500 group-hover:scale-125"></div>
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ note.title }}
                            </h3>
                            <span
                                v-if="note.is_pinned"
                                class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700"
                                title="Fijada"
                            >
                                📌
                            </span>
                        </div>
                        <p
                            v-if="note.content"
                            class="text-gray-600 dark:text-gray-300 line-clamp-3 text-sm"
                        >
                            {{ note.content }}
                        </p>
                    </Link>
                </div>
                <div
                    v-else
                    class="feminine-panel py-12 text-center"
                >
                    <span class="text-6xl mb-4 block">📝</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay notas aún
                    </h3>
                    <Link
                        :href="route('notes.create')"
                        class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                    >
                        Crear Primera Nota
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

