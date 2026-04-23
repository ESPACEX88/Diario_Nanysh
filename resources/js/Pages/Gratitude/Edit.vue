<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    gratitude: any;
}

const props = defineProps<Props>();

const form = useForm({
    date: props.gratitude.date,
    item_one: props.gratitude.item_one,
    item_two: props.gratitude.item_two,
    item_three: props.gratitude.item_three,
    reflection: props.gratitude.reflection || '',
});

const submit = () => {
    form.put(route('gratitude.update', props.gratitude.id));
};
</script>

<template>
    <Head title="Editar Gratitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                Editar Gratitud
            </h2>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="feminine-panel overflow-hidden p-0">
                    <form @submit.prevent="submit" class="space-y-6 p-6 sm:p-8">
                        <div class="rounded-2xl border border-rose-100/80 bg-white/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Ajusta tu registro</p>
                            <p class="mt-1 text-sm text-rose-700">Edita tus tres gratitudes y deja una reflexión que te inspire mañana.</p>
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
                                for="item_one"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Por qué estoy agradecido #1
                            </label>
                            <input
                                id="item_one"
                                v-model="form.item_one"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>
                        <div>
                            <label
                                for="item_two"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Por qué estoy agradecido #2
                            </label>
                            <input
                                id="item_two"
                                v-model="form.item_two"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>
                        <div>
                            <label
                                for="item_three"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Por qué estoy agradecido #3
                            </label>
                            <input
                                id="item_three"
                                v-model="form.item_three"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>
                        <div>
                            <label
                                for="reflection"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Reflexión (opcional)
                            </label>
                            <textarea
                                id="reflection"
                                v-model="form.reflection"
                                rows="4"
                                class="mt-1 block w-full rounded-2xl border border-rose-200 bg-white/85 px-4 py-3 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t border-rose-100 pt-4">
                            <Link
                                :href="route('gratitude.show', props.gratitude.id)"
                                class="rounded-xl px-4 py-2 text-gray-600 transition-colors hover:bg-rose-50 hover:text-rose-700"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-2.5 font-semibold text-white shadow-lg shadow-rose-400/25 transition-all hover:-translate-y-0.5 hover:from-rose-600 hover:to-fuchsia-600 disabled:opacity-50"
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

