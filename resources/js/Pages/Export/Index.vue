<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const selectedFormat = ref('json');
const isExporting = ref(false);

const exportData = () => {
    isExporting.value = true;
    window.location.href = route('export') + '?format=' + selectedFormat.value;
    setTimeout(() => {
        isExporting.value = false;
    }, 2000);
};
</script>

<template>
    <Head title="Exportar Datos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <span class="text-3xl">💾</span>
                <h2 class="text-2xl font-bold leading-tight bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    Exportar Mis Datos
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-xl border-2 border-pink-200 p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            Selecciona el formato de exportación
                        </h3>
                        <p class="text-sm text-gray-600">
                            Descarga todos tus datos en el formato que prefieras
                        </p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-pink-50"
                            :class="selectedFormat === 'json' ? 'border-pink-500 bg-pink-50' : 'border-gray-200'">
                            <input
                                type="radio"
                                v-model="selectedFormat"
                                value="json"
                                class="w-5 h-5 text-pink-500"
                            />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">JSON</div>
                                <div class="text-sm text-gray-600">Formato estructurado, ideal para respaldos y migraciones</div>
                            </div>
                            <span class="text-2xl">📄</span>
                        </label>

                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-pink-50"
                            :class="selectedFormat === 'csv' ? 'border-pink-500 bg-pink-50' : 'border-gray-200'">
                            <input
                                type="radio"
                                v-model="selectedFormat"
                                value="csv"
                                class="w-5 h-5 text-pink-500"
                            />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">CSV</div>
                                <div class="text-sm text-gray-600">Formato de hoja de cálculo, fácil de abrir en Excel</div>
                            </div>
                            <span class="text-2xl">📊</span>
                        </label>

                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-pink-50"
                            :class="selectedFormat === 'pdf' ? 'border-pink-500 bg-pink-50' : 'border-gray-200'">
                            <input
                                type="radio"
                                v-model="selectedFormat"
                                value="pdf"
                                class="w-5 h-5 text-pink-500"
                            />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">PDF</div>
                                <div class="text-sm text-gray-600">Documento imprimible con tus entradas más recientes</div>
                            </div>
                            <span class="text-2xl">📑</span>
                        </label>
                    </div>

                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4 mb-6">
                        <p class="text-sm text-yellow-800">
                            <span class="font-semibold">💡 Nota:</span> La exportación incluye todas tus entradas del diario, notas, fotos, metas, hábitos y gratitudes.
                        </p>
                    </div>

                    <button
                        @click="exportData"
                        :disabled="isExporting"
                        class="w-full px-6 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-xl font-bold text-lg shadow-lg hover:from-pink-600 hover:to-rose-600 hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isExporting">Exportando...</span>
                        <span v-else>💾 Exportar Datos</span>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

