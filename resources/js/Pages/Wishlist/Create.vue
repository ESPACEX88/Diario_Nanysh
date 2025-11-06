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
    category: 'product',
    price: null as number | null,
    url: '',
    priority: 'medium',
});

const submit = () => {
    form.post(route('wishlist.store'));
};
</script>

<template>
    <Head title="Nuevo Deseo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>✨</span>
                Agregar a Mi Lista de Deseos
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <InputLabel for="name" value="¿Qué deseas? *" class="text-pink-700 font-semibold" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    placeholder="Ej: Un vestido rosa, Viaje a París..."
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="description" value="Descripción" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="description"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Cuéntame más sobre este deseo..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="category" value="Categoría *" class="text-pink-700 font-semibold" />
                                    <select
                                        id="category"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        v-model="form.category"
                                        required
                                    >
                                        <option value="product">🛍️ Producto</option>
                                        <option value="experience">✨ Experiencia</option>
                                        <option value="book">📚 Libro</option>
                                        <option value="movie">🎬 Película</option>
                                        <option value="other">💝 Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <InputLabel for="priority" value="Prioridad" class="text-pink-700 font-semibold" />
                                    <select
                                        id="priority"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        v-model="form.priority"
                                    >
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <InputLabel for="price" value="Precio (opcional)" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="price"
                                        type="number"
                                        step="0.01"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.price"
                                        placeholder="0.00"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="url" value="Enlace (opcional)" class="text-pink-700 font-semibold" />
                                    <TextInput
                                        id="url"
                                        type="url"
                                        class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                        v-model="form.url"
                                        placeholder="https://..."
                                    />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">💝</span>
                                    Agregar Deseo
                                </PrimaryButton>
                                <Link
                                    :href="route('wishlist.index')"
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

