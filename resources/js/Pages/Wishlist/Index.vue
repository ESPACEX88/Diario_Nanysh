<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    items: WishlistItem[];
}

const props = defineProps<Props>();

const pendingItems = computed(() => 
    props.items.filter(item => !item.is_obtained)
);

const obtainedItems = computed(() => 
    props.items.filter(item => item.is_obtained)
);

const getCategoryIcon = (category: string) => {
    const icons: Record<string, string> = {
        product: '🛍️',
        experience: '✨',
        book: '📚',
        movie: '🎬',
        other: '💝',
    };
    return icons[category] || '💝';
};

const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'high': return 'from-red-400 to-pink-500';
        case 'medium': return 'from-amber-400 to-orange-400';
        case 'low': return 'from-green-400 to-emerald-400';
        default: return 'from-pink-400 to-rose-400';
    }
};

const deleteItem = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar este artículo?')) {
        router.delete(route('wishlist.destroy', id));
    }
};
</script>

<template>
    <Head title="Lista de Deseos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                    <span>💝</span>
                    Mi Lista de Deseos
                </h2>
                <Link
                    :href="route('wishlist.create')"
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                >
                    + Agregar Deseo
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <!-- Pending Items -->
                <div v-if="pendingItems.length > 0" class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="text-2xl">✨</span>
                        Deseos Pendientes ({{ pendingItems.length }})
                    </h3>
                    <div class="max-h-96 overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="item in pendingItems"
                            :key="item.id"
                            class="group relative overflow-hidden rounded-lg bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-2 border-pink-200 hover:border-pink-400 transition-all shadow-sm hover:shadow-md"
                        >
                            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="text-3xl">{{ getCategoryIcon(item.category) }}</div>
                                    <span
                                        :class="['px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r', getPriorityColor(item.priority)]"
                                    >
                                        {{ item.priority === 'high' ? 'Alta' : item.priority === 'medium' ? 'Media' : 'Baja' }}
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-gray-900 mb-1">{{ item.name }}</h4>
                                <p v-if="item.description" class="text-gray-600 text-xs mb-2 line-clamp-1">
                                    {{ item.description }}
                                </p>
                                <div class="flex items-center justify-between mb-2">
                                    <span v-if="item.price" class="text-lg font-bold text-pink-600">
                                        💰 ${{ item.price.toLocaleString() }}
                                    </span>
                                    <span class="text-xs text-gray-500 capitalize">
                                        {{ item.category }}
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('wishlist.edit', item.id)"
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-400 to-rose-400 text-white rounded-lg text-sm font-semibold hover:from-pink-500 hover:to-rose-500 transition-all text-center"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="deleteItem(item.id)"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all"
                                    >
                                        🗑️
                                    </button>
                                </div>
                                <a
                                    v-if="item.url"
                                    :href="item.url"
                                    target="_blank"
                                    class="mt-3 block text-center text-sm text-pink-600 hover:text-pink-700 font-semibold"
                                >
                                    Ver enlace →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Obtained Items -->
                <div v-if="obtainedItems.length > 0" class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="text-2xl">🎉</span>
                        Deseos Cumplidos ({{ obtainedItems.length }})
                    </h3>
                    <div class="max-h-64 overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div
                            v-for="item in obtainedItems"
                            :key="item.id"
                            class="relative overflow-hidden rounded-lg bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 border-2 border-green-200 opacity-75"
                        >
                            <div class="absolute top-2 right-2 text-xl">✅</div>
                            <div class="p-4">
                                <div class="text-3xl mb-2 opacity-50">{{ getCategoryIcon(item.category) }}</div>
                                <h4 class="text-sm font-bold text-gray-700 mb-1 line-through">{{ item.name }}</h4>
                                <p v-if="item.obtained_date" class="text-xs text-gray-500">
                                    Obtenido: {{ new Date(item.obtained_date).toLocaleDateString('es-ES') }}
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="items.length === 0"
                    class="text-center py-20 bg-gradient-to-br from-pink-100 via-rose-100 to-purple-100 rounded-3xl border-4 border-pink-300"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-200/20 to-purple-200/20 rounded-3xl"></div>
                    <div class="relative">
                        <span class="text-8xl block mb-6 animate-bounce">💝</span>
                        <h3 class="text-3xl font-bold text-gray-800 mb-3">Tu lista de deseos está vacía</h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                            Agrega tus sueños y deseos aquí para recordarlos siempre
                        </p>
                        <Link
                            :href="route('wishlist.create')"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-lg shadow-xl hover:from-pink-600 hover:to-rose-600 hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                        >
                            <span class="mr-2 text-2xl">✨</span>
                            Agregar Mi Primer Deseo
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

