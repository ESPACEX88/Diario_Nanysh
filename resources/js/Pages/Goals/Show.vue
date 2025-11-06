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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ goal.title }}
                </h2>
                <Link
                    :href="route('goals.edit', goal.id)"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div
                        v-if="goal.description"
                        class="mb-6"
                    >
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ goal.description }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div
                                class="bg-purple-600 h-4 rounded-full"
                                :style="{ width: goal.progress_percentage + '%' }"
                            />
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ goal.progress_percentage }}% completado
                        </p>
                    </div>
                    <div
                        v-if="goal.target_date"
                        class="text-sm text-gray-500"
                    >
                        Fecha objetivo: {{ new Date(goal.target_date).toLocaleDateString('es-ES') }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

