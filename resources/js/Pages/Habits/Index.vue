<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    habits: {
        data: any[];
        links: any;
        meta: any;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Hábitos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mis Hábitos
                </h2>
                <Link
                    :href="route('habits.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    Nuevo Hábito
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="habits.data.length > 0" class="space-y-4">
                    <Link
                        v-for="habit in habits.data"
                        :key="habit.id"
                        :href="route('habits.show', habit.id)"
                        class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl">{{ habit.icon || '🔄' }}</span>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ habit.name }}
                                    </h3>
                                    <p
                                        v-if="habit.description"
                                        class="text-gray-600 dark:text-gray-300 text-sm"
                                    >
                                        {{ habit.description }}
                                    </p>
                                </div>
                            </div>
                            <span
                                v-if="!habit.is_active"
                                class="text-gray-400 text-sm"
                            >
                                Inactivo
                            </span>
                        </div>
                    </Link>
                </div>
                <div
                    v-else
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center py-12"
                >
                    <span class="text-6xl mb-4 block">🔄</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay hábitos aún
                    </h3>
                    <Link
                        :href="route('habits.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        Crear Primer Hábito
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

