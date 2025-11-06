<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    description: '',
    start_date: '',
    icon: '💕',
    color: '#EC4899',
});

const submit = () => {
    form.post(route('counters.store'));
};
</script>

<template>
    <Head title="Nuevo Contador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>✨</span>
                Crear Contador de Días
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <InputLabel for="name" value="Nombre del contador *" class="text-pink-700 font-semibold" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    placeholder="Ej: Días juntos, Días sin fumar..."
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="description" value="Descripción" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="description"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Una pequeña descripción..."
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="start_date" value="Fecha de inicio *" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="start_date"
                                        type="date"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.start_date"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.start_date" />
                                </div>

                                <div>
                                    <InputLabel for="icon" value="Icono (emoji)" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="icon"
                                        type="text"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl text-center text-2xl"
                                        v-model="form.icon"
                                        maxlength="2"
                                        placeholder="💕"
                                    />
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="color" value="Color" class="text-pink-700 font-semibold" />
                                <div class="flex items-center gap-4 mt-2">
                                    <input
                                        id="color"
                                        type="color"
                                        v-model="form.color"
                                        class="w-20 h-12 rounded-xl border-2 border-pink-300"
                                    />
                                    <span class="text-sm text-gray-600">Elige un color bonito</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">💕</span>
                                    Crear Contador
                                </PrimaryButton>
                                <Link
                                    :href="route('counters.index')"
                                    class="text-gray-600 hover:text-pink-600 font-semibold transition-colors"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

