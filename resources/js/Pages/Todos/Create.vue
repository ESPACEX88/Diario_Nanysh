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
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-2xl bg-white border-2 border-pink-200 shadow-xl">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-20 -mt-20"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <!-- Primera fila: Título -->
                            <div class="mb-6">
                                <InputLabel for="title" value="Título *" class="text-pink-800 font-semibold mb-2" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    placeholder="¿Qué necesitas hacer?"
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Segunda fila: Descripción -->
                            <div class="mb-6">
                                <InputLabel for="description" value="Descripción" class="text-pink-800 font-semibold mb-2" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-3 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 resize-none"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Detalles adicionales sobre la tarea..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Tercera fila: Fecha límite y Prioridad -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <InputLabel for="due_date" value="Fecha límite" class="text-pink-800 font-semibold mb-2" />
                                    <TextInput
                                        id="due_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                        v-model="form.due_date"
                                    />
                                    <InputError class="mt-2" :message="form.errors.due_date" />
                                </div>

                                <div>
                                    <InputLabel for="priority" value="Prioridad" class="text-pink-800 font-semibold mb-2" />
                                    <select
                                        id="priority"
                                        class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                        v-model="form.priority"
                                    >
                                        <option value="low">🟢 Baja</option>
                                        <option value="medium">🟡 Media</option>
                                        <option value="high">🔴 Alta</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.priority" />
                                </div>
                            </div>

                            <!-- Cuarta fila: Categoría -->
                            <div class="mb-6">
                                <InputLabel for="category" value="Categoría" class="text-pink-800 font-semibold mb-2" />
                                <TextInput
                                    id="category"
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                    v-model="form.category"
                                    placeholder="Ej: Trabajo, Personal, Salud..."
                                />
                                <InputError class="mt-2" :message="form.errors.category" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end gap-4 pt-4 border-t-2 border-pink-100">
                                <Link
                                    :href="route('todos.index')"
                                    class="px-6 py-2.5 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-lg font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Creando...</span>
                                    <span v-else>✨ Crear Tarea</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

