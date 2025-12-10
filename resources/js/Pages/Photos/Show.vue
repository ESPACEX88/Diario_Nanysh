<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import LazyImage from '@/Components/LazyImage.vue';

interface Photo {
    id: number;
    path: string;
    thumbnail_path?: string;
    description?: string;
    taken_at?: string;
    album?: {
        id: number;
        name: string;
    };
}

interface Props {
    photo: Photo;
}

const props = defineProps<Props>();

const getPhotoUrl = (path: string) => {
    return `/storage/${path}`;
};
</script>

<template>
    <Head :title="`Foto: ${photo.description || 'Sin descripción'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    📸 Ver Foto
                </h2>
                <Link
                    :href="route('photos.edit', photo.id)"
                    class="px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-full text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all"
                >
                    Editar
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 overflow-hidden">
                    <LazyImage
                        :src="getPhotoUrl(photo.path)"
                        :alt="photo.description || 'Foto'"
                        class="w-full h-auto max-h-[70vh] object-contain"
                    />
                    <div class="p-6">
                        <div v-if="photo.description" class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Descripción</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ photo.description }}</p>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <span v-if="photo.album">
                                📁 {{ photo.album.name }}
                            </span>
                            <span v-if="photo.taken_at">
                                📅 {{ new Date(photo.taken_at).toLocaleDateString('es-ES') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

