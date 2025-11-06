<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    goal: any;
}

const props = defineProps<Props>();

const form = useForm({
    title: props.goal.title,
    description: props.goal.description || '',
    target_date: props.goal.target_date || '',
    progress_percentage: props.goal.progress_percentage || 0,
    is_completed: props.goal.is_completed || false,
});

const submit = () => {
    form.put(route('goals.update', props.goal.id));
};
</script>

<template>
    <Head title="Editar Meta" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Editar Meta
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Título
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Descripción
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="target_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Fecha Objetivo
                            </label>
                            <input
                                id="target_date"
                                v-model="form.target_date"
                                type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="progress_percentage"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Progreso (%)
                            </label>
                            <input
                                id="progress_percentage"
                                v-model.number="form.progress_percentage"
                                type="number"
                                min="0"
                                max="100"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex items-center">
                            <input
                                id="is_completed"
                                v-model="form.is_completed"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <label
                                for="is_completed"
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                Completada
                            </label>
                        </div>
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('goals.show', goal.id)"
                                class="text-gray-600 hover:text-gray-800"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50"
                            >
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

