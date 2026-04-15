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
        <label class="mb-2 block text-sm font-semibold text-rose-950">
            Etiquetas
        </label>

        <!-- Selected Tags Display -->
        <div v-if="selectedTags.length > 0" class="flex flex-wrap gap-2 mb-3">
            <button
                v-for="tag in selectedTags"
                :key="tag.id"
                @click="toggleTag(tag.id)"
                class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold text-white shadow-sm shadow-rose-500/10 transition hover:-translate-y-px"
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
                class="w-full rounded-2xl border border-rose-200 bg-white/90 px-4 py-3 text-slate-900 shadow-sm shadow-rose-500/5 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-200"
                @focus="isSearching = true"
                @blur="handleBlur"
            />
            
            <!-- Tag Suggestions Dropdown -->
            <div
                v-if="isSearching && (filteredTags.length > 0 || searchQuery.trim())"
                class="absolute z-50 mt-2 max-h-60 w-full overflow-y-auto rounded-3xl border border-rose-100/80 bg-white/95 shadow-[0_24px_70px_rgba(236,72,153,0.16)] backdrop-blur-xl custom-scrollbar"
            >
                <!-- Existing Tags -->
                <div
                    v-for="tag in filteredTags"
                    :key="tag.id"
                    @mousedown.prevent="toggleTag(tag.id)"
                    class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors hover:bg-rose-50"
                    :class="{ 'bg-rose-100/80': selectedTagIds.includes(tag.id) }"
                >
                    <div
                        class="w-4 h-4 rounded-full border-2"
                        :style="{ 
                            backgroundColor: selectedTagIds.includes(tag.id) ? tag.color : 'transparent',
                            borderColor: tag.color
                        }"
                    ></div>
                    <span class="flex-1 font-semibold text-rose-950">{{ tag.name }}</span>
                    <span
                        v-if="selectedTagIds.includes(tag.id)"
                        class="text-rose-600"
                    >
                        ✓
                    </span>
                </div>

                <!-- Create New Tag -->
                <div
                    v-if="allowCreate && searchQuery.trim() && !filteredTags.find(t => t.name.toLowerCase() === searchQuery.toLowerCase())"
                    @mousedown.prevent="showCreateTag = true"
                    class="flex cursor-pointer items-center gap-3 border-t border-rose-100 px-4 py-3 transition-colors hover:bg-rose-50"
                >
                    <span class="text-2xl">➕</span>
                    <span class="flex-1 font-semibold text-rose-950">
                        Crear "{{ searchQuery }}"
                    </span>
                </div>
            </div>
        </div>

        <!-- Create Tag Form -->
        <div
            v-if="showCreateTag"
            class="rounded-3xl border border-rose-100/80 bg-gradient-to-r from-rose-50 to-fuchsia-50 p-4"
        >
            <div class="space-y-3">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-rose-950">
                        Nombre de la etiqueta
                    </label>
                    <input
                        v-model="newTagName"
                        type="text"
                        placeholder="Ej: Trabajo, Familia, Viaje..."
                        class="w-full rounded-2xl border border-rose-200 bg-white/90 px-4 py-3 text-slate-900 focus:border-rose-400 focus:ring-4 focus:ring-rose-200"
                        @keyup.enter="createTag"
                        autofocus
                    />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-rose-950">
                        Color
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="color in tagColors"
                            :key="color"
                            @click="newTagColor = color"
                            class="h-8 w-8 rounded-full border-2 border-white transition hover:-translate-y-px hover:scale-110"
                            :style="{ backgroundColor: color }"
                            :class="{ 'ring-2 ring-offset-2 ring-rose-500': newTagColor === color }"
                            :aria-label="`Seleccionar color ${color}`"
                        ></button>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="createTag"
                        class="flex-1 rounded-full bg-gradient-to-r from-rose-500 via-fuchsia-500 to-purple-500 px-4 py-2 font-semibold text-white transition hover:-translate-y-px"
                    >
                        Crear
                    </button>
                    <button
                        @click="showCreateTag = false; newTagName = ''"
                        class="rounded-full bg-rose-100 px-4 py-2 font-semibold text-rose-700 transition hover:bg-rose-200"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

