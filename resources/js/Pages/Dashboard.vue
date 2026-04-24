<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import StatCard from '@/Components/StatCard.vue';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';

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
        streak?: number;
        thisWeekEntries?: number;
        completedTodosThisWeek?: number;
    };
    pendingTodos?: any[];
    activeHabitsQuick?: Array<{
        id: number;
        name: string;
        icon?: string;
        is_active: boolean;
    }>;
    upcomingEvents?: any[];
    dailyQuote?: {
        quote: string;
        author?: string;
    } | null;
}

const props = defineProps<Props>();
const isLoading = ref(true);

onMounted(() => {
    // Simular carga inicial para mostrar skeletons
    setTimeout(() => {
        isLoading.value = false;
    }, 800);
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 12) return 'Buenos días';
    if (hour >= 12 && hour < 19) return 'Buenas tardes';
    return 'Buenas noches';
});

const petStatus = computed(() => {
    const avg = (props.pet.happiness + props.pet.hunger + props.pet.energy + props.pet.health) / 4;
    if (avg >= 80) return { text: '¡Muy feliz!', emoji: '😊', color: 'text-green-600' };
    if (avg >= 60) return { text: 'Bien', emoji: '🙂', color: 'text-yellow-600' };
    if (avg >= 40) return { text: 'Necesita atención', emoji: '😐', color: 'text-orange-600' };
    return { text: 'Necesita cuidado urgente', emoji: '😟', color: 'text-red-600' };
});

const petProgressBars = computed(() => [
    { label: 'Felicidad', icon: '💖', value: props.pet.happiness, color: 'rose' },
    { label: 'Hambre', icon: '🍽️', value: props.pet.hunger, color: 'amber' },
    { label: 'Energía', icon: '⚡', value: props.pet.energy, color: 'purple' },
    { label: 'Salud', icon: '💚', value: props.pet.health, color: 'emerald' },
]);

const suggestions = computed(() => {
    const suggestions = [];
    
    if (!props.todayEntry) {
        suggestions.push({
            text: '¿Cómo fue tu día hoy? Crea una entrada en tu diario',
            icon: '✨',
            link: route('diary.create'),
            color: 'from-pink-500 to-rose-500',
        });
    }
    
    if (props.pendingTodos && props.pendingTodos.length > 0) {
        const urgentTodos = props.pendingTodos.filter(t => t.priority === 'high');
        if (urgentTodos.length > 0) {
            suggestions.push({
                text: `Tienes ${urgentTodos.length} tarea${urgentTodos.length > 1 ? 's' : ''} de alta prioridad`,
                icon: '⚠️',
                link: route('todos.index'),
                color: 'from-red-500 to-orange-500',
            });
        }
    }
    
    if (props.pet.happiness < 50 || props.pet.hunger < 50) {
        suggestions.push({
            text: `${props.pet.name} necesita tu atención`,
            icon: '🐕',
            link: route('pet.index'),
            color: 'from-blue-500 to-cyan-500',
        });
    }
    
    if (props.stats.streak && props.stats.streak > 0) {
        suggestions.push({
            text: `¡Llevas ${props.stats.streak} día${props.stats.streak > 1 ? 's' : ''} seguido${props.stats.streak > 1 ? 's' : ''} escribiendo! 🔥`,
            icon: '🔥',
            link: route('diary.create'),
            color: 'from-orange-500 to-red-500',
        });
    }
    
    return suggestions;
});

const statCards = computed(() => [
    {
        title: 'Racha de Días',
        value: props.stats.streak || 0,
        icon: '🔥',
        trend: props.stats.streak && props.stats.streak > 0 ? 100 : 0,
        trendLabel: props.stats.streak && props.stats.streak > 0 ? '¡Sigue así!' : 'Comienza hoy',
        color: 'orange' as const,
        link: route('diary.create'),
    },
    {
        title: 'Entradas del Diario',
        value: props.stats.totalEntries,
        icon: '✨',
        trend: ((props.stats.thisWeekEntries || 0) / Math.max(props.stats.totalEntries, 1)) * 100,
        trendLabel: `${props.stats.thisWeekEntries || 0} esta semana`,
        color: 'rose' as const,
        link: route('diary.index'),
    },
    {
        title: 'Favoritos',
        value: props.stats.favoriteEntries,
        icon: '💖',
        trend: 0,
        trendLabel: 'Momentos especiales',
        color: 'amber' as const,
        link: route('diary.index', { favorite: true }),
    },
    {
        title: 'Tareas Completadas',
        value: props.stats.completedTodosThisWeek || 0,
        icon: '✅',
        trend: 0,
        trendLabel: 'Esta semana',
        color: 'emerald' as const,
        link: route('todos.index'),
    },
    {
        title: 'Notas',
        value: props.stats.totalNotes,
        icon: '🌸',
        trend: ((props.stats.pinnedNotes || 0) / Math.max(props.stats.totalNotes, 1)) * 100,
        trendLabel: `${props.stats.pinnedNotes} fijadas`,
        color: 'purple' as const,
        link: route('notes.index'),
    },
    {
        title: 'Metas Activas',
        value: props.stats.activeGoals,
        icon: '🎀',
        trend: 0,
        trendLabel: 'En progreso',
        color: 'fuchsia' as const,
        link: route('goals.index'),
    },
]);

