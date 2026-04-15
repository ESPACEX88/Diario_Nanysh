<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Bienvenida" />

    <div class="relative min-h-screen overflow-hidden text-slate-800">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(244,114,182,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(192,132,252,0.14),_transparent_28%),linear-gradient(180deg,_#fff8fb_0%,_#fffdfd_45%,_#fdf2f8_100%)]"></div>
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 top-10 h-80 w-80 rounded-full bg-rose-300/20 blur-3xl"></div>
            <div class="absolute right-0 top-24 h-96 w-96 rounded-full bg-fuchsia-300/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-pink-200/20 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="flex items-center justify-between rounded-full border border-white/70 bg-white/75 px-5 py-3 shadow-[0_14px_40px_rgba(236,72,153,0.08)] backdrop-blur-xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-rose-500 to-fuchsia-500 text-lg font-bold text-white shadow-lg shadow-rose-500/20">
                        DN
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-rose-500">Diario de Nanysh</p>
                        <p class="text-xs text-rose-900/60">Tu espacio íntimo, bonito y seguro</p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-full border border-rose-200 bg-white/90 px-4 py-2 text-sm font-semibold text-rose-700 shadow-sm shadow-rose-500/10 transition hover:-translate-y-px hover:bg-rose-50 hover:text-rose-900"
                    >
                        Ir al dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full border border-rose-200 bg-white/90 px-4 py-2 text-sm font-semibold text-rose-700 shadow-sm shadow-rose-500/10 transition hover:-translate-y-px hover:bg-rose-50 hover:text-rose-900"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
                        >
                            Crear cuenta
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="flex flex-1 items-center py-10 sm:py-14">
                <div class="grid w-full items-center gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                    <section class="space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white/80 px-4 py-2 text-sm font-semibold text-rose-700 shadow-sm shadow-rose-500/10 backdrop-blur-xl">
                            <span>💗</span>
                            Un diario privado con diseño suave y elegante
                        </div>

                        <div class="max-w-2xl space-y-4">
                            <h1 class="text-4xl font-bold tracking-tight text-rose-950 sm:text-5xl lg:text-6xl">
                                Un espacio delicado para tus recuerdos, metas y pensamientos.
                            </h1>
                            <p class="max-w-xl text-lg leading-8 text-rose-900/70">
                                Guarda lo importante en un lugar íntimo, visualmente cálido y fácil de usar. Todo el sistema comparte la misma estética: rosa, fucsia, vidrio y bordes suaves.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-px hover:shadow-xl"
                            >
                                Comenzar
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center rounded-full border border-rose-200 bg-white/85 px-6 py-3 text-sm font-semibold text-rose-700 shadow-sm shadow-rose-500/10 transition hover:-translate-y-px hover:bg-rose-50 hover:text-rose-900"
                            >
                                Crear acceso
                            </Link>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <article class="rounded-3xl border border-rose-100/80 bg-white/80 p-5 shadow-[0_16px_40px_rgba(236,72,153,0.08)] backdrop-blur-xl">
                                <p class="text-2xl">✨</p>
                                <h2 class="mt-3 text-sm font-semibold text-rose-950">Íntimo</h2>
                                <p class="mt-2 text-sm leading-6 text-rose-900/70">Tu información queda ordenada y protegida.</p>
                            </article>
                            <article class="rounded-3xl border border-rose-100/80 bg-white/80 p-5 shadow-[0_16px_40px_rgba(236,72,153,0.08)] backdrop-blur-xl">
                                <p class="text-2xl">🌷</p>
                                <h2 class="mt-3 text-sm font-semibold text-rose-950">Suave</h2>
                                <p class="mt-2 text-sm leading-6 text-rose-900/70">Una interfaz amable, clara y sin ruido visual.</p>
                            </article>
                            <article class="rounded-3xl border border-rose-100/80 bg-white/80 p-5 shadow-[0_16px_40px_rgba(236,72,153,0.08)] backdrop-blur-xl">
                                <p class="text-2xl">💎</p>
                                <h2 class="mt-3 text-sm font-semibold text-rose-950">Completo</h2>
                                <p class="mt-2 text-sm leading-6 text-rose-900/70">Diario, metas, tareas, fotos y mucho más.</p>
                            </article>
                        </div>
                    </section>

                    <section class="feminine-surface relative overflow-hidden p-6 sm:p-8">
                        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-fuchsia-200/40 blur-3xl"></div>
                        <div class="absolute -left-10 bottom-0 h-40 w-40 rounded-full bg-rose-200/40 blur-3xl"></div>

                        <div class="relative space-y-5">
                            <div class="rounded-[2rem] border border-rose-100/80 bg-gradient-to-br from-white via-rose-50 to-fuchsia-50 p-6 shadow-inner">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-rose-500">Bienvenida</p>
                                <h2 class="mt-3 text-2xl font-bold text-rose-950">Tu diario, pero más bonito.</h2>
                                <p class="mt-3 text-sm leading-7 text-rose-900/70">
                                    Un diseño limpio para que escribir, revisar y navegar se sienta especial, no pesado.
                                </p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-rose-100 bg-white/85 p-4">
                                    <p class="text-sm font-semibold text-rose-950">Acceso rápido</p>
                                    <p class="mt-1 text-sm text-rose-900/65">Entra con un gesto simple y continúa tu día.</p>
                                </div>
                                <div class="rounded-2xl border border-rose-100 bg-white/85 p-4">
                                    <p class="text-sm font-semibold text-rose-950">Consistencia</p>
                                    <p class="mt-1 text-sm text-rose-900/65">Login, dashboard y formularios comparten la misma estética.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
</template>
