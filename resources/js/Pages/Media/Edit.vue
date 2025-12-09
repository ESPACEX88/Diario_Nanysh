<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

interface MediaItem {
    id: number;
    type: string;
    title: string;
    author?: string;
    description?: string;
    status: string;
    rating?: number;
    review?: string;
    url?: string;
}

interface Props {
    item: MediaItem;
}

const props = defineProps<Props>();

const form = useForm({
    type: props.item.type,
    title: props.item.title,
    author: props.item.author || '',
    description: props.item.description || '',
    status: props.item.status,
    rating: props.item.rating?.toString() || '',
    review: props.item.review || '',
    url: props.item.url || '',
});

const submit = () => {
    form.put(route('media.update', props.item.id));
};
</script>

<template>
    <Head title="Editar Artículo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                ✏️ Editar Artículo
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="type" value="Tipo *" />
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                required
                            >
                                <option value="book">Libro</option>
                                <option value="movie">Película</option>
                                <option value="series">Serie</option>
                                <option value="music">Música</option>
                                <option value="podcast">Podcast</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.type" />
                        </div>

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
                            <InputLabel for="author" value="Autor/Director" />
                            <TextInput
                                id="author"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.author"
                            />
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

                        <div class="mb-6">
                            <InputLabel for="status" value="Estado *" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                required
                            >
                                <option value="want">Quiero ver/leer</option>
                                <option value="in_progress">En progreso</option>
                                <option value="completed">Completado</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="rating" value="Calificación (1-5)" />
                            <TextInput
                                id="rating"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.rating"
                                min="1"
                                max="5"
                            />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="review" value="Reseña" />
                            <textarea
                                id="review"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.review"
                                rows="4"
                            ></textarea>
                        </div>

                        <div class="mb-6">
                            <InputLabel for="url" value="URL" />
                            <TextInput
                                id="url"
                                type="url"
                                class="mt-1 block w-full"
                                v-model="form.url"
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Actualizar Artículo
                            </PrimaryButton>
                            <Link
                                :href="route('media.index')"
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

