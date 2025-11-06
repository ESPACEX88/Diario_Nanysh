<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    content: '',
    mood: '😊',
    date: new Date().toISOString().split('T')[0],
});

const submit = () => {
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
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-white to-pink-50 shadow-xl border-2 border-pink-100">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-200/30 to-purple-200/30 rounded-full -mr-20 -mt-20"></div>
                    <form @submit.prevent="submit" class="relative p-8 space-y-6">
                        <div>
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Título
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div>
                            <label
                                for="date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Fecha
                            </label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="form.errors.date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.date }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Estado de Ánimo
                                <span class="text-xs text-gray-500 ml-2">
                                    💡 Los estados felices (😊, 😍, 🥳) te dan fichitas para Snoopy
                                </span>
                            </label>
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800 font-semibold">
                                    💰 Estados felices = Fichitas para alimentar a Snoopy
                                </p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <button
                                    v-for="mood in moods"
                                    :key="mood"
                                    type="button"
                                    :class="[
                                        'text-4xl p-3 rounded-xl border-2 transition-all transform hover:scale-110 relative',
                                        form.mood === mood
                                            ? 'border-pink-500 bg-gradient-to-br from-pink-100 to-rose-100 shadow-md scale-110'
                                            : 'border-pink-200 hover:border-pink-300 bg-white hover:bg-pink-50',
                                        ['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood)
                                            ? 'ring-2 ring-yellow-300'
                                            : ''
                                    ]"
                                    @click="form.mood = mood"
                                    :title="['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood) ? '¡Este estado te da fichitas! 💰' : 'Este estado no da fichitas'"
                                >
                                    {{ mood }}
                                    <span
                                        v-if="['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'].includes(mood)"
                                        class="absolute -top-1 -right-1 text-xs bg-yellow-400 rounded-full w-5 h-5 flex items-center justify-center"
                                    >
                                        💰
                                    </span>
                                </button>
                            </div>
                            <div
                                v-if="form.errors.mood"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.mood }}
                            </div>
                        </div>

                        <div>
                            <label
                                for="content"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Contenido
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="10"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <div
                                v-if="form.errors.content"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.content }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a
                                :href="route('diary.index')"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 border border-transparent rounded-full font-bold text-sm text-white shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transform hover:-translate-y-0.5 transition-all disabled:opacity-50"
                            >
                                <span class="mr-2">💕</span>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

