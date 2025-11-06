<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    date: new Date().toISOString().split('T')[0],
    item_one: '',
    item_two: '',
    item_three: '',
    reflection: '',
});

const submit = () => {
    form.post(route('gratitude.store'));
};
</script>

<template>
    <Head title="Nueva Gratitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Nueva Gratitud
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <label
                                for="date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Fecha
                            </label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="item_one"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Por qué estoy agradecido #1
                            </label>
                            <input
                                id="item_one"
                                v-model="form.item_one"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="item_two"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Por qué estoy agradecido #2
                            </label>
                            <input
                                id="item_two"
                                v-model="form.item_two"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="item_three"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Por qué estoy agradecido #3
                            </label>
                            <input
                                id="item_three"
                                v-model="form.item_three"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label
                                for="reflection"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Reflexión (opcional)
                            </label>
                            <textarea
                                id="reflection"
                                v-model="form.reflection"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('gratitude.index')"
                                class="text-gray-600 hover:text-gray-800"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 disabled:opacity-50"
                            >
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

