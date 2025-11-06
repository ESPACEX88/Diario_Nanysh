<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    recentEntries: any[];
    todayEntry: any;
    pet: {
        name: string;
        level: number;
        happiness: number;
        hunger: number;
        energy: number;
        health: number;
        coins: number;
        mood: string;
    };
    stats: {
        totalEntries: number;
        favoriteEntries: number;
        totalNotes: number;
        pinnedNotes: number;
        activeGoals: number;
        activeHabits: number;
        thisWeekGratitudes: number;
    };
    pendingTodos?: any[];
    upcomingEvents?: any[];
    dailyQuote?: {
        quote: string;
        author?: string;
    } | null;
}

const props = defineProps<Props>();

const statCards = computed(() => [
    {
        title: 'Entradas del Diario',
        value: props.stats.totalEntries,
        icon: '✨',
        gradient: 'from-rose-400 to-pink-500',
        bgGradient: 'from-rose-50 to-pink-50',
        link: route('diary.index'),
    },
    {
        title: 'Favoritos',
        value: props.stats.favoriteEntries,
        icon: '💖',
        gradient: 'from-amber-400 to-orange-400',
        bgGradient: 'from-amber-50 to-orange-50',
        link: route('diary.index', { favorite: true }),
    },
    {
        title: 'Notas',
        value: props.stats.totalNotes,
        icon: '🌸',
        gradient: 'from-purple-400 to-pink-400',
        bgGradient: 'from-purple-50 to-pink-50',
        link: route('notes.index'),
    },
    {
        title: 'Metas Activas',
        value: props.stats.activeGoals,
        icon: '🎀',
        gradient: 'from-fuchsia-400 to-purple-500',
        bgGradient: 'from-fuchsia-50 to-purple-50',
        link: route('goals.index'),
    },
    {
        title: 'Hábitos Activos',
        value: props.stats.activeHabits,
        icon: '💫',
        gradient: 'from-indigo-400 to-purple-500',
        bgGradient: 'from-indigo-50 to-purple-50',
        link: route('habits.index'),
    },
    {
        title: 'Gratitudes Esta Semana',
        value: props.stats.thisWeekGratitudes,
        icon: '🌺',
        gradient: 'from-pink-400 to-rose-500',
        bgGradient: 'from-pink-50 to-rose-50',
        link: route('gratitude.index'),
    },
]);

