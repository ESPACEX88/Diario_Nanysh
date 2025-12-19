<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import LazyImage from '@/Components/LazyImage.vue';

interface Photo {
    id: number;
    path: string;
    thumbnail_path?: string;
    full_url: string;
    thumbnail_url: string;
    description?: string;
    taken_at?: string;
    album?: {
        id: number;
        name: string;
    };
}

interface Album {
    id: number;
    name: string;
}

interface Props {
    photos: {
        data: Photo[];
        links: any;
        meta: any;
    };
    albums: Album[];
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);
const photoToDelete = ref<number | null>(null);

const deletePhoto = (id: number) => {
    photoToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (photoToDelete.value) {
        router.delete(route('photos.destroy', photoToDelete.value));
    }
    showDeleteModal.value = false;
    photoToDelete.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    photoToDelete.value = null;
};

const getPhotoUrl = (path: string) => {
    // Si ya es una URL completa (Cloudinary), devolverla tal cual
    if (path.startsWith('https://') || path.startsWith('http://')) {
        return path;
    }
    // Si es una ruta local, agregar /storage/
    return `/storage/${path}`;
};
</script>

<template>
    <Head title="Mis Fotos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>📸</span>
                    Mis Fotos
                </h2>
                <Link
                    :href="route('photos.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Subir Foto
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="photos.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div
                        v-for="photo in photos.data"
                        :key="photo.id"
                        class="group relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all"
                    >
                        <Link :href="route('photos.show', photo.id)">
                            <LazyImage
                                :src="photo.thumbnail_url || photo.full_url"
                                :alt="photo.description || 'Foto'"
                                class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                    <p v-if="photo.description" class="text-sm font-semibold truncate">
                                        {{ photo.description }}
                                    </p>
                                    <p v-if="photo.album" class="text-xs text-gray-200 mt-1">
                                        {{ photo.album.name }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                        <button
                            @click="deletePhoto(photo.id)"
                            class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                        >
                            🗑️
                        </button>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-3xl border-4 border-pink-300 dark:border-gray-700"
                >
                    <span class="text-8xl block mb-6">📸</span>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-3">No hay fotos aún</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Sube tus fotos favoritas para crear recuerdos
                    </p>
                    <Link
                        :href="route('photos.create')"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <span class="mr-2 text-2xl">✨</span>
                        Subir Mi Primera Foto
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Eliminar Foto"
            message="¿Estás segura de que quieres eliminar esta foto? Esta acción no se puede deshacer."
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            type="danger"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

