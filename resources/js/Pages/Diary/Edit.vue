<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
    entry: {
        id: number;
        title: string;
        content: string;
        mood: string;
        date: string;
    };
}

const props = defineProps<Props>();

const form = useForm({
    title: props.entry.title,
    content: props.entry.content,
    mood: props.entry.mood,
    date: props.entry.date,
});

const submit = () => {
    form.put(route('diary.update', props.entry.id));
};

const moods = [
    // Happy moods (give coins)
    '😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙',
    // Neutral/Sad moods (no coins)
    '😢', '😡', '😰', '😨', '😭', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '😤', '😠', '😦', '😧', '😱', '😳', '😵', '😶', '😐', '😑', '😯', '🤔', '😴'
];
</script>

<template>
    <Head title="Editar Entrada" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Editar Entrada
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
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
                            <Link
                                :href="route('diary.show', entry.id)"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            >
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

