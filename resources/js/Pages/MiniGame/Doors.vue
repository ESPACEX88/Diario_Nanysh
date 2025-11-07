<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Pet {
    id: number;
    name: string;
    coins: number;
    happiness: number;
}

interface Result {
    won: boolean;
    coins: number;
    message: string;
    winningDoors: number[];
    losingDoor: number;
}

interface Props {
    pet: Pet;
    result?: Result;
    canPlay?: boolean;
    nextPlayAt?: string | null;
    hoursRemaining?: number;
    minutesRemaining?: number;
}

const props = withDefaults(defineProps<Props>(), {
    canPlay: true,
    hoursRemaining: 0,
    minutesRemaining: 0,
});

const selectedDoor = ref<number | null>(null);
const gameResult = ref<Result | null>(props.result || null);
const pet = ref<Pet>(props.pet);

const form = useForm({
    door: null as number | null,
});

const canPlayGame = computed(() => props.canPlay && !form.processing && selectedDoor.value !== null);

const selectDoor = (doorNumber: number) => {
    if (form.processing || !props.canPlay) return;
    selectedDoor.value = doorNumber;
    form.door = doorNumber;
};

const playGame = () => {
    if (!selectedDoor.value || form.processing || !props.canPlay) return;

    form.door = selectedDoor.value;
    form.post(route('minigame.doors.play'), {
        preserveScroll: true,
        onSuccess: () => {
            // El resultado se recargará desde las props
            window.location.reload();
        },
        onError: () => {
            gameResult.value = {
                won: false,
                coins: 0,
                message: 'Hubo un error al jugar. Intenta de nuevo.',
                winningDoors: [],
                losingDoor: 0,
            };
        },
    });
};

const resetGame = () => {
    selectedDoor.value = null;
    gameResult.value = null;
    form.reset();
};

const getDoorEmoji = (doorNumber: number) => {
    if (!gameResult.value) return '🚪';
    
    if (gameResult.value.winningDoors.includes(doorNumber)) {
        return '🎁';
    }
    if (doorNumber === gameResult.value.losingDoor) {
        return '❌';
    }
    return '🚪';
};

const getDoorClass = (doorNumber: number) => {
    const baseClass = 'relative w-full h-64 rounded-2xl border-4 transition-all transform cursor-pointer flex items-center justify-center text-6xl font-bold';
    
    if (!selectedDoor.value) {
        return `${baseClass} border-pink-300 bg-gradient-to-br from-pink-100 to-rose-100 hover:border-pink-500 hover:scale-105 hover:shadow-xl`;
    }
    
    if (selectedDoor.value === doorNumber) {
        if (gameResult.value) {
            if (gameResult.value.won && gameResult.value.winningDoors.includes(doorNumber)) {
                return `${baseClass} border-green-500 bg-gradient-to-br from-green-200 to-emerald-200 scale-105 shadow-2xl ring-4 ring-green-300`;
            }
            if (doorNumber === gameResult.value.losingDoor) {
                return `${baseClass} border-red-500 bg-gradient-to-br from-red-200 to-rose-200 scale-105 shadow-2xl ring-4 ring-red-300`;
            }
        }
        return `${baseClass} border-pink-600 bg-gradient-to-br from-pink-200 to-rose-200 scale-105 shadow-xl ring-4 ring-pink-400`;
    }
    
    if (gameResult.value) {
        if (gameResult.value.winningDoors.includes(doorNumber)) {
            return `${baseClass} border-green-300 bg-gradient-to-br from-green-100 to-emerald-100 opacity-75`;
        }
        if (doorNumber === gameResult.value.losingDoor) {
            return `${baseClass} border-red-300 bg-gradient-to-br from-red-100 to-rose-100 opacity-75`;
        }
    }
    
    return `${baseClass} border-gray-300 bg-gray-100 opacity-50`;
};
</script>

