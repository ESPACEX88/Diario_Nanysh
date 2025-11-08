<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    pin: '',
});

// Estado del PIN para mostrar/ocultar
const showPin = ref(false);
const pinInput = ref<HTMLInputElement | null>(null);

// Limpiar errores cuando el usuario escribe
watch(() => form.pin, () => {
    if (form.errors.pin) {
        form.clearErrors('pin');
    }
});

// Detectar si es de día o de noche
const isDaytime = ref(true);

const updateTimeOfDay = () => {
    const hour = new Date().getHours();
    isDaytime.value = hour >= 6 && hour < 20;
};

onMounted(() => {
    updateTimeOfDay();
    setInterval(updateTimeOfDay, 60000);
    
    // Enfocar el input al cargar
    setTimeout(() => {
        pinInput.value?.focus();
    }, 100);
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

// Manejar solo números
const handlePinInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let value = target.value.replace(/[^0-9]/g, ''); // Solo números
    value = value.slice(0, 5); // Máximo 5 dígitos
    form.pin = value;
    target.value = value;
};

// Manejar tecla Enter
const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && form.pin.length === 5 && !form.processing) {
        submit();
    }
};

const submit = () => {
    // Asegurar que el PIN tenga exactamente 5 dígitos
    if (form.pin.length !== 5) {
        form.setError('pin', 'El PIN debe tener 5 dígitos');
        return;
    }
    
    // Limpiar espacios y caracteres no numéricos
    form.pin = form.pin.replace(/[^0-9]/g, '').padStart(5, '0');
    
    form.post(route('login'), {
        onFinish: () => {
            // No limpiar el PIN si hay error, solo si fue exitoso
            if (!form.errors.pin) {
                form.reset('pin');
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Diario de Nanysh - Bienvenida" />

        <div
            class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8"
            :class="
                isDaytime
                    ? 'bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50'
                    : 'bg-gradient-to-br from-pink-900 via-rose-900 to-purple-900'
            "
        >
            <!-- Decoración de fondo -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-20 blur-3xl"
                    :class="isDaytime ? 'bg-pink-300' : 'bg-pink-600'"
                ></div>
                <div
                    class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full opacity-20 blur-3xl"
                    :class="isDaytime ? 'bg-rose-300' : 'bg-rose-600'"
                ></div>
            </div>

            <div class="w-full max-w-md relative z-10">
                <!-- Card principal -->
                <div
                    class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10 border-2 transition-all duration-300"
                    :class="
                        !isDaytime
                            ? 'bg-pink-900/95 border-pink-700/50'
                            : 'border-pink-200/50 hover:border-pink-300/50'
                    "
                >
                    <!-- Título y saludo -->
                    <div class="text-center mb-8">
                        <div class="mb-4">
                            <span class="text-6xl sm:text-7xl block mb-2 animate-pulse">💕</span>
                        </div>
                        <h1
                            class="text-3xl sm:text-4xl md:text-5xl font-bold mb-3 bg-gradient-to-r from-pink-500 via-rose-500 to-purple-500 bg-clip-text text-transparent"
                            :class="!isDaytime ? 'from-pink-300 via-rose-300 to-purple-300' : ''"
                        >
                            Diario de Nanysh
                        </h1>
                        <p
                            class="text-lg sm:text-xl text-pink-600 font-semibold mb-4"
                            :class="!isDaytime ? 'text-pink-200' : ''"
                        >
                            {{ greeting }}, Bienvenida
                        </p>
                        <div class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-full"
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
                        </div>
                    </div>

                    <!-- Mensaje de estado -->
                    <div
                        v-if="status"
                        class="mb-4 p-3 rounded-xl text-sm font-medium text-green-700 text-center bg-green-50 border border-green-200"
                        :class="!isDaytime ? 'text-green-300 bg-green-900/30 border-green-700/50' : ''"
                    >
                        {{ status }}
                    </div>

                    <!-- Formulario de PIN -->
                    <form @submit.prevent="submit">
                        <div>
                            <label
                                for="pin"
                                class="block text-sm font-semibold mb-3 text-center"
                                :class="
                                    isDaytime
                                        ? 'text-pink-700'
                                        : 'text-pink-200'
                                "
                            >
                                Ingresa tu PIN de 5 dígitos
                            </label>

                            <div class="relative">
                                <input
                                    ref="pinInput"
                                    id="pin"
                                    :type="showPin ? 'text' : 'password'"
                                    :value="form.pin"
                                    @input="handlePinInput"
                                    @keypress="handleKeyPress"
                                    class="block w-full text-center text-3xl sm:text-4xl tracking-[0.5em] font-bold rounded-xl border-2 transition-all focus:outline-none focus:ring-4"
                                    :class="
                                        form.errors.pin
                                            ? 'border-red-400 focus:border-red-500 focus:ring-red-200 bg-red-50 text-red-900'
                                            : isDaytime
                                            ? 'border-pink-300 focus:border-pink-500 focus:ring-pink-200 bg-pink-50 text-pink-900'
                                            : 'border-pink-600 focus:border-pink-400 focus:ring-pink-800/50 bg-pink-800/30 text-pink-100'
                                    "
                                    required
                                    autofocus
                                    autocomplete="off"
                                    maxlength="5"
                                    placeholder="•••••"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    :disabled="form.processing"
                                />
                                <button
                                    type="button"
                                    @click="showPin = !showPin"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    :class="!isDaytime ? 'text-pink-400 hover:text-pink-300' : ''"
                                    tabindex="-1"
                                >
                                    <svg
                                        v-if="showPin"
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Indicador de longitud -->
                            <div class="mt-3 flex justify-center gap-1">
                                <div
                                    v-for="i in 5"
                                    :key="i"
                                    class="h-2 rounded-full transition-all"
                                    :class="
                                        i <= form.pin.length
                                            ? isDaytime
                                                ? 'bg-pink-500 w-8'
                                                : 'bg-pink-400 w-8'
                                            : isDaytime
                                                ? 'bg-pink-200 w-2'
                                                : 'bg-pink-700/50 w-2'
                                    "
                                ></div>
                            </div>

                            <InputError
                                class="mt-3 text-center"
                                :message="form.errors.pin"
                            />
                        </div>

                        <div class="mt-8">
                            <PrimaryButton
                                class="w-full py-4 text-lg font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all bg-gradient-to-r from-pink-500 via-rose-500 to-purple-500 hover:from-pink-600 hover:via-rose-600 hover:to-purple-600 focus:ring-4 focus:ring-pink-200"
                                :class="{
                                    'opacity-50 cursor-not-allowed': form.processing || form.pin.length !== 5,
                                }"
                                :disabled="form.processing || form.pin.length !== 5"
                            >
                                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Ingresando...
                                </span>
                                <span v-else class="flex items-center justify-center gap-2">
                                    <span>Ingresar</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </span>
                            </PrimaryButton>
                        </div>
                    </form>

                    <!-- Decoración -->
                    <div class="mt-8 text-center">
                        <div
                            class="inline-flex items-center gap-2 text-xs font-medium"
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
