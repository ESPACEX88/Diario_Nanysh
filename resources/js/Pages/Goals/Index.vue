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
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mis Metas
                </h2>
                <Link
                    :href="route('goals.create')"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700"
                >
                    Nueva Meta
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="goals.data.length > 0" class="space-y-4">
                    <Link
                        v-for="goal in goals.data"
                        :key="goal.id"
                        :href="route('goals.show', goal.id)"
                        class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ goal.title }}
                                </h3>
                                <p
                                    v-if="goal.description"
                                    class="text-gray-600 dark:text-gray-300 mt-2"
                                >
                                    {{ goal.description }}
                                </p>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div
                                            class="bg-purple-600 h-2.5 rounded-full"
                                            :style="{ width: goal.progress_percentage + '%' }"
                                        />
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ goal.progress_percentage }}% completado
                                    </p>
                                </div>
                            </div>
                            <span
                                v-if="goal.is_completed"
                                class="text-green-500 text-2xl"
                            >
                                ✅
                            </span>
                        </div>
                    </Link>
                </div>
                <div
                    v-else
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center py-12"
                >
                    <span class="text-6xl mb-4 block">🎯</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay metas aún
                    </h3>
                    <Link
                        :href="route('goals.create')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700"
                    >
                        Crear Primera Meta
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

