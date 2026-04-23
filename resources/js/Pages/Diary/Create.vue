<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TagSelector from '@/Components/TagSelector.vue';

interface Props {
    tags?: Array<{
        id: number;
        name: string;
        color: string;
    }>;
}

const props = defineProps<Props>();

// Función para obtener la fecha local en formato YYYY-MM-DD
const getLocalDate = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const form = useForm({
    title: '',
    content: '',
    mood: '😊',
    date: getLocalDate(),
    tags: [] as number[],
});

const submit = () => {
    // Asegurar que la fecha se envíe en formato YYYY-MM-DD sin conversión de zona horaria
    if (form.date) {
        const dateParts = form.date.split('T')[0].split('-');
        if (dateParts.length === 3) {
            form.date = `${dateParts[0]}-${dateParts[1]}-${dateParts[2]}`;
        }
    }
    form.post(route('diary.store'));
};

const moods = [
    // Happy moods (give coins)
    '😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙',
    // Neutral/Sad moods (no coins)
    '😢', '😡', '😰', '😨', '😭', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '😤', '😠', '😦', '😧', '😱', '😳', '😵', '😶', '😐', '😑', '😯', '🤔', '😴'
];
</script>

<template>
    <Head title="Nueva Entrada" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-2xl">✨</span>
                <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    Nueva Entrada del Diario
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="feminine-panel relative overflow-hidden p-0">
                    <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-gradient-to-br from-rose-200/35 to-fuchsia-200/30 blur-sm"></div>
                    <form @submit.prevent="submit" class="relative space-y-6 p-8">
                        <div class="rounded-2xl border border-rose-100/80 bg-white/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Entrada del día</p>
                            <p class="mt-1 text-sm text-rose-700">Usa un título corto, elige tu mood y escribe lo más importante que quieras recordar.</p>
                        </div>
                        <!-- Primera fila: Título y Fecha -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label
                                    for="title"
                                    class="block text-sm font-semibold text-gray-800 mb-2"
                                >
                                    Título *
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    autocomplete="off"
                                    class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                    placeholder="¿Qué pasó hoy?"
                                    aria-required="true"
                                    aria-describedby="title-error"
                                />
                                <div
                                    id="title-error"
                                    v-if="form.errors.title"
                                    class="mt-1 text-sm text-red-600 font-medium"
                                    role="alert"
                                    aria-live="polite"
                                >
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <div>
                                <label
                                    for="date"
                                    class="block text-sm font-semibold text-gray-800 mb-2"
                                >
                                    Fecha *
                                </label>
                                <input
                                    id="date"
                                    v-model="form.date"
                                    type="date"
                                    required
                                    class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                />
                                <div
                                    v-if="form.errors.date"
                                    class="mt-1 text-sm text-red-600 font-medium"
                                >
                                    {{ form.errors.date }}
                                </div>
                            </div>
                        </div>

                        <!-- Segunda fila: Estado de Ánimo -->
                        <div class="mb-6">
                            <label
                                class="block text-sm font-semibold text-gray-800 mb-3"
                            >
                                Estado de Ánimo *
                            </label>
                            <div class="mb-3 rounded-xl border border-yellow-200 bg-gradient-to-r from-yellow-50 to-amber-50 p-3">
                                <p class="text-sm text-amber-900 font-semibold">
                                    💰 Los estados felices te dan fichitas para Snoopy
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2 rounded-2xl border border-rose-100 bg-white/70 p-4">
                                <button
                                    v-for="mood in moods"
                                    :key="mood"
                                    type="button"
                                    :class="[
                                        'relative rounded-xl border p-2.5 text-3xl transition-all hover:scale-110',
                                        form.mood === mood
                                            ? 'scale-110 border-rose-500 bg-gradient-to-br from-rose-200 to-fuchsia-200 shadow-lg ring-2 ring-rose-300'
                                            : 'border-rose-200 bg-white hover:border-rose-400 hover:bg-rose-50',
                                        ['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood)
                                            ? 'ring-2 ring-yellow-400'
                                            : ''
                                    ]"
                                    @click="form.mood = mood"
                                    :title="['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood) ? '¡Este estado te da fichitas! 💰' : 'Este estado no da fichitas'"
                                >
                                    {{ mood }}
                                    <span
                                        v-if="['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood)"
                                        class="absolute -top-1 -right-1 text-xs bg-yellow-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold"
                                    >
                                        💰
                                    </span>
                                </button>
                            </div>
                            <div
                                v-if="form.errors.mood"
                                class="mt-1 text-sm text-red-600 font-medium"
                            >
                                {{ form.errors.mood }}
                            </div>
                        </div>

                        <!-- Tercera fila: Contenido -->
                        <div class="mb-6">
                            <label
                                for="content"
                                class="block text-sm font-semibold text-gray-800 mb-2"
                            >
                                Contenido *
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="8"
                                required
                                autocomplete="off"
                                class="mt-1 block w-full resize-none rounded-2xl border border-rose-200 bg-white/85 px-4 py-3 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                                placeholder="Escribe sobre tu día..."
                                aria-required="true"
                                aria-describedby="content-error"
                            />
                            <div
                                id="content-error"
                                v-if="form.errors.content"
                                class="mt-1 text-sm text-red-600 font-medium"
                                role="alert"
                                aria-live="polite"
                            >
                                {{ form.errors.content }}
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-6">
                            <TagSelector
                                v-model="form.tags"
                                :existing-tags="props.tags || []"
                            />
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-4 border-t border-rose-100 pt-4">
                            <a
                                :href="route('diary.index')"
                                class="rounded-xl px-6 py-2.5 font-semibold text-gray-700 transition-colors hover:bg-rose-50 hover:text-rose-800"
                            >
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-xl border border-transparent bg-gradient-to-r from-rose-500 to-fuchsia-500 px-8 py-3 font-bold text-white shadow-lg shadow-rose-400/30 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span class="mr-2">💕</span>
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Entrada</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

