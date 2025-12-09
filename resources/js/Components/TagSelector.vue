<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface Tag {
    id: number;
    name: string;
    color: string;
}

interface Props {
    modelValue: number[];
    existingTags?: Tag[];
    allowCreate?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    existingTags: () => [],
    allowCreate: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: number[]];
}>();

const selectedTagIds = ref<number[]>(props.modelValue || []);
const searchQuery = ref('');
const availableTags = ref<Tag[]>(props.existingTags || []);
const isSearching = ref(false);
const showCreateTag = ref(false);
const newTagName = ref('');
const newTagColor = ref('#EC4899');

const filteredTags = computed(() => {
    if (!searchQuery.value) {
        return availableTags.value;
    }
    const query = searchQuery.value.toLowerCase();
    return availableTags.value.filter(tag => 
        tag.name.toLowerCase().includes(query)
    );
});

const toggleTag = (tagId: number) => {
    const index = selectedTagIds.value.indexOf(tagId);
    if (index > -1) {
        selectedTagIds.value.splice(index, 1);
    } else {
        selectedTagIds.value.push(tagId);
    }
    emit('update:modelValue', [...selectedTagIds.value]);
};

const createTag = async () => {
    if (!newTagName.value.trim()) return;

    try {
        const response = await axios.post(route('tags.store'), {
            name: newTagName.value.trim(),
            color: newTagColor.value,
        });

        const newTag = response.data;
        availableTags.value.push(newTag);
        selectedTagIds.value.push(newTag.id);
        emit('update:modelValue', [...selectedTagIds.value]);
        
        newTagName.value = '';
        showCreateTag.value = false;
    } catch (error) {
        console.error('Error creating tag:', error);
    }
};

const selectedTags = computed(() => {
    return availableTags.value.filter(tag => 
        selectedTagIds.value.includes(tag.id)
    );
});

const handleBlur = () => {
    setTimeout(() => {
        isSearching.value = false;
    }, 200);
};

const tagColors = [
    '#EC4899', '#F472B6', '#FB7185', '#F87171',
    '#F59E0B', '#EAB308', '#84CC16', '#22C55E',
    '#10B981', '#14B8A6', '#06B6D4', '#3B82F6',
    '#6366F1', '#8B5CF6', '#A855F7', '#D946EF',
];

watch(() => props.modelValue, (newValue) => {
    selectedTagIds.value = newValue || [];
}, { immediate: true });
</script>

<template>
    <div class="space-y-3">
        <label class="block text-sm font-semibold text-gray-800 mb-2">
            Etiquetas
        </label>

        <!-- Selected Tags Display -->
        <div v-if="selectedTags.length > 0" class="flex flex-wrap gap-2 mb-3">
            <button
                v-for="tag in selectedTags"
                :key="tag.id"
                @click="toggleTag(tag.id)"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold text-white transition-all hover:scale-105"
                :style="{ backgroundColor: tag.color }"
                :aria-label="`Eliminar etiqueta ${tag.name}`"
            >
                <span>{{ tag.name }}</span>
                <span class="text-xs">✕</span>
            </button>
        </div>

        <!-- Search and Select -->
        <div class="relative">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="🔍 Buscar o crear etiqueta..."
                class="w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all"
                @focus="isSearching = true"
                @blur="handleBlur"
            />
            
            <!-- Tag Suggestions Dropdown -->
            <div
                v-if="isSearching && (filteredTags.length > 0 || searchQuery.trim())"
                class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border-2 border-pink-200 max-h-60 overflow-y-auto custom-scrollbar"
            >
                <!-- Existing Tags -->
                <div
                    v-for="tag in filteredTags"
                    :key="tag.id"
                    @mousedown.prevent="toggleTag(tag.id)"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-pink-50 cursor-pointer transition-colors"
                    :class="{ 'bg-pink-100': selectedTagIds.includes(tag.id) }"
                >
                    <div
                        class="w-4 h-4 rounded-full border-2"
                        :style="{ 
                            backgroundColor: selectedTagIds.includes(tag.id) ? tag.color : 'transparent',
                            borderColor: tag.color
                        }"
                    ></div>
                    <span class="flex-1 font-semibold text-gray-800">{{ tag.name }}</span>
                    <span
                        v-if="selectedTagIds.includes(tag.id)"
                        class="text-pink-600"
                    >
                        ✓
                    </span>
                </div>

                <!-- Create New Tag -->
                <div
                    v-if="allowCreate && searchQuery.trim() && !filteredTags.find(t => t.name.toLowerCase() === searchQuery.toLowerCase())"
                    @mousedown.prevent="showCreateTag = true"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-pink-50 cursor-pointer transition-colors border-t-2 border-pink-100"
                >
                    <span class="text-2xl">➕</span>
                    <span class="flex-1 font-semibold text-gray-800">
                        Crear "{{ searchQuery }}"
                    </span>
                </div>
            </div>
        </div>

        <!-- Create Tag Form -->
        <div
            v-if="showCreateTag"
            class="p-4 bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl border-2 border-pink-200"
        >
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">
                        Nombre de la etiqueta
                    </label>
                    <input
                        v-model="newTagName"
                        type="text"
                        placeholder="Ej: Trabajo, Familia, Viaje..."
                        class="w-full rounded-lg border-2 border-pink-200 bg-white px-4 py-2 text-gray-900 focus:border-pink-500 focus:ring-2 focus:ring-pink-500"
                        @keyup.enter="createTag"
                        autofocus
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Color
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="color in tagColors"
                            :key="color"
                            @click="newTagColor = color"
                            class="w-8 h-8 rounded-full border-2 transition-all hover:scale-110"
                            :style="{ backgroundColor: color }"
                            :class="{ 'ring-2 ring-offset-2 ring-pink-500': newTagColor === color }"
                            :aria-label="`Seleccionar color ${color}`"
                        ></button>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="createTag"
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all"
                    >
                        Crear
                    </button>
                    <button
                        @click="showCreateTag = false; newTagName = ''"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

