<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LazyImage from '@/Components/LazyImage.vue';

interface Photo {
    id: number;
    path: string;
    thumbnail_path?: string;
    full_url: string;
    thumbnail_url: string;
    description?: string;
    taken_at?: string;
    album_id?: number;
}

interface Album {
    id: number;
    name: string;
}

interface Props {
    photo: Photo;
    albums: Album[];
}

const props = defineProps<Props>();

const form = useForm({
    description: props.photo.description || '',
    album_id: props.photo.album_id || null as number | null,
    taken_at: props.photo.taken_at ? new Date(props.photo.taken_at).toISOString().slice(0, 16) : '',
});

const getPhotoUrl = (path: string) => {
    // Si ya es una URL completa (Cloudinary), devolverla tal cual
    if (path.startsWith('https://') || path.startsWith('http://')) {
        return path;
    }
    // Si es una ruta local, agregar /storage/
    return `/storage/${path}`;
};

const submit = () => {
    form.put(route('photos.update', props.photo.id));
};
</script>

<template>
    <Head :title="`Editar Foto`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                ✏️ Editar Foto
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <div class="mb-6">
                        <LazyImage
                            :src="props.photo.thumbnail_url || props.photo.full_url"
                            :alt="props.photo.description || 'Foto'"
                            class="w-full h-64 object-cover rounded-lg border-2 border-pink-200"
                        />
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="description" value="Descripción" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.description"
                                rows="3"
                                placeholder="Describe esta foto..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="album_id" value="Álbum (opcional)" />
                            <select
                                id="album_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.album_id"
                            >
                                <option :value="null">Sin álbum</option>
                                <option v-for="album in props.albums" :key="album.id" :value="album.id">
                                    {{ album.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.album_id" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="taken_at" value="Fecha de la foto" />
                            <input
                                id="taken_at"
                                type="datetime-local"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                v-model="form.taken_at"
                            />
                            <InputError class="mt-2" :message="form.errors.taken_at" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Guardar Cambios
                            </PrimaryButton>
                            <Link
                                :href="route('photos.show', props.photo.id)"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-semibold"
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

