<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    habit: any;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.habit.name,
    description: props.habit.description || '',
    frequency: props.habit.frequency || 'daily',
    color: props.habit.color || '#3b82f6',
    icon: props.habit.icon || '🔄',
    is_active: props.habit.is_active ?? true,
});

const submit = () => {
    form.put(route('habits.update', props.habit.id));
};
</script>

<template>
    <Head title="Editar Hábito" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="bg-gradient-to-r from-rose-600 to-fuchsia-600 bg-clip-text text-xl font-semibold leading-tight text-transparent">
                Editar Hábito
            </h2>
        </template>

        <div class="py-10 sm:py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="feminine-panel overflow-hidden p-0">
                    <form @submit.prevent="submit" class="space-y-6 p-6 sm:p-8">
                        <div class="rounded-2xl border border-rose-100/80 bg-white/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-rose-500">Ajusta tu rutina</p>
                            <p class="mt-1 text-sm text-rose-700">Edita nombre y descripcion para mantener tu habito claro y accionable.</p>
                        </div>

                        <div>
                            <label
                                for="name"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Nombre
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>
                        <div>
                            <label
                                for="description"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Descripción
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-2xl border border-rose-200 bg-white/85 px-4 py-3 text-gray-900 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                            />
                        </div>
                        <div class="flex items-center rounded-xl border border-rose-100 bg-white/75 px-3 py-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-rose-300 text-rose-600 shadow-sm focus:border-rose-400 focus:ring-rose-300"
                            />
                            <label
                                for="is_active"
                                class="ml-2 text-sm font-medium text-gray-700"
                            >
                                Activo
                            </label>
                        </div>
                        <div class="flex items-center justify-end gap-4 border-t border-rose-100 pt-4">
                            <Link
                                :href="route('habits.show', props.habit.id)"
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

