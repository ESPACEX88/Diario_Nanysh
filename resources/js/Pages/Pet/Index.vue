<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

interface Props {
    pet: {
        id: number;
        name: string;
        level: number;
        experience: number;
        happiness: number;
        hunger: number;
        energy: number;
        health: number;
        coins: number;
        mood: string;
    };
}

const props = defineProps<Props>();
const page = usePage();

const showRenameModal = ref(false);
const showSuccessMessage = ref(false);
const showErrorMessage = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Watch for flash messages
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        successMessage.value = flash.success;
        showSuccessMessage.value = true;
        setTimeout(() => {
            showSuccessMessage.value = false;
        }, 5000);
    }
    if (flash?.error) {
        errorMessage.value = flash.error;
        showErrorMessage.value = true;
        setTimeout(() => {
            showErrorMessage.value = false;
        }, 5000);
    }
}, { immediate: true });
const renameForm = useForm({
    name: props.pet.name,
});

const submitRename = () => {
    renameForm.post(route('pet.rename'), {
        preserveScroll: true,
        onSuccess: () => {
            showRenameModal.value = false;
        },
    });
};

const feedPet = () => {
    const coins = Number(props.pet.coins);
    if (coins < 10) {
        alert('No tienes suficientes fichitas. Necesitas 10 fichitas para alimentar a Snoopy.');
        return;
    }
    router.post(route('pet.feed'), {}, {
        preserveScroll: false,
    });
};

const playWithPet = () => {
    const coins = Number(props.pet.coins);
    if (coins < 15) {
        alert('No tienes suficientes fichitas. Necesitas 15 fichitas para jugar con Snoopy.');
        return;
    }
    router.post(route('pet.play'), {}, {
        preserveScroll: false,
    });
};

const careForPet = () => {
    const coins = Number(props.pet.coins);
    if (coins < 20) {
        alert('No tienes suficientes fichitas. Necesitas 20 fichitas para cuidar a Snoopy.');
        return;
    }
    router.post(route('pet.care'), {}, {
        preserveScroll: false,
    });
};

const expNeeded = computed(() => props.pet.level * 100);
const expProgress = computed(() => (props.pet.experience / expNeeded.value) * 100);

const getMoodText = computed(() => {
    const avg = (props.pet.happiness + props.pet.hunger + props.pet.energy + props.pet.health) / 4;
    if (avg >= 80) return '¡Muy feliz!';
    if (avg >= 60) return 'Contento';
    if (avg >= 40) return 'Triste';
    return 'Necesita atención';
});

const getStatColor = (value: number) => {
    if (value >= 70) return 'from-green-400 to-emerald-500';
    if (value >= 40) return 'from-yellow-400 to-orange-500';
    return 'from-red-400 to-rose-500';
};
</script>