const quickActions = computed(() => [
    { label: 'Nueva Entrada', icon: '✨', route: route('diary.create'), gradient: 'from-rose-50 to-pink-50', hoverGradient: 'hover:from-rose-100 hover:to-pink-100', border: 'hover:border-pink-300' },
    { label: 'Nueva Tarea', icon: '✅', route: route('todos.create'), gradient: 'from-amber-50 to-orange-50', hoverGradient: 'hover:from-amber-100 hover:to-orange-100', border: 'hover:border-amber-300' },
    { label: 'Nuevo Evento', icon: '📅', route: route('events.create'), gradient: 'from-purple-50 to-indigo-50', hoverGradient: 'hover:from-purple-100 hover:to-indigo-100', border: 'hover:border-purple-300' },
    { label: 'Agregar Deseo', icon: '💝', route: route('wishlist.create'), gradient: 'from-pink-50 to-rose-50', hoverGradient: 'hover:from-pink-100 hover:to-rose-100', border: 'hover:border-pink-300' },
    { label: 'Registrar Sueño', icon: '🌙', route: route('dreams.create'), gradient: 'from-indigo-50 to-purple-50', hoverGradient: 'hover:from-indigo-100 hover:to-purple-100', border: 'hover:border-indigo-300' },
    { label: 'Minijuegos', icon: '🎰', route: route('minigame.doors'), gradient: 'from-yellow-50 to-orange-50', hoverGradient: 'hover:from-yellow-100 hover:to-orange-100', border: 'hover:border-yellow-300' },
    { label: 'Mis Hábitos', icon: '🎯', route: route('habits.index'), gradient: 'from-emerald-50 to-green-50', hoverGradient: 'hover:from-emerald-100 hover:to-green-100', border: 'hover:border-emerald-300' },
]);

const toggleTodo = (id: number) => {
    router.post(route('todos.toggle', id), {}, {
        preserveScroll: true,
    });
};

