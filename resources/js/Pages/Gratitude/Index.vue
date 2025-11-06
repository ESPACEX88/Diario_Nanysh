<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    gratitudes: {
        data: any[];
        links: any;
        meta: any;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Gratitudes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mis Gratitudes
                </h2>
                <Link
                    :href="route('gratitude.create')"
                    class="inline-flex items-center px-4 py-2 bg-pink-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-pink-700"
                >
                    Nueva Gratitud
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="gratitudes.data.length > 0" class="space-y-4">
                    <Link
                        v-for="gratitude in gratitudes.data"
                        :key="gratitude.id"
                        :href="route('gratitude.show', gratitude.id)"
                        class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ new Date(gratitude.date).toLocaleDateString('es-ES', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-gray-700 dark:text-gray-300">
                                🙏 {{ gratitude.item_one }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300">
                                🙏 {{ gratitude.item_two }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300">
                                🙏 {{ gratitude.item_three }}
                            </p>
                        </div>
                    </Link>
                </div>
                <div
                    v-else
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center py-12"
                >
                    <span class="text-6xl mb-4 block">🙏</span>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        No hay gratitudes aún
                    </h3>
                    <Link
                        :href="route('gratitude.create')"
                        class="inline-flex items-center px-4 py-2 bg-pink-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-pink-700"
                    >
                        Crear Primera Gratitud
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

