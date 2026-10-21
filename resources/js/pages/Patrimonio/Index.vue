<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
    Package, 
    Plus, 
    Edit, 
    Trash2,
    Search,
    User,
    Tag,
    Calendar,
    ChevronLeft,
    ChevronRight
} from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
    patrimonios: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const updateSearch = debounce((value) => {
    router.get(route('patrimonios.index'), { search: value }, {
        preserveState: true,
        replace: true
    });
}, 300);

watch(search, (value) => {
    updateSearch(value);
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('pt-BR');
};

const deletePatrimonio = (id) => {
    if (confirm('Tem certeza que deseja excluir este patrimônio?')) {
        router.delete(route('patrimonios.destroy', id));
    }
};
</script>

<template>
    <Head title="Carga Patrimonial" />

    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Carga Patrimonial</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os bens e patrimônios em posse dos servidores
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('patrimonios.create')"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <Plus class="h-4 w-4" />
                            Novo Patrimônio
                        </Link>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6">
                    <div class="relative max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar por número, nome ou servidor..."
                            class="block w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div v-if="patrimonios.data.length === 0" class="p-12 text-center">
                        <Package class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum patrimônio encontrado</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comece cadastrando um novo bem patrimonial.
                        </p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Número / Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Servidor em Posse
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Data de Atribuição
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr 
                                    v-for="item in patrimonios.data" 
                                    :key="item.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                                <Package class="h-5 w-5 text-indigo-600" />
                                            </div>
                                            <div>
                                                <div class="font-mono text-sm font-medium text-gray-900 flex items-center gap-1">
                                                    <Tag class="h-3 w-3 text-gray-400" />
                                                    {{ item.numero_patrimonio }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ item.nome }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div v-if="item.user" class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center">
                                                <User class="h-4 w-4 text-slate-600" />
                                            </div>
                                            <span class="text-sm text-gray-900 font-medium">
                                                {{ item.user.name }}
                                            </span>
                                        </div>
                                        <div v-else class="text-sm text-gray-400 italic">
                                            Disponível em estoque
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div v-if="item.data_atribuicao" class="flex items-center gap-2 text-sm text-gray-500">
                                            <Calendar class="h-4 w-4" />
                                            <span>{{ formatDate(item.data_atribuicao) }}</span>
                                        </div>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('patrimonios.edit', item.id)"
                                                class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-indigo-600 transition"
                                                title="Editar"
                                            >
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                            <button
                                                @click="deletePatrimonio(item.id)"
                                                class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 transition"
                                                title="Excluir"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="patrimonios.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ patrimonios.from }}</span> a 
                                <span class="font-medium">{{ patrimonios.to }}</span> de 
                                <span class="font-medium">{{ patrimonios.total }}</span> registros
                            </div>
                            <nav class="flex gap-1" aria-label="Pagination">
                                <Link
                                    v-for="link in patrimonios.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :disabled="!link.url"
                                    :class="[
                                        'inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                        link.active 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700',
                                        !link.url && 'pointer-events-none opacity-50'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
