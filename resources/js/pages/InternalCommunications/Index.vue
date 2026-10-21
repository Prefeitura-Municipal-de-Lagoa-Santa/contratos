<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Mail,
    Plus,
    Eye,
    FileDown,
    Search,
    Calendar,
    User,
    Building2,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next';

const props = defineProps({
    communications: Object,
});

const searchQuery = ref('');

const filteredCommunications = computed(() => {
    if (!searchQuery.value) {
        return props.communications.data;
    }
    const query = searchQuery.value.toLowerCase();
    return props.communications.data.filter(comm => 
        comm.code.toLowerCase().includes(query) ||
        comm.subject.toLowerCase().includes(query) ||
        comm.sender_name.toLowerCase().includes(query) ||
        comm.recipient_name.toLowerCase().includes(query)
    );
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Comunicações Internas" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                            <Mail class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Comunicações Internas</h1>
                            <p class="text-sm text-gray-600">Gerencie as comunicações internas da prefeitura</p>
                        </div>
                    </div>
                    <Link
                        href="/comunicacoes-internas/nova"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:shadow-blue-500/40"
                    >
                        <Plus class="h-5 w-5" />
                        Nova Comunicação
                    </Link>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por número, assunto, remetente ou destinatário..."
                            class="block w-full rounded-xl border-gray-200 bg-white py-3.5 pl-12 pr-4 text-sm shadow-sm transition-all duration-200 placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>
                </div>

                <!-- Communications List -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Número
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Data
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Assunto
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Remetente
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Destinatário
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="comm in filteredCommunications"
                                    :key="comm.id"
                                    class="transition-colors duration-150 hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-2 rounded-lg bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                                            <Mail class="h-4 w-4" />
                                            {{ comm.code }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <Calendar class="h-4 w-4 text-gray-400" />
                                            {{ formatDate(comm.date) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs truncate text-sm font-medium text-gray-900">
                                            {{ comm.subject }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                                                <User class="h-4 w-4 text-emerald-600" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ comm.sender_name }}</div>
                                                <div v-if="comm.department" class="flex items-center gap-1 text-xs text-gray-500">
                                                    <Building2 class="h-3 w-3" />
                                                    {{ comm.department }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100">
                                                <User class="h-4 w-4 text-purple-600" />
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ comm.recipient_name }}</div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/comunicacoes-internas/${comm.id}`"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-200"
                                            >
                                                <Eye class="h-4 w-4" />
                                                Ver
                                            </Link>
                                            <a
                                                :href="`/comunicacoes-internas/${comm.id}/pdf`"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 transition-all duration-200 hover:bg-blue-200"
                                            >
                                                <FileDown class="h-4 w-4" />
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredCommunications.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <Mail class="h-12 w-12 text-gray-300" />
                                            <p class="mt-4 text-sm text-gray-500">Nenhuma comunicação encontrada</p>
                                            <Link
                                                href="/comunicacoes-internas/nova"
                                                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                                            >
                                                <Plus class="h-4 w-4" />
                                                Criar primeira comunicação
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="communications.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="text-sm text-gray-600">
                            Mostrando <span class="font-medium">{{ communications.from }}</span> a <span class="font-medium">{{ communications.to }}</span> de <span class="font-medium">{{ communications.total }}</span> resultados
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                v-if="communications.prev_page_url"
                                :href="communications.prev_page_url"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                            >
                                <ChevronLeft class="h-4 w-4" />
                                Anterior
                            </Link>
                            <Link
                                v-if="communications.next_page_url"
                                :href="communications.next_page_url"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                            >
                                Próximo
                                <ChevronRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
