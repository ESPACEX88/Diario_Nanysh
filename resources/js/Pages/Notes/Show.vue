<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Props {
    note: any;
}

const props = defineProps<Props>();

const deleteNote = () => {
    if (confirm('¿Eliminar esta nota?')) {
        router.delete(route('notes.destroy', props.note.id));
    }
};
</script>

<template>
    <Head :title="note.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                    {{ note.title }}
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('notes.edit', note.id)"
                        class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                    >
                        Editar
                    </Link>
                    <button
                        @click="deleteNote"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition-all hover:-translate-y-0.5 hover:bg-red-100"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Nota destacada</p>
                        <p class="mt-1 text-sm text-rose-700">Guarda ideas con claridad y vuelve a ellas cuando las necesites.</p>
                    </div>
                    <span
                        class="rounded-full border border-rose-200/80 bg-white/80 px-3 py-1 text-xs font-semibold text-rose-700 shadow-sm"
                        :style="{ borderColor: note.color || '#fbbf24' }"
                    >
                        Color personalizado
                    </span>
                </section>

                <div
                    class="feminine-panel overflow-hidden p-6"
                    :style="{ borderLeft: `4px solid ${note.color || '#fbbf24'}` }"
                >
                    <div
                        v-if="note.content"
                        class="prose max-w-none"
                    >
                        <p
                            class="whitespace-pre-wrap text-gray-700"
                            v-html="note.content.replace(/\n/g, '<br>')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

