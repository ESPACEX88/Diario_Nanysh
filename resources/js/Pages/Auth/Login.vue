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

const showPin = ref(false);
const pinInput = ref<HTMLInputElement | null>(null);

watch(() => form.pin, () => {
    if (form.errors.pin) {
        form.clearErrors('pin');
    }
});

const isDaytime = ref(true);

const updateTimeOfDay = () => {
    const hour = new Date().getHours();
    isDaytime.value = hour >= 6 && hour < 20;
};

onMounted(() => {
    updateTimeOfDay();
    setInterval(updateTimeOfDay, 60000);

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

const handlePinInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let value = target.value.replace(/[^0-9]/g, '');
    value = value.slice(0, 5);
    form.pin = value;
    target.value = value;
};

const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && form.pin.length === 5 && !form.processing) {
        submit();
    }
};

const submit = () => {
    if (form.pin.length !== 5) {
        form.setError('pin', 'El PIN debe tener 5 dígitos');
        return;
    }

    form.pin = form.pin.replace(/[^0-9]/g, '').padStart(5, '0');

    form.post(route('login'), {
        onFinish: () => {
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

        <div class="relative min-h-screen overflow-hidden">
            <div
                class="absolute inset-0"
                :class="
                    isDaytime
                        ? 'bg-gradient-to-br from-rose-50 via-white to-fuchsia-50'
                        : 'bg-gradient-to-br from-slate-900 via-fuchsia-950 to-slate-900'
                "
            ></div>

            <div class="pointer-events-none absolute inset-0">
                <div
                    class="absolute -top-24 right-0 h-80 w-80 rounded-full opacity-25 blur-3xl"
                    :class="isDaytime ? 'bg-fuchsia-300' : 'bg-fuchsia-600'"
                ></div>
                <div
                    class="absolute -bottom-28 -left-10 h-96 w-96 rounded-full opacity-25 blur-3xl"
                    :class="isDaytime ? 'bg-rose-300' : 'bg-rose-700'"
                ></div>
            </div>

            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full items-stretch gap-6 lg:grid-cols-2">
                    <section
                        class="hidden rounded-3xl border p-8 lg:flex lg:flex-col lg:justify-between"
                        :class="
                            isDaytime
                                ? 'border-rose-100 bg-white/65 text-rose-900 backdrop-blur-xl'
                                : 'border-fuchsia-900/50 bg-slate-900/50 text-fuchsia-100 backdrop-blur-xl'
                        "
                    >
                        <div>
                            <p
                                class="mb-4 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold tracking-wide"
                                :class="isDaytime ? 'bg-rose-100 text-rose-700' : 'bg-fuchsia-900/40 text-fuchsia-200'"
                            >
                                Diario personal protegido
                            </p>
                            <h2 class="text-3xl font-semibold leading-tight">
                                Un espacio privado para tus momentos importantes.
                            </h2>
                            <p
                                class="mt-4 max-w-md text-sm leading-relaxed"
                                :class="isDaytime ? 'text-rose-700/80' : 'text-fuchsia-100/75'"
                            >
                                Accede con tu PIN y continúa donde lo dejaste: notas, fotos, metas y recuerdos.
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border p-5"
                            :class="
                                isDaytime
                                    ? 'border-rose-200 bg-white/70'
                                    : 'border-fuchsia-900/60 bg-slate-950/40'
                            "
                        >
                            <p class="text-sm font-medium">{{ greeting }} ✨</p>
                            <p
                                class="mt-2 text-xs"
                                :class="isDaytime ? 'text-rose-700/80' : 'text-fuchsia-100/70'"
                            >
                                Cada entrada es solo tuya.
                            </p>
                        </div>
                    </section>

                    <section
                        class="rounded-3xl border p-6 shadow-2xl sm:p-8"
                        :class="
                            isDaytime
                                ? 'border-rose-100 bg-white/85 backdrop-blur-xl'
                                : 'border-fuchsia-900/50 bg-slate-900/70 backdrop-blur-xl'
                        "
                    >
                        <div class="mb-8 text-center">
                            <span class="mb-3 block text-5xl">💕</span>
                            <p
                                class="mb-2 text-sm font-medium"
                                :class="isDaytime ? 'text-rose-600' : 'text-fuchsia-200'"
                            >
                                {{ greeting }}
                            </p>
                        <h1
                            class="bg-gradient-to-r bg-clip-text text-3xl font-bold text-transparent sm:text-4xl"
                            :class="
                                isDaytime
                                    ? 'from-rose-500 via-fuchsia-500 to-purple-500'
                                    : 'from-fuchsia-200 via-rose-200 to-purple-200'
                            "
                        >
                            Diario de Nanysh
                        </h1>
                            <p
                                class="mt-3 text-sm"
                                :class="isDaytime ? 'text-rose-600/90' : 'text-fuchsia-100/80'"
                            >
                                Ingresa tu PIN para continuar.
                            </p>
                        </div>

                        <div
                            v-if="status"
                            class="mb-5 rounded-xl border p-3 text-center text-sm font-medium"
                            :class="
                                isDaytime
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                    : 'border-emerald-900/60 bg-emerald-950/40 text-emerald-200'
                            "
                        >
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label
                                    for="pin"
                                    class="mb-2 block text-sm font-semibold"
                                    :class="isDaytime ? 'text-rose-700' : 'text-fuchsia-200'"
                                >
                                    PIN de 5 digitos
                                </label>
                                <div class="relative">
                                    <input
                                        ref="pinInput"
                                        id="pin"
                                        :type="showPin ? 'text' : 'password'"
                                        :value="form.pin"
                                        @input="handlePinInput"
                                        @keypress="handleKeyPress"
                                        class="block w-full rounded-2xl border px-4 py-4 pr-12 text-center text-3xl font-bold tracking-[0.45em] outline-none transition focus:ring-4 sm:text-4xl"
                                        :class="
                                            form.errors.pin
                                                ? 'border-red-400 bg-red-50 text-red-900 focus:border-red-500 focus:ring-red-200'
                                                : isDaytime
                                                    ? 'border-rose-200 bg-rose-50/60 text-rose-900 focus:border-rose-400 focus:ring-rose-200'
                                                    : 'border-fuchsia-800 bg-slate-950/50 text-fuchsia-100 focus:border-fuchsia-500 focus:ring-fuchsia-900/60'
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
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium transition"
                                        :class="
                                            isDaytime
                                                ? 'text-rose-500 hover:text-rose-700'
                                                : 'text-fuchsia-300 hover:text-fuchsia-100'
                                        "
                                        :aria-label="showPin ? 'Ocultar PIN' : 'Mostrar PIN'"
                                    >
                                        {{ showPin ? 'Ocultar' : 'Mostrar' }}
                                    </button>
                                </div>

                                <div class="mt-3 flex justify-center gap-2">
                                    <div
                                        v-for="i in 5"
                                        :key="i"
                                        class="h-2 rounded-full transition-all"
                                        :class="
                                            i <= form.pin.length
                                                ? isDaytime
                                                    ? 'w-8 bg-rose-500'
                                                    : 'w-8 bg-fuchsia-300'
                                                : isDaytime
                                                    ? 'w-3 bg-rose-200'
                                                    : 'w-3 bg-fuchsia-800'
                                        "
                                    ></div>
                                </div>

                                <InputError class="mt-3" :message="form.errors.pin" />
                            </div>

                            <PrimaryButton
                                class="w-full rounded-2xl py-4 text-base font-semibold shadow-lg transition hover:shadow-xl"
                                :class="
                                    isDaytime
                                        ? 'bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 hover:from-rose-600 hover:via-fuchsia-600 hover:to-purple-600'
                                        : 'bg-gradient-to-r from-fuchsia-500 via-rose-500 to-purple-500 hover:from-fuchsia-400 hover:via-rose-400 hover:to-purple-400'
                                "
                                :disabled="form.processing || form.pin.length !== 5"
                            >
                                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                    Ingresando...
                                </span>
                                <span v-else class="flex items-center justify-center gap-2">
                                    <span>Ingresar</span>
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </span>
                            </PrimaryButton>
                        </form>

                        <p
                            class="mt-6 text-center text-xs"
                            :class="isDaytime ? 'text-rose-500' : 'text-fuchsia-200/80'"
                        >
                            Tu diario personal y seguro.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
