<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import EmptyState from '@/Components/EmptyState.vue';

interface Todo {
    id: number;
    title: string;
    description?: string;
    is_completed: boolean;
    due_date?: string;
    priority: 'low' | 'medium' | 'high';
    category?: string;
}

interface Paginated<T> {
    data: T[];
    links?: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Props {
    todos: Paginated<Todo> | Todo[];
    categories?: string[];
    filters?: {
        search?: string;
        category?: string;
        completed?: boolean;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const showCompleted = ref(true);
const searchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

const todoList = computed<Todo[]>(() =>
    Array.isArray(props.todos) ? props.todos : (props.todos?.data ?? [])
);

const paginationLinks = computed(() =>
    Array.isArray(props.todos) ? [] : (props.todos?.links ?? [])
);

const categories = computed(() => {
    if (props.categories?.length) {
        return [...props.categories].sort();
    }

    const cats = todoList.value
        .map(t => t.category)
        .filter((cat): cat is string => !!cat);
    return [...new Set(cats)].sort();
});

const pendingTodos = computed(() =>
    todoList.value.filter(t => !t.is_completed)
);

const completedTodos = computed(() =>
    todoList.value.filter(t => t.is_completed)
);

watch(searchQuery, () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    searchTimeout.value = setTimeout(() => {
        applyFilters();
    }, 500);
});

watch(selectedCategory, () => {
    applyFilters();
});

const applyFilters = () => {
    router.get(
        route('todos.index'),
        {
            search: searchQuery.value || undefined,
            category: selectedCategory.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    router.get(route('todos.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const toggleComplete = (id: number) => {
    router.post(route('todos.toggle', id), {}, {
        preserveScroll: true,
    });
};

const deleteTodo = (id: number) => {
    if (confirm('¿Estás segura de que quieres eliminar esta tarea?')) {
        router.delete(route('todos.destroy', id));
    }
};

const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'high': return 'bg-red-100 text-red-700 border-red-300';
        case 'medium': return 'bg-yellow-100 text-yellow-700 border-yellow-300';
        case 'low': return 'bg-green-100 text-green-700 border-green-300';
        default: return 'bg-gray-100 text-gray-700 border-gray-300';
    }
};
</script>

<template>
    <Head title="Tareas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    📝 Mis Tareas
                </h2>
                <Link
                    :href="route('todos.create')"
                    class="px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md hover:shadow-lg"
                >
                    + Nueva Tarea
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <label for="todos-search" class="sr-only">Buscar tareas</label>
                            <input
                                id="todos-search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="🔍 Buscar tareas..."
                                class="w-full rounded-xl border-2 border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 pl-12 text-gray-900 dark:text-gray-100 shadow-sm focus:border-pink-500 dark:focus:border-pink-400 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all"
                                aria-label="Buscar tareas"
                                aria-describedby="todos-search-description"
                            />
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl" aria-hidden="true">🔍</span>
                            <button
                                v-if="searchQuery"
                                @click="clearFilters"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 rounded"
                                aria-label="Limpiar búsqueda"
                                @keydown.enter="clearFilters"
                                @keydown.space.prevent="clearFilters"
                            >
                                ✕
                            </button>
                            <span id="todos-search-description" class="sr-only">Busca por título, descripción o categoría</span>
                        </div>
                        <label for="todos-category" class="sr-only">Filtrar por categoría</label>
                        <select
                            id="todos-category"
                            v-model="selectedCategory"
                            class="px-4 py-3 rounded-xl border-2 border-pink-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-pink-500 dark:focus:border-pink-400 focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all"
                            aria-label="Filtrar por categoría"
                        >
                            <option value="">Todas las categorías</option>
                            <option
                                v-for="category in categories"
                                :key="category"
                                :value="category"
                            >
                                {{ category }}
                            </option>
                        </select>
                    </div>
                    <div v-if="searchQuery || selectedCategory" class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2 flex-wrap">
                        <span>Filtros activos:</span>
                        <span v-if="searchQuery" class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full">
                            Búsqueda: "{{ searchQuery }}"
                        </span>
                        <span v-if="selectedCategory" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full">
                            Categoría: {{ selectedCategory }}
                        </span>
                        <button
                            @click="clearFilters"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors text-xs font-semibold"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <div v-if="pendingTodos.length > 0" class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <span>⏳</span>
                        Pendientes ({{ pendingTodos.length }})
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="todo in pendingTodos"
                            :key="todo.id"
                            class="group bg-white dark:bg-gray-800 rounded-xl shadow-md border-2 border-pink-100 dark:border-gray-700 hover:border-pink-400 dark:hover:border-pink-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-4"
                        >
                            <div class="flex items-start gap-4">
                                <input
                                    type="checkbox"
                                    :checked="todo.is_completed"
                                    @change="toggleComplete(todo.id)"
                                    :aria-label="`Marcar ${todo.title} como ${todo.is_completed ? 'pendiente' : 'completada'}`"
                                    class="mt-1 w-5 h-5 text-pink-500 rounded focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 cursor-pointer"
                                />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-base">{{ todo.title }}</h4>
                                        <span
                                            v-if="todo.priority"
                                            :class="['px-2 py-1 rounded-full text-xs font-semibold border', getPriorityColor(todo.priority), 'dark:border-opacity-50']"
                                        >
                                            {{ todo.priority === 'high' ? '🔴 Alta' : todo.priority === 'medium' ? '🟡 Media' : '🟢 Baja' }}
                                        </span>
                                    </div>
                                    <p v-if="todo.description" class="text-gray-600 dark:text-gray-300 text-sm mb-1 line-clamp-1">{{ todo.description }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span v-if="todo.due_date">
                                            📅 {{ new Date(todo.due_date).toLocaleDateString('es-ES') }}
                                        </span>
                                        <span v-if="todo.category">🏷️ {{ todo.category }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('todos.edit', todo.id)"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-200 transition-all"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="deleteTodo(todo.id)"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="completedTodos.length > 0" class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <span>✅</span>
                            Completadas ({{ completedTodos.length }})
                        </h3>
                        <button
                            @click="showCompleted = !showCompleted"
                            :aria-expanded="showCompleted"
                            aria-label="Mostrar u ocultar tareas completadas"
                            class="text-sm text-gray-600 hover:text-gray-800 font-semibold px-3 py-1 rounded-lg hover:bg-gray-100 transition-all focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                            @keydown.enter="showCompleted = !showCompleted"
                            @keydown.space.prevent="showCompleted = !showCompleted"
                        >
                            <span aria-hidden="true">{{ showCompleted ? '👁️' : '👁️‍🗨️' }}</span>
                            <span class="sr-only">{{ showCompleted ? 'Ocultar' : 'Mostrar' }}</span>
                            <span class="ml-1">{{ showCompleted ? 'Ocultar' : 'Mostrar' }}</span>
                        </button>
                    </div>
                    <div v-show="showCompleted" class="space-y-3">
                        <div
                            v-for="todo in completedTodos"
                            :key="todo.id"
                            class="bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 opacity-75 hover:opacity-100 transition-opacity"
                        >
                            <div class="flex items-start gap-4">
                                <input
                                    type="checkbox"
                                    checked
                                    @change="toggleComplete(todo.id)"
                                    class="mt-1 w-5 h-5 text-pink-500 rounded focus:ring-pink-500"
                                />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-600 dark:text-gray-400 text-sm line-through">{{ todo.title }}</h4>
                                    <p v-if="todo.description" class="text-gray-500 dark:text-gray-500 text-xs mt-1 line-clamp-1">{{ todo.description }}</p>
                                </div>
                                <button
                                    @click="deleteTodo(todo.id)"
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="paginationLinks.length > 3"
                    class="mb-6 flex flex-wrap justify-center gap-2"
                >
                    <Link
                        v-for="link in paginationLinks"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'rounded-xl border px-4 py-2 font-semibold transition-all',
                            link.active
                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white border-pink-600 shadow-lg'
                                : 'bg-white/90 text-gray-700 border-rose-200 hover:border-rose-400 hover:bg-rose-50',
                            !link.url ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:scale-105'
                        ]"
                        v-html="link.label"
                    />
                </div>

                <EmptyState
                    v-if="todoList.length === 0"
                    icon="📝"
                    :title="searchQuery || selectedCategory ? 'No se encontraron tareas' : 'No hay tareas aún'"
                    :description="searchQuery || selectedCategory ? 'Intenta con otros filtros o crea una nueva tarea.' : 'Crea tu primera tarea para comenzar a organizarte'"
                    action-label="Crear Primera Tarea"
                    :action-route="'todos.create'"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
