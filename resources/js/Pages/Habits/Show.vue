<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    habit: any;
}

defineProps<Props>();
</script>

<template>
    <Head :title="habit.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ habit.name }}
                </h2>
                <Link
                    :href="route('habits.edit', habit.id)"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-5xl">{{ habit.icon || '🔄' }}</span>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ habit.name }}
                            </h3>
                            <p
                                v-if="habit.description"
                                class="text-gray-600 dark:text-gray-300"
                            >
                                {{ habit.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