<template>
    <Head title="Mi Snoopy" />

    <AuthenticatedLayout>
        <!-- Flash Messages -->
        <div v-if="showSuccessMessage" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
            <span class="text-xl">✅</span>
            <span>{{ successMessage }}</span>
            <button @click="showSuccessMessage = false" class="ml-2 text-white hover:text-gray-200">✕</button>
        </div>
        
        <div v-if="showErrorMessage" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in">
            <span class="text-xl">❌</span>
            <span>{{ errorMessage }}</span>
            <button @click="showErrorMessage = false" class="ml-2 text-white hover:text-gray-200">✕</button>
        </div>

        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span class="text-4xl">🐕</span>
                        <span class="absolute -top-1 -right-1 text-xl animate-pulse">❤️</span>
                    </div>
                    <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        {{ pet.name }}
                    </h2>
                    <button
                        @click="showRenameModal = true"
                        class="text-gray-500 hover:text-pink-600 transition-colors"
                        title="Cambiar nombre"
                    >
                        ✏️
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 bg-gradient-to-r from-amber-400 to-yellow-500 px-4 py-2 rounded-full shadow-md">
                        <span class="text-xl">🪙</span>
                        <span class="font-bold text-white">{{ Number(pet.coins) || 0 }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 rounded-full shadow-md">
                        <span class="text-xl">⭐</span>
                        <span class="font-bold text-white">Nivel {{ pet.level }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <!-- Main Pet Display -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Snoopy Display -->
                    <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-100 via-sky-50 to-cyan-50 shadow-xl border-2 border-blue-200 p-8">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-200/30 to-cyan-200/30 rounded-full -mr-32 -mt-32"></div>
                        <div class="relative text-center">
                            <div class="mb-6">
                                <!-- Snoopy with Heart -->
                                <div class="relative inline-block mb-4">
                                    <div class="text-9xl transform hover:scale-110 transition-transform">
                                        🐕
                                    </div>
                                    <div class="absolute -top-4 -right-4 text-5xl animate-pulse">
                                        ❤️
                                    </div>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-800 mb-2">
                                    {{ pet.name }}
                                </h3>
                                <p class="text-lg text-gray-600 font-semibold mb-4">
                                    {{ getMoodText }}
                                </p>
                                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-red-400 to-pink-500 px-6 py-3 rounded-full shadow-lg">
                                    <span class="text-2xl">❤️</span>
                                    <span class="text-white font-bold">Te quiere mucho</span>
                                </div>
                            </div>
                            
                            <!-- Experience Bar -->
                            <div class="mb-6">
                                <div class="flex justify-between text-sm font-semibold text-gray-700 mb-2">
                                    <span>Experiencia</span>
                                    <span>{{ pet.experience }} / {{ expNeeded }} XP</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div
                                        class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500"
                                        :style="{ width: expProgress + '%' }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Panel -->
                    <div class="space-y-4">
                        <div
                            v-for="stat in [
                                { name: 'Felicidad', value: pet.happiness, icon: '💖', color: getStatColor(pet.happiness) },
                                { name: 'Hambre', value: pet.hunger, icon: '🍽️', color: getStatColor(pet.hunger) },
                                { name: 'Energía', value: pet.energy, icon: '⚡', color: getStatColor(pet.energy) },
                                { name: 'Salud', value: pet.health, icon: '💚', color: getStatColor(pet.health) },
                            ]"
                            :key="stat.name"
                            class="bg-white rounded-xl p-4 shadow-lg border-2 border-gray-100"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ stat.icon }}</span>
                                    <span class="font-semibold text-gray-700">{{ stat.name }}</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ stat.value }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r rounded-full transition-all duration-500"
                                    :class="stat.color"
                                    :style="{ width: stat.value + '%' }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-2xl shadow-lg border-2 border-pink-100 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-pink-500 to-rose-500">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <span>✨</span>
                                Cuidar a {{ pet.name }}
                            </h3>
                            <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                                <p class="text-sm text-white font-semibold">
                                    💡 Escribe en tu diario con estados de ánimo felices (😊, 😍, 🥳) para ganar fichitas
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button
                                @click="feedPet"
                                :disabled="Number(pet.coins) < 10"
                                :class="[
                                    'group flex flex-col items-center justify-center p-8 rounded-xl transition-all border-2 transform',
                                    Number(pet.coins) >= 10
                                        ? 'bg-gradient-to-br from-orange-50 to-amber-50 hover:from-orange-100 hover:to-amber-100 border-transparent hover:border-orange-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer'
                                        : 'bg-gray-100 border-gray-200 opacity-50 cursor-not-allowed'
                                ]"
                            >
                                <span class="text-6xl mb-4 transform group-hover:scale-110 transition-transform">🍽️</span>
                                <span class="text-lg font-bold text-gray-800 mb-2">Alimentar</span>
                                <span class="text-sm text-gray-600 text-center mb-2">+30 Hambre<br>+10 Felicidad</span>
                                <div class="flex items-center gap-1 text-xs font-bold" :class="Number(pet.coins) >= 10 ? 'text-orange-600' : 'text-red-500'">
                                    <span>🪙</span>
                                    <span>10 fichitas</span>
                                </div>
                            </button>

                            <button
                                @click="playWithPet"
                                :disabled="Number(pet.coins) < 15 || pet.energy < 20"
                                :class="[
                                    'group flex flex-col items-center justify-center p-8 rounded-xl transition-all border-2 transform',
                                    Number(pet.coins) >= 15 && pet.energy >= 20
                                        ? 'bg-gradient-to-br from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 border-transparent hover:border-purple-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer'
                                        : 'bg-gray-100 border-gray-200 opacity-50 cursor-not-allowed'
                                ]"
                            >
                                <span class="text-6xl mb-4 transform group-hover:scale-110 transition-transform">🎮</span>
                                <span class="text-lg font-bold text-gray-800 mb-2">Jugar</span>
                                <span class="text-sm text-gray-600 text-center mb-2">+25 Felicidad<br>-15 Energía</span>
                                <div class="flex items-center gap-1 text-xs font-bold" :class="Number(pet.coins) >= 15 ? 'text-purple-600' : 'text-red-500'">
                                    <span>🪙</span>
                                    <span>15 fichitas</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">✨ Sin límite</div>
                            </button>

                            <button
                                @click="careForPet"
                                :disabled="Number(pet.coins) < 20"
                                :class="[
                                    'group flex flex-col items-center justify-center p-8 rounded-xl transition-all border-2 transform',
                                    Number(pet.coins) >= 20
                                        ? 'bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 border-transparent hover:border-green-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer'
                                        : 'bg-gray-100 border-gray-200 opacity-50 cursor-not-allowed'
                                ]"
                            >
                                <span class="text-6xl mb-4 transform group-hover:scale-110 transition-transform">💤</span>
                                <span class="text-lg font-bold text-gray-800 mb-2">Cuidar</span>
                                <span class="text-sm text-gray-600 text-center mb-2">+20 Salud<br>+30 Energía</span>
                                <div class="flex items-center gap-1 text-xs font-bold" :class="Number(pet.coins) >= 20 ? 'text-green-600' : 'text-red-500'">
                                    <span>🪙</span>
                                    <span>20 fichitas</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rename Modal -->
        <div
            v-if="showRenameModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            @click.self="showRenameModal = false"
        >
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Cambiar nombre de {{ pet.name }}</h3>
                <form @submit.prevent="submitRename" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nuevo nombre
                        </label>
                        <input
                            v-model="renameForm.name"
                            type="text"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                            placeholder="Nombre de tu mascota"
                        />
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button
                            type="button"
                            @click="showRenameModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="renameForm.processing"
                            class="px-6 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-bold hover:from-pink-600 hover:to-rose-600 disabled:opacity-50"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

