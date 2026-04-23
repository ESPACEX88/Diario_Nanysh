<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    goal: any;
}

defineProps<Props>();
</script>

<template>
    <Head :title="goal.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                    {{ goal.title }}
                </h2>
                <Link
                    :href="route('goals.edit', goal.id)"
                    class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="feminine-panel space-y-5 p-6 sm:p-8">
                    <div
                        v-if="goal.description"
                        class="rounded-2xl border border-rose-100/80 bg-white/70 p-4"
                    >
                        <p class="text-gray-700">
                            {{ goal.description }}
                        </p>
                    </div>
                    <div>
                        <div class="h-4 w-full rounded-full bg-rose-100">
                            <div
                                class="h-4 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500"
                                :style="{ width: goal.progress_percentage + '%' }"
                            />
                        </div>
                        <p class="mt-2 text-sm font-semibold text-rose-700">
                            {{ goal.progress_percentage }}% completado
                        </p>
                    </div>
                    <div
                        v-if="goal.target_date"
                        class="inline-flex items-center rounded-full border border-rose-200 bg-white/80 px-3 py-1 text-sm font-medium text-rose-700"
                    >
                        Fecha objetivo: {{ new Date(goal.target_date).toLocaleDateString('es-ES') }}
                    </div>
                    <div v-if="goal.is_completed" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                        ✅ Meta completada
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

