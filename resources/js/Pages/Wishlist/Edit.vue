<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

interface WishlistItem {
    id: number;
    name: string;
    description?: string;
    category: string;
    price?: number;
    url?: string;
    priority: string;
    is_obtained: boolean;
    obtained_date?: string;
}

interface Props {
    item: WishlistItem;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.item.name,
    description: props.item.description || '',
    category: props.item.category,
    price: props.item.price?.toString() || '',
    url: props.item.url || '',
    priority: props.item.priority,
    is_obtained: props.item.is_obtained || false,
    obtained_date: props.item.obtained_date || '',
});

const submit = () => {
    form.put(route('wishlist.update', props.item.id));
};
</script>

<template>
    <Head title="Editar Deseo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                ✏️ Editar Deseo
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="name" value="Nombre *" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="description" value="Descripción" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.description"
                                rows="4"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <InputLabel for="category" value="Categoría *" />
                                <select
                                    id="category"
                                    v-model="form.category"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    required
                                >
                                    <option value="product">Producto</option>
                                    <option value="experience">Experiencia</option>
                                    <option value="book">Libro</option>
                                    <option value="movie">Película</option>
                                    <option value="other">Otro</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category" />
                            </div>

                            <div>
                                <InputLabel for="priority" value="Prioridad" />
                                <select
                                    id="priority"
                                    v-model="form.priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                >
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <InputLabel for="price" value="Precio" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    min="0"
                                />
                            </div>

                            <div>
                                <InputLabel for="url" value="URL" />
                                <TextInput
                                    id="url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.url"
                                />
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input
                                    id="is_obtained"
                                    type="checkbox"
                                    v-model="form.is_obtained"
                                    class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                />
                                <InputLabel for="is_obtained" value="Ya lo obtuve" class="ml-2" />
                            </div>
                        </div>

                        <div v-if="form.is_obtained" class="mb-6">
                            <InputLabel for="obtained_date" value="Fecha en que lo obtuve" />
                            <TextInput
                                id="obtained_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.obtained_date"
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Actualizar Deseo
                            </PrimaryButton>
                            <Link
                                :href="route('wishlist.index')"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-semibold"
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

