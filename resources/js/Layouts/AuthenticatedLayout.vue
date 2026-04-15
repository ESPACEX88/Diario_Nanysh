<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';

const showingNavigationDropdown = ref(false);
const { isDark, toggleDarkMode } = useDarkMode();
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(244,114,182,0.16),_transparent_30%),linear-gradient(180deg,_#fff8fb_0%,_#fffdfd_45%,_#fdf2f8_100%)] dark:bg-[radial-gradient(circle_at_top,_rgba(244,114,182,0.14),_transparent_30%),linear-gradient(180deg,_#120816_0%,_#1a1020_50%,_#0f172a_100%)]">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-28 top-0 h-96 w-96 rounded-full bg-rose-300/20 blur-3xl"></div>
            <div class="absolute right-0 top-24 h-[28rem] w-[28rem] rounded-full bg-fuchsia-300/16 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-[32rem] w-[32rem] -translate-x-1/2 rounded-full bg-pink-200/20 blur-3xl"></div>
        </div>

        <div class="relative z-10 min-h-screen">
            <nav
                class="sticky top-0 z-50 border-b border-rose-200/70 bg-white/80 shadow-[0_10px_30px_rgba(236,72,153,0.08)] backdrop-blur-xl dark:border-fuchsia-900/50 dark:bg-slate-950/70"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-rose-500"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-2 sm:space-x-4 sm:-my-px sm:ms-10 sm:flex items-center flex-wrap"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('dashboard') }"
                                    aria-label="Ir al dashboard"
                                >
                                    <span class="hidden sm:inline" aria-hidden="true">🏠 </span>Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('diary.index')"
                                    :active="route().current('diary.*')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('diary.*') }"
                                >
                                    <span class="hidden sm:inline">📖 </span>Diario
                                </NavLink>
                                <NavLink
                                    :href="route('todos.index')"
                                    :active="route().current('todos.*')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('todos.*') }"
                                >
                                    <span class="hidden sm:inline">✅ </span>Tareas
                                </NavLink>
                                <NavLink
                                    :href="route('events.index')"
                                    :active="route().current('events.*')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('events.*') }"
                                >
                                    <span class="hidden sm:inline">📅 </span>Eventos
                                </NavLink>
                                <NavLink
                                    :href="route('pet.index')"
                                    :active="route().current('pet.*')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('pet.*') }"
                                >
                                    <span class="hidden sm:inline">🐕 </span>Snoopy
                                </NavLink>
                                <NavLink
                                    :href="route('minigame.doors')"
                                    :active="route().current('minigame.*')"
                                    class="text-pink-700 hover:text-pink-900 font-semibold px-2 sm:px-3 py-2 rounded-lg transition-all hover:bg-pink-50 text-sm sm:text-base"
                                    :class="{ 'bg-pink-100 shadow-sm': route().current('minigame.*') }"
                                >
                                    <span class="hidden sm:inline">🎰 </span>Minijuegos
                                </NavLink>
                                
                                <!-- Dropdown Menu -->
                                <div class="relative group">
                                    <button
                                        class="flex items-center gap-1 rounded-full border border-rose-200 bg-white/80 px-4 py-2 font-semibold text-rose-700 transition hover:-translate-y-px hover:bg-rose-50 hover:text-rose-900"
                                    >
                                        Más
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute left-0 z-50 mt-3 w-80 overflow-hidden rounded-[1.75rem] border border-rose-100/80 bg-white/95 p-3 shadow-[0_24px_70px_rgba(236,72,153,0.14)] opacity-0 invisible transition-all duration-200 group-hover:visible group-hover:opacity-100 backdrop-blur-xl">
                                        <div class="space-y-3">
                                            <!-- Sección: Registros y Seguimiento -->
                                            <div class="mb-3">
                                                <div class="mb-1 px-3 py-2 text-xs font-bold uppercase tracking-[0.28em] text-rose-500">
                                                    📝 Registros
                                                </div>
                                                <div class="space-y-1">
                                                    <Link
                                                        :href="route('dreams.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        🌙 Sueños
                                                    </Link>
                                                    <Link
                                                        :href="route('photos.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        📸 Fotos
                                                    </Link>
                                                    <Link
                                                        :href="route('cycle.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        🌸 Ciclo
                                                    </Link>
                                                    <Link
                                                        :href="route('habits.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        🔄 Hábitos
                                                    </Link>
                                                    <Link
                                                        :href="route('workouts.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        💪 Gym
                                                    </Link>
                                                </div>
                                            </div>

                                            <!-- Sección: Entretenimiento -->
                                            <div class="mb-3">
                                                <div class="mb-1 px-3 py-2 text-xs font-bold uppercase tracking-[0.28em] text-rose-500">
                                                    🎬 Entretenimiento
                                                </div>
                                                <div class="space-y-1">
                                                    <Link
                                                        :href="route('media.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        📚 Libros/Películas
                                                    </Link>
                                                    <Link
                                                        :href="route('wishlist.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        💝 Lista de Deseos
                                                    </Link>
                                                </div>
                                            </div>

                                            <!-- Sección: Cocina -->
                                            <div class="mb-3">
                                                <div class="mb-1 px-3 py-2 text-xs font-bold uppercase tracking-[0.28em] text-rose-500">
                                                    🍳 Cocina
                                                </div>
                                                <div class="space-y-1">
                                                    <Link
                                                        :href="route('recipes.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        📖 Recetario
                                                    </Link>
                                                    <Link
                                                        :href="route('meals.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        🍽️ Comidas Favoritas
                                                    </Link>
                                                </div>
                                            </div>

                                            <!-- Sección: Progreso -->
                                            <div class="mb-3">
                                                <div class="mb-1 px-3 py-2 text-xs font-bold uppercase tracking-[0.28em] text-rose-500">
                                                    🎯 Progreso
                                                </div>
                                                <div class="space-y-1">
                                                    <Link
                                                        :href="route('achievements.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        🏆 Logros
                                                    </Link>
                                                    <Link
                                                        :href="route('counters.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        📅 Contadores
                                                    </Link>
                                                    <Link
                                                        :href="route('statistics')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        📊 Estadísticas
                                                    </Link>
                                                </div>
                                            </div>

                                            <!-- Sección: Configuración -->
                                            <div class="mt-2 border-t border-rose-100 pt-2">
                                                <div class="space-y-1">
                                                    <button
                                                        @click="toggleDarkMode"
                                                        class="block w-full rounded-2xl px-3 py-2 text-left text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        {{ isDark ? '☀️ Modo Claro' : '🌙 Modo Oscuro' }}
                                                    </button>
                                                    <Link
                                                        :href="route('export.index')"
                                                        class="block rounded-2xl px-3 py-2 text-sm font-semibold text-rose-800 transition-all hover:bg-rose-50 hover:text-rose-950"
                                                    >
                                                        💾 Exportar Datos
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white/90 px-4 py-2 text-sm font-medium leading-4 text-rose-700 shadow-sm shadow-rose-500/10 transition duration-150 ease-in-out hover:-translate-y-px hover:text-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                                            >
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-rose-400 to-fuchsia-500 text-xs font-semibold text-white">
                                                    {{ $page.props.auth.user.name.charAt(0) }}
                                                </span>
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white/90 p-2.5 text-rose-500 shadow-sm shadow-rose-500/10 transition duration-150 ease-in-out hover:-translate-y-px hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                                :aria-expanded="showingNavigationDropdown"
                                aria-label="Abrir menú de navegación"
                                aria-controls="mobile-menu"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    id="mobile-menu"
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                    role="menu"
                    aria-label="Menú de navegación móvil"
                >
                        <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            🏠 Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('diary.index')"
                            :active="route().current('diary.*')"
                        >
                            📖 Diario
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('todos.index')"
                            :active="route().current('todos.*')"
                        >
                            ✅ Tareas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('events.index')"
                            :active="route().current('events.*')"
                        >
                            📅 Eventos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('pet.index')"
                            :active="route().current('pet.*')"
                        >
                            🐕 Snoopy
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('minigame.doors')"
                            :active="route().current('minigame.*')"
                        >
                            🎰 Minijuegos
                        </ResponsiveNavLink>
                        <!-- Registros -->
                        <div class="px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            📝 Registros
                        </div>
                        <ResponsiveNavLink
                            :href="route('dreams.index')"
                            :active="route().current('dreams.*')"
                        >
                            🌙 Sueños
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('photos.index')"
                            :active="route().current('photos.*')"
                        >
                            📸 Fotos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('cycle.index')"
                            :active="route().current('cycle.*')"
                        >
                            🌸 Ciclo
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('habits.index')"
                            :active="route().current('habits.*')"
                        >
                            🔄 Hábitos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('workouts.index')"
                            :active="route().current('workouts.*')"
                        >
                            💪 Gym
                        </ResponsiveNavLink>
                        
                        <!-- Entretenimiento -->
                        <div class="px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            🎬 Entretenimiento
                        </div>
                        <ResponsiveNavLink
                            :href="route('media.index')"
                            :active="route().current('media.*')"
                        >
                            📚 Libros/Películas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('wishlist.index')"
                            :active="route().current('wishlist.*')"
                        >
                            💝 Lista de Deseos
                        </ResponsiveNavLink>
                        
                        <!-- Cocina -->
                        <div class="px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            🍳 Cocina
                        </div>
                        <ResponsiveNavLink
                            :href="route('recipes.index')"
                            :active="route().current('recipes.*')"
                        >
                            📖 Recetario
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('meals.index')"
                            :active="route().current('meals.*')"
                        >
                            🍽️ Comidas Favoritas
                        </ResponsiveNavLink>
                        
                        <!-- Progreso -->
                        <div class="px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            🎯 Progreso
                        </div>
                        <ResponsiveNavLink
                            :href="route('achievements.index')"
                            :active="route().current('achievements.*')"
                        >
                            🏆 Logros
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('counters.index')"
                            :active="route().current('counters.*')"
                        >
                            📅 Contadores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('statistics')"
                            :active="route().current('statistics')"
                        >
                            📊 Estadísticas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('recipes.index')"
                            :active="route().current('recipes.*')"
                        >
                            📖 Recetario
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-rose-100/80 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-semibold text-rose-900"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-rose-700/70">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="sticky top-16 z-40 border-b border-rose-100/80 bg-white/70 shadow-[0_10px_30px_rgba(236,72,153,0.05)] backdrop-blur-xl"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="relative z-10">
                <slot />
            </main>
        </div>
    </div>
</template>
