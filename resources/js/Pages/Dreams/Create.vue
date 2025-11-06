<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    title: '',
    content: '',
    date: new Date().toISOString().split('T')[0],
    type: 'normal',
    mood: '',
    tags: [] as string[],
});

const submit = () => {
    form.post(route('dreams.store'));
};
</script>

<template>
    <Head title="Registrar Sueño" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>🌙</span>
                Registrar Mi Sueño
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-purple-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <InputLabel for="title" value="Título del sueño *" class="text-pink-700 font-semibold" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    placeholder="Ej: Volar sobre las nubes..."
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="content" value="Describe tu sueño *" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="content"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.content"
                                    rows="8"
                                    required
                                    placeholder="Cuéntame todos los detalles de tu sueño..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.content" />
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="date" value="Fecha *" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.date"
                                        required
                                    />
                                </div>

                                <div>
                                    <InputLabel for="type" value="Tipo *" class="text-pink-700 font-semibold" />
                                    <select
                                        id="type"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        v-model="form.type"
                                        required
                                    >
                                        <option value="normal">🌙 Normal</option>
                                        <option value="lucid">✨ Lúcido</option>
                                        <option value="nightmare">😰 Pesadilla</option>
                                        <option value="recurring">🔄 Recurrente</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="mood" value="Estado de ánimo (emoji)" class="text-pink-700 font-semibold" />
                                <TextInput
                                    id="mood"
                                    type="text"
                                    class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl text-center text-2xl"
                                    v-model="form.mood"
                                    maxlength="2"
                                    placeholder="😊"
                                />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">🌙</span>
                                    Guardar Sueño
                                </PrimaryButton>
                                <Link
                                    :href="route('dreams.index')"
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

