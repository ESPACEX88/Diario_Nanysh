<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TagSelector from '@/Components/TagSelector.vue';

interface Tag {
    id: number;
    name: string;
    color: string;
}

interface Props {
    entry: {
        id: number;
        title: string;
        content: string;
        mood: string;
        date: string;
        tags?: Tag[];
    };
    tags?: Tag[];
}

const props = defineProps<Props>();

const form = useForm({
    title: props.entry.title,
    content: props.entry.content,
    mood: props.entry.mood,
    date: props.entry.date,
    tags: props.entry.tags?.map(t => t.id) || [] as number[],
});

const submit = () => {
    // Asegurar que la fecha se envíe en formato YYYY-MM-DD sin conversión de zona horaria
    if (form.date) {
        const dateParts = form.date.split('T')[0].split('-');
        if (dateParts.length === 3) {
            form.date = `${dateParts[0]}-${dateParts[1]}-${dateParts[2]}`;
        }
    }
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
            <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                Editar Entrada
            </h2>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="feminine-panel overflow-hidden p-0">
                    <form @submit.prevent="submit" class="space-y-6 p-6 sm:p-8">
                        <div class="rounded-2xl border border-rose-100/80 bg-white/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Edicion consciente</p>
                            <p class="mt-1 text-sm text-rose-700">Reescribe tu entrada con calma y conserva lo que sentiste ese dia.</p>
                        </div>

                        <div>
                            <label
                                for="title"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Título
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
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
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Fecha
                            </label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700"
                            >
                                Estado de Ánimo
                                <span class="text-xs text-gray-500 ml-2">
                                    💡 Los estados felices (😊, 😍, 🥳) te dan fichitas para Snoopy
                                </span>
                            </label>
                            <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <p class="text-sm font-semibold text-amber-700">
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
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Contenido
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="10"
                                required
                                class="mt-1 block w-full rounded-2xl border border-rose-200 bg-white/85 px-4 py-3 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                            <div
                                v-if="form.errors.content"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.content }}
                            </div>
                        </div>

                        <div>
                            <TagSelector
                                v-model="form.tags"
                                :existing-tags="props.tags || []"
                            />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t border-rose-100 pt-4">
                            <Link
                                :href="route('diary.show', props.entry.id)"
                                class="rounded-xl px-4 py-2 text-gray-600 transition-colors hover:bg-rose-50 hover:text-rose-700"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600 disabled:opacity-50"
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