const toggleHabitToday = (id: number) => {
    router.post(route('habits.log', id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-4xl animate-pulse">💕</span>
                    <div>
                        <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 via-rose-600 to-purple-600 bg-clip-text text-transparent">
                            {{ greeting }}, hermosa
                        </h2>
                        <p class="text-sm text-gray-600">Tu espacio personal de crecimiento</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Hero Section: Snoopy + Quote -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Snoopy Widget -->
                    <div class="lg:col-span-2">
                        <Link
                            :href="route('pet.index')"
                            class="block relative overflow-hidden rounded-[2rem] border border-rose-200/80 bg-gradient-to-br from-rose-100 via-pink-50 to-fuchsia-100 shadow-[0_24px_70px_rgba(236,72,153,0.14)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_30px_90px_rgba(236,72,153,0.2)]"
                        >
                            <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-gradient-to-br from-rose-200/40 to-fuchsia-200/40 -mr-32 -mt-32 animate-pulse"></div>
                            <div class="relative p-8">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-6">
                                        <div class="relative">
                                            <span class="text-8xl transform hover:scale-110 transition-transform block">🐕</span>
                                            <span class="absolute -top-1 -right-1 text-4xl animate-bounce">❤️</span>
                                        </div>
                                        <div>
                                            <h3 class="mb-2 text-3xl font-bold text-rose-950">
                                                {{ pet.name }}
                                            </h3>
                                            <p class="mb-3 text-lg font-semibold text-rose-900/80">
                                                Nivel <AnimatedCounter :value="pet.level" :duration="1000" /> • 
                                                <span :class="petStatus.color" class="font-bold">
                                                    {{ petStatus.emoji }} {{ petStatus.text }}
                                                </span>
                                            </p>
                                            <div class="grid grid-cols-2 gap-3 mb-3">
                                                <div v-for="bar in petProgressBars" :key="bar.label" class="flex items-center gap-2">
                                                    <span class="text-xl">{{ bar.icon }}</span>
                                                    <div class="flex-1">
                                                        <ProgressBar :progress="bar.value" :color="bar.color" size="sm" showValue />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-2 shadow-lg shadow-rose-500/20">
                                                <span class="text-xl">🪙</span>
                                                <span class="font-bold text-white text-lg">
                                                    <AnimatedCounter :value="pet.coins || 0" :duration="1500" /> fichitas
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block rounded-full bg-gradient-to-r from-rose-500 to-fuchsia-500 px-6 py-3 font-bold text-white shadow-lg shadow-rose-500/20 transition-all hover:scale-105 hover:shadow-xl">
                                            Cuidar →
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Daily Quote -->
                    <div
                        v-if="dailyQuote"
                        class="relative overflow-hidden rounded-[2rem] border border-fuchsia-200/80 bg-gradient-to-br from-fuchsia-100 via-rose-100 to-pink-100 shadow-[0_24px_70px_rgba(192,38,211,0.14)]"
                    >
                        <div class="absolute top-0 right-0 h-48 w-48 rounded-full bg-gradient-to-br from-pink-300/40 to-fuchsia-300/40 -mr-24 -mt-24 animate-pulse"></div>
                        <div class="relative p-6 h-full flex flex-col justify-center text-center">
                            <div class="text-6xl mb-4 animate-bounce">💝</div>
                            <p class="mb-3 text-xl font-bold leading-relaxed text-rose-950 italic">
                                "{{ dailyQuote.quote }}"
                            </p>
                            <p
                                v-if="dailyQuote.author"
                                class="mt-auto text-sm font-semibold text-rose-900/70"
                            >
                                — {{ dailyQuote.author }}
                            </p>
                        </div>
                    </div>
                    <!-- Skeleton for Quote -->
                    <div v-else-if="isLoading" class="relative overflow-hidden rounded-[2rem] border border-gray-200 bg-white">
                        <SkeletonLoader variant="card" class="h-full" />
                    </div>
                </div>

                <!-- Smart Suggestions -->
                <div v-if="suggestions.length > 0 && !isLoading" class="space-y-3">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="text-2xl">💡</span>
                        Sugerencias para ti
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            v-for="(suggestion, index) in suggestions"
                            :key="index"
                            :href="suggestion.link"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-r shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
                            :class="suggestion.color"
                        >
                            <div class="absolute inset-0 bg-white/10 group-hover:bg-white/20 transition-all"></div>
                            <div class="relative p-5 flex items-center gap-4">
                                <span class="text-4xl transform group-hover:scale-110 transition-transform">{{ suggestion.icon }}</span>
                                <p class="text-white font-semibold text-sm flex-1">{{ suggestion.text }}</p>
                                <span class="text-white text-xl group-hover:translate-x-1 transition-transform">→</span>
                            </div>
                        </Link>
                    </div>
                </div>
                <!-- Skeleton for Suggestions -->
                <div v-else-if="isLoading" class="space-y-3">
                    <div class="flex items-center gap-2">
                        <SkeletonLoader variant="circular" size="lg" />
                        <SkeletonLoader variant="text" class="w-48 h-6" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <SkeletonLoader v-for="i in 3" :key="i" variant="card" class="h-24" />
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-2xl">📊</span>
                        Tus Estadísticas
                    </h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <StatCard
                            v-for="card in statCards"
                            :key="card.title"
                            :title="card.title"
                            :value="card.value"
                            :icon="card.icon"
                            :trend="card.trend"
                            :trend-label="card.trendLabel"
                            :color="card.color"
                            :href="card.link"
                        />
                    </div>
                </div>

                <!-- Today's Entry + Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Today's Entry -->
                    <div
                        v-if="todayEntry"
                        class="lg:col-span-2 relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-100 via-rose-50 to-purple-50 shadow-2xl border-2 border-pink-200"
                    >
                        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-300/30 to-purple-300/30 rounded-full -mr-20 -mt-20"></div>
                        <div class="relative p-8">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl">📖</span>
                                    <h3 class="text-2xl font-bold text-gray-800">
                                        Entrada de Hoy
                                    </h3>
                                </div>
                                <Link
                                    :href="route('diary.show', todayEntry.id)"
                                    class="px-5 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full text-sm font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
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
                                <span class="text-4xl">{{ todayEntry.mood }}</span>
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
                    <div
                        v-else
                        class="lg:col-span-2 relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-100 via-rose-50 to-purple-50 shadow-2xl border-2 border-pink-200 text-center p-8"
                    >
                        <span class="text-6xl block mb-4">✨</span>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">¿Cómo fue tu día?</h3>
                        <p class="text-gray-600 mb-6">Crea una entrada en tu diario para recordar este día especial</p>
                        <Link
                            :href="route('diary.create')"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-1 transition-all"
                        >
                            <span class="mr-2">✨</span>
                            Crear Entrada
                        </Link>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-3xl shadow-2xl border-2 border-gray-100 overflow-hidden">
                        <div class="p-5 bg-gradient-to-r from-pink-500 to-rose-500">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>⚡</span>
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <Link
                                v-for="action in quickActions"
                                :key="action.label"
                                :href="action.route"
                                class="group flex items-center gap-3 p-4 rounded-xl transition-all border-2 border-transparent hover:shadow-lg transform hover:-translate-y-1"
                                :class="[action.gradient, action.hoverGradient, action.border]"
                            >
                                <span class="text-3xl transform group-hover:scale-110 transition-transform">{{ action.icon }}</span>
                                <span class="font-semibold text-gray-700 flex-1">{{ action.label }}</span>
                            </Link>

                            <div v-if="activeHabitsQuick && activeHabitsQuick.length > 0" class="pt-2">
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">Atajos de hábitos</p>
                                <div class="space-y-2">
                                    <div
                                        v-for="habit in activeHabitsQuick"
                                        :key="habit.id"
                                        class="flex items-center gap-2 p-2 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border border-emerald-200"
                                    >
                                        <Link
                                            :href="route('habits.show', habit.id)"
                                            class="flex items-center gap-2 flex-1 min-w-0"
                                        >
                                            <span class="text-xl">{{ habit.icon || '🔄' }}</span>
                                            <span class="font-medium text-gray-700 truncate">{{ habit.name }}</span>
                                        </Link>
                                        <button
                                            @click="toggleHabitToday(habit.id)"
                                            class="px-2 py-1 text-xs font-semibold rounded-md bg-emerald-500 text-white hover:bg-emerald-600 transition-colors"
                                        >
                                            Hoy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Todos & Upcoming Events -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Pending Todos -->
                    <div
                        v-if="pendingTodos && pendingTodos.length > 0"
                        class="bg-white rounded-3xl shadow-2xl border-2 border-gray-100 overflow-hidden"
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
                        <div class="p-6 max-h-80 overflow-y-auto space-y-3">
                            <div
                                v-for="todo in pendingTodos"
                                :key="todo.id"
                                class="flex items-center gap-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border-2 border-amber-100 hover:border-amber-300 transition-all"
                            >
                                <input
                                    type="checkbox"
                                    :checked="todo.is_completed"
                                    @change="toggleTodo(todo.id)"
                                    class="w-5 h-5 text-pink-500 rounded focus:ring-pink-500 cursor-pointer"
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
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold"
                                >
                                    Alta
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div
                        v-if="upcomingEvents && upcomingEvents.length > 0"
                        class="bg-white rounded-3xl shadow-2xl border-2 border-gray-100 overflow-hidden"
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
                        <div class="p-6 max-h-80 overflow-y-auto space-y-3">
                            <Link
                                v-for="event in upcomingEvents"
                                :key="event.id"
                                :href="route('events.show', event.id)"
                                class="block p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-100 hover:border-purple-300 hover:shadow-lg transition-all transform hover:-translate-y-1"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-4 h-4 rounded-full shadow-md"
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
                    class="bg-white rounded-3xl shadow-2xl border-2 border-gray-100 overflow-hidden"
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
                    <div class="p-6 max-h-96 overflow-y-auto space-y-4">
                        <Link
                            v-for="entry in recentEntries"
                            :key="entry.id"
                            :href="route('diary.show', entry.id)"
                            class="group block p-5 bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl border-2 border-pink-100 hover:border-pink-300 hover:shadow-lg transition-all transform hover:-translate-y-1"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">{{ entry.mood }}</span>
                                        <h4 class="font-bold text-gray-900 text-lg">
                                            {{ entry.title }}
                                        </h4>
                                        <span
                                            v-if="entry.is_favorite"
                                            class="text-yellow-500 text-xl"
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
        </div>
    </AuthenticatedLayout>
</template>
