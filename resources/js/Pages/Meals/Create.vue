<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    description: '',
    recipe: '',
    type: 'lunch',
    prep_time: null as number | null,
    cook_time: null as number | null,
    servings: null as number | null,
    rating: null as number | null,
});

const submit = () => {
    form.post(route('meals.store'));
};
</script>

<template>
    <Head title="Nueva Comida Favorita" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent flex items-center gap-2">
                <span>🍽️</span>
                Agregar Comida Favorita
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-4 border-pink-200 shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-pink-200/30 to-rose-200/30 rounded-full -mr-32 -mt-32"></div>
                    <div class="relative p-8">
                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <InputLabel for="name" value="Nombre de la comida *" class="text-pink-700 font-semibold" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-2 block w-full border-pink-300 focus:border-pink-500 focus:ring-pink-500 rounded-xl"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    placeholder="Ej: Pastel de chocolate..."
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="type" value="Tipo *" class="text-pink-700 font-semibold" />
                                <select
                                    id="type"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.type"
                                    required
                                >
                                    <option value="breakfast">🌅 Desayuno</option>
                                    <option value="lunch">🍽️ Almuerzo</option>
                                    <option value="dinner">🌙 Cena</option>
                                    <option value="snack">🍪 Snack</option>
                                    <option value="dessert">🍰 Postre</option>
                                    <option value="drink">🥤 Bebida</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="description" value="Descripción" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="description"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Describe esta comida..."
                                ></textarea>
                            </div>

                            <div class="mb-6">
                                <InputLabel for="recipe" value="Receta" class="text-pink-700 font-semibold" />
                                <textarea
                                    id="recipe"
                                    class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    v-model="form.recipe"
                                    rows="6"
                                    placeholder="Escribe la receta completa..."
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div>
                                    <InputLabel for="prep_time" value="Tiempo prep (min)" class="text-pink-700 font-semibold" />
                                    <input
                                        id="prep_time"
                                        type="number"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        :value="form.prep_time?.toString() || ''"
                                        @input="form.prep_time = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                    />
                                    <InputError class="mt-2" :message="form.errors.prep_time" />
                                </div>
                                <div>
                                    <InputLabel for="cook_time" value="Tiempo cocción (min)" class="text-pink-700 font-semibold" />
                                    <input
                                        id="cook_time"
                                        type="number"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        :value="form.cook_time?.toString() || ''"
                                        @input="form.cook_time = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                    />
                                    <InputError class="mt-2" :message="form.errors.cook_time" />
                                </div>
                                <div>
                                    <InputLabel for="servings" value="Porciones" class="text-pink-700 font-semibold" />
                                    <input
                                        id="servings"
                                        type="number"
                                        class="mt-2 block w-full rounded-xl border-pink-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                        :value="form.servings?.toString() || ''"
                                        @input="form.servings = ($event.target as HTMLInputElement).value ? parseInt(($event.target as HTMLInputElement).value) : null"
                                    />
                                    <InputError class="mt-2" :message="form.errors.servings" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton
                                    class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <span class="mr-2">🍽️</span>
                                    Guardar Comida
                                </PrimaryButton>
                                <Link
                                    :href="route('meals.index')"
                                    class="text-gray-600 hover:text-pink-600 font-semibold transition-colors"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

