<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    priority: 'medium' as 'low' | 'medium' | 'high',
    category: '',
});

const submit = () => {
    form.post(route('todos.store'));
};
</script>

<template>
    <Head title="Nueva Tarea" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                ✨ Nueva Tarea
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-8">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="title" value="Título *" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="description" value="Descripción" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.description"
                                rows="4"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <InputLabel for="due_date" value="Fecha límite" />
                                <TextInput
                                    id="due_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.due_date"
                                />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>

                            <div>
                                <InputLabel for="priority" value="Prioridad" />
                                <select
                                    id="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.priority"
                                >
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.priority" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <InputLabel for="category" value="Categoría" />
                            <TextInput
                                id="category"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.category"
                                placeholder="Ej: Trabajo, Personal, Salud..."
                            />
                            <InputError class="mt-2" :message="form.errors.category" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Crear Tarea
                            </PrimaryButton>
                            <Link
                                :href="route('todos.index')"
                                class="text-gray-600 hover:text-gray-900 font-semibold"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

