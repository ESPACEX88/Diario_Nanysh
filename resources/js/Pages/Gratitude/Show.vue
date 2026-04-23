<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    gratitude: any;
}

defineProps<Props>();
</script>

<template>
    <Head title="Gratitud" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                    Gratitud del {{ new Date(gratitude.date).toLocaleDateString('es-ES') }}
                </h2>
                <Link
                    :href="route('gratitude.edit', gratitude.id)"
                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <section class="feminine-panel flex items-center justify-between gap-3 p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Practica diaria</p>
                        <p class="mt-1 text-sm text-rose-700">Reconocer lo bueno de hoy fortalece tu bienestar emocional.</p>
                    </div>
                    <span class="text-2xl">🙏</span>
                </section>

                <div class="feminine-panel p-6">
                    <div class="mb-6 space-y-4">
                        <div class="flex items-start gap-3 rounded-xl border border-rose-100/80 bg-white/80 p-4">
                            <span class="text-2xl">✨</span>
                            <p class="text-lg text-gray-700">
                                {{ gratitude.item_one }}
                            </p>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl border border-rose-100/80 bg-white/80 p-4">
                            <span class="text-2xl">💫</span>
                            <p class="text-lg text-gray-700">
                                {{ gratitude.item_two }}
                            </p>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl border border-rose-100/80 bg-white/80 p-4">
                            <span class="text-2xl">🌸</span>
                            <p class="text-lg text-gray-700">
                                {{ gratitude.item_three }}
                            </p>
                        </div>
                    </div>
                    <div
                        v-if="gratitude.reflection"
                        class="mt-6 border-t border-rose-100 pt-6"
                    >
                        <h3 class="mb-2 font-semibold text-gray-900">
                            Reflexión
                        </h3>
                        <p class="text-gray-700">
                            {{ gratitude.reflection }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

