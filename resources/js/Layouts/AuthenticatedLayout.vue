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
    <div>
        <div class="min-h-screen bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <nav
                class="border-b-2 border-pink-200 dark:border-gray-700 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-lg sticky top-0 z-50"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
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
                                        class="text-pink-700 hover:text-pink-900 font-semibold flex items-center gap-1 px-3 py-2 rounded-lg hover:bg-pink-100 transition-all"
                                    >
                                        Más
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border-2 border-pink-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                        <div class="p-2 space-y-1">
                                            <Link
                                                :href="route('wishlist.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                💝 Lista de Deseos
                                            </Link>
                                            <Link
                                                :href="route('achievements.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                🏆 Logros
                                            </Link>
                                            <Link
                                                :href="route('counters.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                📅 Contadores
                                            </Link>
                                            <Link
                                                :href="route('dreams.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                🌙 Sueños
                                            </Link>
                                            <Link
                                                :href="route('media.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 font-semibold transition-all"
                                            >
                                                📚 Libros/Películas
                                            </Link>
                                            <button
                                                @click="toggleDarkMode"
                                                class="w-full text-left block px-4 py-3 rounded-lg hover:bg-pink-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 font-semibold transition-all"
                                            >
                                                {{ isDark ? '☀️ Modo Claro' : '🌙 Modo Oscuro' }}
                                            </button>
                                            <Link
                                                :href="route('statistics')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 font-semibold transition-all"
                                            >
                                                📊 Estadísticas
                                            </Link>
                                            <Link
                                                :href="route('export.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 font-semibold transition-all"
                                            >
                                                💾 Exportar Datos
                                            </Link>
                                            <Link
                                                :href="route('meals.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                🍽️ Comidas
                                            </Link>
                                            <Link
                                                :href="route('cycle.index')"
                                                class="block px-4 py-3 rounded-lg hover:bg-pink-50 text-gray-700 hover:text-pink-700 font-semibold transition-all"
                                            >
                                                🌸 Ciclo
                                            </Link>
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
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
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
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
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
                        <ResponsiveNavLink
                            :href="route('wishlist.index')"
                            :active="route().current('wishlist.*')"
                        >
                            💝 Lista de Deseos
                        </ResponsiveNavLink>
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
                            :href="route('dreams.index')"
                            :active="route().current('dreams.*')"
                        >
                            🌙 Sueños
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('media.index')"
                            :active="route().current('media.*')"
                        >
                            📚 Libros/Películas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('meals.index')"
                            :active="route().current('meals.*')"
                        >
                            🍽️ Comidas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('cycle.index')"
                            :active="route().current('cycle.*')"
                        >
                            🌸 Ciclo
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
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
                class="bg-white/90 backdrop-blur-sm shadow-md sticky top-16 z-40 border-b-2 border-pink-100"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