const toggleTodo = (id: number) => {
    router.post(route('todos.toggle', id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-3xl">💕</span>
                <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    Mi Diario Personal
            </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome Message -->
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                        ¡Hola, hermosa! 👋
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Bienvenida a tu espacio personal de reflexión y crecimiento
                    </p>
                </div>

                <!-- Snoopy Widget -->
                <div class="mb-8">
                    <Link
                        :href="route('pet.index')"
                        class="block relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-100 via-sky-50 to-cyan-50 shadow-xl border-2 border-blue-200 hover:border-blue-300 transition-all transform hover:-translate-y-1"
                    >
                        <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-blue-200/30 to-cyan-200/30 rounded-full -mr-24 -mt-24"></div>
                        <div class="relative p-6 flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="relative text-7xl transform hover:scale-110 transition-transform">
                                    <span>🐕</span>
                                    <span class="absolute -top-2 -right-2 text-3xl animate-pulse">❤️</span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-1">
                                        {{ pet.name }}
                                    </h3>
                                    <p class="text-gray-600 font-semibold mb-2">
                                        Nivel {{ pet.level }} • 
                                        <span :class="{
                                            'text-green-600': (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 >= 70,
                                            'text-yellow-600': (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 >= 40 && (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 < 70,
                                            'text-red-600': (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 < 40
                                        }">
                                            {{ (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 >= 70 ? 'Muy feliz' : (pet.happiness + pet.hunger + pet.energy + pet.health) / 4 >= 40 ? 'Bien' : 'Necesita atención' }}
                                        </span>
                                    </p>
                                    <div class="flex gap-4 text-sm mb-2">
                                        <span class="text-gray-600">💖 {{ pet.happiness }}%</span>
                                        <span class="text-gray-600">🍽️ {{ pet.hunger }}%</span>
                                        <span class="text-gray-600">⚡ {{ pet.energy }}%</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-gradient-to-r from-amber-400 to-yellow-500 px-3 py-1 rounded-full inline-block">
                                        <span class="text-sm">🪙</span>
                                        <span class="font-bold text-white text-sm">{{ pet.coins || 0 }} fichitas</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-sm shadow-md">
                                    Cuidar →
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-10">
                    <Link
                        v-for="card in statCards"
                        :key="card.title"
                        :href="card.link"
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                        :class="card.bgGradient"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-300" :class="card.gradient"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">
                                        {{ card.title }}
                                    </p>
                                    <p class="text-4xl font-bold text-gray-900">
                                        {{ card.value }}
                                    </p>
                                </div>
                                <div class="text-5xl transform group-hover:scale-110 transition-transform duration-300">
                                    {{ card.icon }}
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Today's Entry Section -->
                <div
                    v-if="todayEntry"
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-100 via-rose-50 to-purple-50 shadow-xl mb-8 border border-pink-200"
                >
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-300/20 to-purple-300/20 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative p-8">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">📖</span>
                                <h3 class="text-xl font-bold text-gray-800">
                                    Entrada de Hoy
                                </h3>
                            </div>
                            <Link
                                :href="route('diary.show', todayEntry.id)"
                                class="px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full text-sm font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md hover:shadow-lg"
                            >
                                Ver más →
                            </Link>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-3">
                            {{ todayEntry.title }}
                        </h4>
                        <p class="text-gray-700 line-clamp-3 mb-4 leading-relaxed">
                            {{ todayEntry.content }}
                        </p>
                        <div class="flex items-center gap-4">
                            <span class="text-3xl">{{ todayEntry.mood }}</span>
                            <span class="text-sm text-gray-600 font-medium">
                                {{ new Date(todayEntry.date).toLocaleDateString('es-ES', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-100 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-pink-500 to-rose-500">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span>✨</span>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
                            <Link
                                :href="route('diary.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-rose-50 to-pink-50 rounded-xl hover:from-rose-100 hover:to-pink-100 transition-all border-2 border-transparent hover:border-pink-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">✨</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Nueva Entrada
                                </span>
                            </Link>
                            <Link
                                :href="route('todos.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl hover:from-amber-100 hover:to-orange-100 transition-all border-2 border-transparent hover:border-amber-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">✅</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Nueva Tarea
                                </span>
                            </Link>
                            <Link
                                :href="route('events.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl hover:from-purple-100 hover:to-indigo-100 transition-all border-2 border-transparent hover:border-purple-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">📅</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Nuevo Evento
                                </span>
                            </Link>
                            <Link
                                :href="route('wishlist.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl hover:from-pink-100 hover:to-rose-100 transition-all border-2 border-transparent hover:border-pink-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">💝</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Agregar Deseo
                                </span>
                            </Link>
                            <Link
                                :href="route('dreams.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl hover:from-indigo-100 hover:to-purple-100 transition-all border-2 border-transparent hover:border-indigo-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">🌙</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Registrar Sueño
                                </span>
                            </Link>
                            <Link
                                :href="route('notes.create')"
                                class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all border-2 border-transparent hover:border-purple-300 hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">🌸</span>
                                <span class="text-sm font-semibold text-gray-700 text-center">
                                    Nueva Nota
                                </span>
                            </Link>
                        </div>
                        
                        <!-- Más Acciones -->
                        <div class="mt-4 pt-4 border-t border-pink-200">
                            <h4 class="text-sm font-semibold text-gray-600 mb-3 text-center">Más opciones</h4>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <Link
                                    :href="route('counters.create')"
                                    class="group flex flex-col items-center justify-center p-4 bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg hover:from-pink-100 hover:to-rose-100 transition-all border border-pink-200 hover:border-pink-300"
                                >
                                    <span class="text-2xl mb-2">📅</span>
                                    <span class="text-xs font-semibold text-gray-700 text-center">Contador</span>
                                </Link>
                                <Link
                                    :href="route('media.create')"
                                    class="group flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg hover:from-blue-100 hover:to-indigo-100 transition-all border border-blue-200 hover:border-blue-300"
                                >
                                    <span class="text-2xl mb-2">📚</span>
                                    <span class="text-xs font-semibold text-gray-700 text-center">Libro/Película</span>
                                </Link>
                                <Link
                                    :href="route('meals.create')"
                                    class="group flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all border border-green-200 hover:border-green-300"
                                >
                                    <span class="text-2xl mb-2">🍽️</span>
                                    <span class="text-xs font-semibold text-gray-700 text-center">Comida</span>
                                </Link>
                                <Link
                                    :href="route('cycle.create')"
                                    class="group flex flex-col items-center justify-center p-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg hover:from-pink-100 hover:to-purple-100 transition-all border border-pink-200 hover:border-pink-300"
                                >
                                    <span class="text-2xl mb-2">🌸</span>
                                    <span class="text-xs font-semibold text-gray-700 text-center">Ciclo</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Motivational Quote -->
                <div
                    v-if="dailyQuote"
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-200 via-rose-200 to-purple-200 shadow-xl mb-8 border-2 border-pink-300"
                >
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-300/30 to-purple-300/30 rounded-full -mr-20 -mt-20"></div>
                    <div class="relative p-8 text-center">
                        <div class="text-5xl mb-4">💝</div>
                        <p class="text-2xl font-semibold text-gray-800 mb-3 italic leading-relaxed">
                            "{{ dailyQuote.quote }}"
                        </p>
                        <p
                            v-if="dailyQuote.author"
                            class="text-sm text-gray-600 font-medium"
                        >
                            — {{ dailyQuote.author }}
                        </p>
                    </div>
                </div>

                <!-- Pending Todos -->
                <div
                    v-if="pendingTodos && pendingTodos.length > 0"
                    class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-100 overflow-hidden"
                >
                    <div class="p-6 bg-gradient-to-r from-amber-500 to-orange-500">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span>✅</span>
                                Tareas Pendientes
                            </h3>
                            <Link
                                :href="route('todos.index')"
                                class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold hover:bg-white/30 transition-all"
                            >
                                Ver todas →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div
                                v-for="todo in pendingTodos"
                                :key="todo.id"
                                class="flex items-center gap-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border-2 border-amber-100 hover:border-amber-300 transition-all"
                            >
                                <input
                                    type="checkbox"
                                    :checked="todo.is_completed"
                                    @change="toggleTodo(todo.id)"
                                    class="w-5 h-5 text-pink-500 rounded focus:ring-pink-500"
                                />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ todo.title }}</h4>
                                    <p
                                        v-if="todo.due_date"
                                        class="text-xs text-gray-600 mt-1"
                                    >
                                        📅 {{ new Date(todo.due_date).toLocaleDateString('es-ES') }}
                                    </p>
                                </div>
                                <span
                                    v-if="todo.priority === 'high'"
                                    class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold"
                                >
                                    Alta
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div
                    v-if="upcomingEvents && upcomingEvents.length > 0"
                    class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-100 overflow-hidden"
                >
                    <div class="p-6 bg-gradient-to-r from-purple-500 to-pink-500">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span>📅</span>
                                Próximos Eventos
                            </h3>
                            <Link
                                :href="route('events.index')"
                                class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold hover:bg-white/30 transition-all"
                            >
                                Ver calendario →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <Link
                                v-for="event in upcomingEvents"
                                :key="event.id"
                                :href="route('events.show', event.id)"
                                class="block p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-100 hover:border-purple-300 hover:shadow-lg transition-all"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-3 h-3 rounded-full"
                                        :style="{ backgroundColor: event.color || '#EC4899' }"
                                    ></div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ event.title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            📅 {{ new Date(event.start_date).toLocaleDateString('es-ES', {
                                                weekday: 'long',
                                                day: 'numeric',
                                                month: 'long',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Entries -->
                <div
                    v-if="recentEntries.length > 0"
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden"
                >
                    <div class="p-6 bg-gradient-to-r from-purple-500 to-pink-500">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span>💫</span>
                                Entradas Recientes
                            </h3>
                            <Link
                                :href="route('diary.index')"
                                class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold hover:bg-white/30 transition-all"
                            >
                                Ver todas →
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <Link
                                v-for="entry in recentEntries"
                                :key="entry.id"
                                :href="route('diary.show', entry.id)"
                                class="group block p-5 bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl border-2 border-pink-100 hover:border-pink-300 hover:shadow-lg transition-all transform hover:-translate-y-1"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-2xl">{{ entry.mood }}</span>
                                            <h4 class="font-bold text-gray-900 text-lg">
                                                {{ entry.title }}
                                            </h4>
                                            <span
                                                v-if="entry.is_favorite"
                                                class="text-yellow-500 text-lg"
                                                title="Favorito"
                                            >
                                                💖
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 line-clamp-2 mb-3 leading-relaxed">
                                            {{ entry.content }}
                                        </p>
                                        <span class="text-xs text-gray-500 font-medium">
                                            {{ new Date(entry.date).toLocaleDateString('es-ES', {
                                                weekday: 'long',
                                                day: 'numeric',
                                                month: 'long'
                                            }) }}
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-100 via-purple-50 to-rose-50 shadow-xl text-center py-16 border-2 border-pink-200"
                >
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-300/30 to-purple-300/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-rose-300/20 to-pink-300/20 rounded-full -ml-24 -mb-24"></div>
                    <div class="relative p-6">
                        <span class="text-7xl mb-6 block transform hover:scale-110 transition-transform inline-block">✨</span>
                        <h3 class="text-3xl font-bold text-gray-800 mb-3">
                            ¡Comienza tu diario, hermosa!
                        </h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                            Crea tu primera entrada del diario para comenzar a documentar tus días especiales.
                        </p>
                        <Link
                            :href="route('diary.create')"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold text-base shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-1 transition-all"
                        >
                            <span class="mr-2">✨</span>
                            Crear Primera Entrada
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
