<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    location: '',
    color: '#EC4899',
    is_recurring: false,
    recurrence_pattern: '',
    send_reminder: false,
    reminder_minutes: null,
    reminder_email: '',
});

const submit = () => {
    form.post(route('events.store'));
};
</script>

<template>
    <Head title="Nuevo Evento" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                ✨ Nuevo Evento
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
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <InputLabel for="start_date" value="Fecha y hora de inicio *" />
                                <TextInput
                                    id="start_date"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    v-model="form.start_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <div>
                                <InputLabel for="end_date" value="Fecha y hora de fin" />
                                <TextInput
                                    id="end_date"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    v-model="form.end_date"
                                />
                            </div>
                        </div>

                        <div class="mb-6">
                            <InputLabel for="location" value="Ubicación" />
                            <TextInput
                                id="location"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.location"
                            />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="color" value="Color" />
                            <div class="flex items-center gap-4 mt-2">
                                <input
                                    id="color"
                                    type="color"
                                    v-model="form.color"
                                    class="w-16 h-10 rounded border-gray-300"
                                />
                                <span class="text-sm text-gray-600">Selecciona un color para el evento</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input
                                    id="send_reminder"
                                    type="checkbox"
                                    v-model="form.send_reminder"
                                    class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                />
                                <InputLabel for="send_reminder" value="Enviar recordatorio por correo" class="ml-2" />
                            </div>
                        </div>

                        <div v-if="form.send_reminder" class="mb-6">
                            <InputLabel for="reminder_email" value="Correo para recordatorio *" />
                            <TextInput
                                id="reminder_email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.reminder_email"
                                :required="form.send_reminder"
                                placeholder="correo@ejemplo.com"
                            />
                            <InputError class="mt-2" :message="form.errors.reminder_email" />
                        </div>

                        <div v-if="form.send_reminder" class="mb-6">
                            <InputLabel for="reminder_minutes" value="Minutos antes del evento" />
                            <input
                                id="reminder_minutes"
                                type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                :value="form.reminder_minutes?.toString() || ''"
                                @input="form.reminder_minutes = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                min="1"
                                placeholder="60"
                            />
                            <InputError class="mt-2" :message="form.errors.reminder_minutes" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Crear Evento
                            </PrimaryButton>
                            <Link
                                :href="route('events.index')"
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

