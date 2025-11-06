<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    pin: '',
});

// Detectar si es de día o de noche
const isDaytime = ref(true);

const updateTimeOfDay = () => {
    const hour = new Date().getHours();
    isDaytime.value = hour >= 6 && hour < 20; // 6 AM a 8 PM es día
};

onMounted(() => {
    updateTimeOfDay();
    // Actualizar cada minuto
    setInterval(updateTimeOfDay, 60000);
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 12) {
        return 'Buenos días';
    } else if (hour >= 12 && hour < 19) {
        return 'Buenas tardes';
    } else {
        return 'Buenas noches';
    }
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('pin');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Diario de Nanysh - Bienvenida" />

        <div
            class="min-h-screen flex items-center justify-center px-4"
            :class="
                isDaytime
                    ? 'bg-gradient-to-br from-pink-50 via-rose-50 to-pink-100'
                    : 'bg-gradient-to-br from-pink-900 via-rose-900 to-pink-800'
            "
        >
            <div class="w-full max-w-md">
                <!-- Card principal -->
                <div
                    class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-2xl p-8 border border-pink-200/50"
                    :class="
                        !isDaytime
                            ? 'bg-pink-900/90 border-pink-700/50'
                            : ''
                    "
                >
                    <!-- Título y saludo -->
                    <div class="text-center mb-8">
                        <h1
                            class="text-4xl font-bold mb-2 bg-gradient-to-r from-pink-500 to-rose-500 bg-clip-text text-transparent"
                            :class="!isDaytime ? 'from-pink-300 to-rose-300' : ''"
                        >
                            Diario de Nanysh
                        </h1>
                        <p
                            class="text-xl text-pink-600 font-medium"
                            :class="!isDaytime ? 'text-pink-200' : ''"
                        >
                            {{ greeting }}, Bienvenida
                        </p>
                        <div class="mt-4">
                            <span
                                class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-full"
                                :class="
                                    isDaytime
                                        ? 'bg-pink-100 text-pink-700'
                                        : 'bg-pink-800/50 text-pink-200'
                                "
                            >
                                <svg
                                    v-if="isDaytime"
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                                    />
                                </svg>
                                <span v-if="isDaytime">Es de día</span>
                                <span v-else>Es de noche</span>
                            </span>
                        </div>
                    </div>

                    <!-- Mensaje de estado -->
                    <div
                        v-if="status"
                        class="mb-4 text-sm font-medium text-green-600 text-center"
                        :class="!isDaytime ? 'text-green-400' : ''"
                    >
                        {{ status }}
                    </div>

                    <!-- Formulario de PIN -->
                    <form @submit.prevent="submit">
                        <div>
                            <label
                                for="pin"
                                class="block text-sm font-medium mb-2"
                                :class="
                                    isDaytime
                                        ? 'text-pink-700'
                                        : 'text-pink-200'
                                "
                            >
                                Ingresa tu PIN
                            </label>

                            <TextInput
                                id="pin"
                                type="password"
                                class="mt-1 block w-full text-center text-2xl tracking-widest font-semibold"
                                :class="
                                    form.errors.pin
                                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                        : 'border-pink-300 focus:border-pink-500 focus:ring-pink-500'
                                "
                                v-model="form.pin"
                                required
                                autofocus
                                maxlength="5"
                                placeholder="•••••"
                                inputmode="numeric"
                                pattern="[0-9]*"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.pin"
                            />
                        </div>

                        <div class="mt-6">
                            <PrimaryButton
                                class="w-full py-3 text-lg font-semibold bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 focus:ring-pink-500"
                                :class="{
                                    'opacity-25': form.processing,
                                }"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">
                                    Ingresando...
                                </span>
                                <span v-else>Ingresar</span>
                            </PrimaryButton>
                        </div>
                    </form>

                    <!-- Decoración -->
                    <div class="mt-8 text-center">
                        <div
                            class="inline-flex items-center gap-2 text-xs"
                            :class="
                                isDaytime
                                    ? 'text-pink-400'
                                    : 'text-pink-500'
                            "
                        >
                            <svg
                                class="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>Tu diario personal y seguro</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
