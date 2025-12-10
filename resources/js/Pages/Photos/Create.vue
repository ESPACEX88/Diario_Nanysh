<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

interface Album {
    id: number;
    name: string;
}

interface Props {
    albums: Album[];
}

const props = defineProps<Props>();

const form = useForm({
    photo: null as File | null,
    description: '',
    album_id: null as number | null,
    taken_at: '',
});

const preview = ref<string | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.photo = target.files[0];
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const submit = () => {
    form.post(route('photos.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Subir Foto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                📸 Subir Foto
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-pink-100 dark:border-gray-700 p-8">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="photo" value="Foto *" />
                            <input
                                id="photo"
                                type="file"
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                                @change="handleFileChange"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.photo" />
                            
                            <div v-if="preview" class="mt-4">
                                <img :src="preview" alt="Preview" class="max-w-full h-64 object-cover rounded-lg border-2 border-pink-200" />
                            </div>
                        </div>

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
                                Subir Foto
                            </PrimaryButton>
                            <Link
                                :href="route('photos.index')"
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

