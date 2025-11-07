<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Todo {
    id: number;
    title: string;
    description?: string;
    is_completed: boolean;
    due_date?: string;
    priority: 'low' | 'medium' | 'high';
    category?: string;
}

interface Props {
    todos: Todo[];
}

const props = defineProps<Props>();

const pendingTodos = computed(() => 
    props.todos.filter(t => !t.is_completed)
);

const completedTodos = computed(() => 
    props.todos.filter(t => t.is_completed)
);

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
        case 'high': return 'bg-red-100 text-red-700';
        case 'medium': return 'bg-yellow-100 text-yellow-700';
        case 'low': return 'bg-green-100 text-green-700';
        default: return 'bg-gray-100 text-gray-700';
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
                <!-- Pending Todos -->
                <div v-if="pendingTodos.length > 0" class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span>⏳</span>
                        Pendientes ({{ pendingTodos.length }})
                    </h3>
                    <div class="max-h-96 overflow-y-auto space-y-2 pr-2">
                        <div
                            v-for="todo in pendingTodos"
                            :key="todo.id"
                            class="bg-white rounded-lg shadow-sm border-2 border-pink-100 hover:border-pink-300 transition-all p-3"
                        >
                            <div class="flex items-start gap-4">
                                <input
                                    type="checkbox"
                                    :checked="todo.is_completed"
                                    @change="toggleComplete(todo.id)"
                                    class="mt-1 w-5 h-5 text-pink-500 rounded focus:ring-pink-500"
                                />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-900 text-base">{{ todo.title }}</h4>
                                        <span
                                            v-if="todo.priority"
                                            :class="['px-2 py-1 rounded-full text-xs font-semibold', getPriorityColor(todo.priority)]"
                                        >
                                            {{ todo.priority === 'high' ? 'Alta' : todo.priority === 'medium' ? 'Media' : 'Baja' }}
                                        </span>
                                    </div>
                                    <p v-if="todo.description" class="text-gray-600 text-sm mb-1 line-clamp-1">{{ todo.description }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
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

                <!-- Completed Todos -->
                <div v-if="completedTodos.length > 0" class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span>✅</span>
                        Completadas ({{ completedTodos.length }})
                    </h3>
                    <div class="max-h-64 overflow-y-auto space-y-2 pr-2">
                        <div
                            v-for="todo in completedTodos"
                            :key="todo.id"
                            class="bg-gray-50 rounded-lg border-2 border-gray-200 p-3 opacity-75"
                        >
                            <div class="flex items-start gap-4">
                                <input
                                    type="checkbox"
                                    checked
                                    @change="toggleComplete(todo.id)"
                                    class="mt-1 w-5 h-5 text-pink-500 rounded focus:ring-pink-500"
                                />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-600 text-sm line-through">{{ todo.title }}</h4>
                                    <p v-if="todo.description" class="text-gray-500 text-xs mt-1 line-clamp-1">{{ todo.description }}</p>
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

                <!-- Empty State -->
                <div
                    v-if="todos.length === 0"
                    class="text-center py-16 bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl border-2 border-pink-200"
                >
                    <span class="text-6xl block mb-4">📝</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay tareas aún</h3>
                    <p class="text-gray-600 mb-6">Crea tu primera tarea para comenzar a organizarte</p>
                    <Link
                        :href="route('todos.create')"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md hover:shadow-lg"
                    >
                        + Crear Primera Tarea
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

