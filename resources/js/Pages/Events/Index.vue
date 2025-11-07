<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Event {
    id: number;
    title: string;
    description?: string;
    start_date: string;
    end_date?: string;
    location?: string;
    color: string;
}

interface Props {
    events: Event[];
}

defineProps<Props>();
</script>

<template>
    <Head title="Eventos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    📅 Mis Eventos
                </h2>
                <Link
                    :href="route('events.create')"
                    class="px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md hover:shadow-lg"
                >
                    + Nuevo Evento
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="events.length > 0" class="max-h-96 overflow-y-auto space-y-2 pr-2">
                    <div
                        v-for="event in events"
                        :key="event.id"
                        class="bg-white rounded-lg shadow-sm border-l-4 p-3 hover:shadow-md transition-all"
                        :style="{ borderLeftColor: event.color || '#EC4899' }"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900 mb-1">{{ event.title }}</h3>
                                <p v-if="event.description" class="text-gray-600 text-sm mb-2 line-clamp-1">{{ event.description }}</p>
                                <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                                    <span>
                                        📅 {{ new Date(event.start_date).toLocaleDateString('es-ES', {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) }}
                                    </span>
                                    <span v-if="event.location">📍 {{ event.location }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('events.edit', event.id)"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-200 transition-all"
                                >
                                    Editar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-center py-16 bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl border-2 border-pink-200"
                >
                    <span class="text-6xl block mb-4">📅</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay eventos aún</h3>
                    <p class="text-gray-600 mb-6">Crea tu primer evento para comenzar a organizarte</p>
                    <Link
                        :href="route('events.create')"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md hover:shadow-lg"
                    >
                        + Crear Primer Evento
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

