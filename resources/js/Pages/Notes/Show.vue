<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Props {
    note: any;
}

const props = defineProps<Props>();

const deleteNote = () => {
    if (confirm('¿Eliminar esta nota?')) {
        router.delete(route('notes.destroy', props.note.id));
    }
};
</script>

<template>
    <Head :title="note.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ note.title }}
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('notes.edit', note.id)"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                    >
                        Editar
                    </Link>
                    <button
                        @click="deleteNote"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6"
                    :style="{ borderLeft: `4px solid ${note.color || '#fbbf24'}` }"
                >
                    <div
                        v-if="note.content"
                        class="prose max-w-none"
                    >
                        <p
                            class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"
                            v-html="note.content.replace(/\n/g, '<br>')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