<template>
    <Head title="Puertas Misteriosas 🎰" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-3xl">🎰</span>
                <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    Puertas Misteriosas
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Información del juego -->
                <div class="mb-8 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="text-4xl">🎁</span>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-purple-900 mb-2">¿Cómo funciona?</h3>
                            <p class="text-purple-800 mb-2">
                                Hay <strong>3 puertas misteriosas</strong>. En <strong>2 de ellas</strong> hay fichitas esperándote, 
                                pero en <strong>1 puerta</strong> no hay nada. ¡Elige sabiamente!
                            </p>
                            <div class="flex items-center gap-4 mt-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">💰</span>
                                    <span class="font-semibold text-purple-900">Fichitas de Snoopy: {{ pet.coins }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">😊</span>
                                    <span class="font-semibold text-purple-900">Felicidad: {{ pet.happiness }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de cooldown -->
                <div v-if="!props.canPlay" class="mb-8 bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-300 rounded-2xl p-6">
                    <div class="text-center">
                        <div class="text-5xl mb-4">⏰</div>
                        <h3 class="text-2xl font-bold text-orange-900 mb-2">¡Espera un momento!</h3>
                        <p class="text-orange-800 text-lg mb-4">
                            Ya jugaste recientemente. Debes esperar <strong>{{ props.hoursRemaining }} horas y {{ props.minutesRemaining }} minutos</strong> para jugar de nuevo.
                        </p>
                        <p class="text-orange-700 text-sm">
                            Puedes jugar una vez cada 12 horas para mantener el juego emocionante. 🎮
                        </p>
                    </div>
                </div>

                <!-- Puertas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        v-for="door in [1, 2, 3]"
                        :key="door"
                        @click="selectDoor(door)"
                        :class="[
                            getDoorClass(door),
                            !props.canPlay ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                    >
                        <div class="text-center">
                            <div class="text-8xl mb-4">
                                {{ getDoorEmoji(door) }}
                            </div>
                            <div class="text-2xl font-bold text-gray-800">
                                Puerta {{ door }}
                            </div>
                            <div v-if="selectedDoor === door && !gameResult" class="mt-2 text-sm text-pink-700 font-semibold">
                                ¡Seleccionada!
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de jugar -->
                <div v-if="!gameResult && props.canPlay" class="text-center mb-8">
                    <PrimaryButton
                        @click="playGame"
                        :disabled="!selectedDoor || form.processing || !props.canPlay"
                        class="px-12 py-4 text-xl bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 rounded-full font-bold shadow-2xl transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing" class="flex items-center gap-2">
                            <span class="animate-spin">⏳</span>
                            Jugando...
                        </span>
                        <span v-else-if="!selectedDoor" class="flex items-center gap-2">
                            <span>👆</span>
                            Selecciona una puerta primero
                        </span>
                        <span v-else class="flex items-center gap-2">
                            <span>🎲</span>
                            ¡Abrir Puerta {{ selectedDoor }}!
                        </span>
                    </PrimaryButton>
                </div>

                <!-- Resultado -->
                <div v-if="gameResult" class="mb-8">
                    <div
                        :class="[
                            'rounded-2xl p-8 border-4 shadow-2xl',
                            gameResult.won
                                ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-400'
                                : 'bg-gradient-to-br from-red-50 to-rose-50 border-red-400'
                        ]"
                    >
                        <div class="text-center">
                            <div class="text-6xl mb-4">
                                {{ gameResult.won ? '🎉' : '😅' }}
                            </div>
                            <h3
                                :class="[
                                    'text-3xl font-bold mb-4',
                                    gameResult.won ? 'text-green-800' : 'text-red-800'
                                ]"
                            >
                                {{ gameResult.won ? '¡Ganaste!' : '¡Oh no!' }}
                            </h3>
                            <p
                                :class="[
                                    'text-xl mb-6',
                                    gameResult.won ? 'text-green-700' : 'text-red-700'
                                ]"
                            >
                                {{ gameResult.message }}
                            </p>
                            <div v-if="gameResult.won" class="mb-6">
                                <div class="inline-block bg-yellow-200 border-4 border-yellow-400 rounded-full px-8 py-4">
                                    <div class="text-4xl font-bold text-yellow-900">
                                        +{{ gameResult.coins }} 💰
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-6">
                                <p class="font-semibold mb-2">Resultado del juego:</p>
                                <div class="flex justify-center gap-4">
                                    <span
                                        v-for="door in [1, 2, 3]"
                                        :key="door"
                                        :class="[
                                            'px-4 py-2 rounded-lg font-bold',
                                            gameResult.winningDoors.includes(door)
                                                ? 'bg-green-200 text-green-800'
                                                : 'bg-red-200 text-red-800'
                                        ]"
                                    >
                                        Puerta {{ door }}: {{ gameResult.winningDoors.includes(door) ? '🎁 Ganó' : '❌ Perdió' }}
                                    </span>
                                </div>
                            </div>
                            <PrimaryButton
                                @click="resetGame"
                                class="px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-full font-bold shadow-lg"
                            >
                                <span class="mr-2">🔄</span>
                                Jugar de Nuevo
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Snoopy -->
                <div class="bg-white rounded-2xl border-2 border-pink-200 shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>🐕</span>
                        Estado de Snoopy
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ pet.coins }}</div>
                            <div class="text-sm text-gray-600">Fichitas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">😊</div>
                            <div class="text-2xl font-bold text-pink-600">{{ pet.happiness }}%</div>
                            <div class="text-sm text-gray-600">Felicidad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

