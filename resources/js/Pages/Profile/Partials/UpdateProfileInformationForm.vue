<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-semibold text-gray-900">
                Informacion del perfil
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Actualiza tu nombre y correo para mantener tu cuenta al dia.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Nombre" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Correo" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border border-rose-200 bg-white/85 px-4 py-2.5 shadow-sm shadow-rose-500/10 focus:border-rose-400 focus:ring-2 focus:ring-rose-300"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Tu correo electronico aun no esta verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-rose-600 underline hover:text-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-300 focus:ring-offset-2"
                    >
                        Haz clic aqui para reenviar el correo de verificacion.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-600"
                >
                    Se envio un nuevo enlace de verificacion a tu correo.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-rose-700"
                    >
                        Guardado.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
