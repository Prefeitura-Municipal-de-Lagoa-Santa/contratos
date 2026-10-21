<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    FileText,
    Plus,
    Eye,
    Calendar,
    User,
    Building2,
    ArrowRight,
    Search,
    FileStack,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Input } from '@/components/ui/input';

const props = defineProps({
    oficios: Object,
});

const searchQuery = ref('');

const filteredOficios = computed(() => {
    if (!searchQuery.value) return props.oficios.data;
    const query = searchQuery.value.toLowerCase();
    return props.oficios.data.filter(oficio =>
        oficio.code.toLowerCase().includes(query) ||
        oficio.subject.toLowerCase().includes(query) ||
        oficio.recipient_name.toLowerCase().includes(query)
    );
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Ofícios" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                            <FileStack class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Ofícios</h1>
                            <p class="text-gray-600">Documentos oficiais para comunicação externa</p>
                        </div>
                    </div>
                    <Link
                        href="/oficios/novo"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl"
                    >
                        <Plus class="h-5 w-5" />
                        Novo Ofício
                    </Link>
                </div>

                <!-- Search -->
                <div class="mb-6">
                    <div class="relative max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por número, assunto ou destinatário..."
                            class="w-full rounded-xl border-gray-200 bg-white py-3 pl-10 text-sm shadow-sm"
                        />
                    </div>
                </div>

                <!-- Oficios List -->
                <div v-if="filteredOficios.length > 0" class="space-y-4">
                    <div
                        v-for="oficio in filteredOficios"
                        :key="oficio.id"
                        class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-xl"
                    >
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center rounded-lg bg-indigo-100 px-3 py-1 text-sm font-bold text-indigo-700">
                                        OF {{ oficio.code }}
                                    </span>
                                    <span class="flex items-center gap-1 text-sm text-gray-500">
                                        <Calendar class="h-4 w-4" />
                                        {{ formatDate(oficio.date) }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ oficio.subject }}</h3>
                                
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="flex items-center gap-1.5">
                                        <User class="h-4 w-4 text-gray-400" />
                                        <span class="font-medium">Para:</span> {{ oficio.recipient_name }}
                                    </span>
                                    <span v-if="oficio.recipient_institution" class="flex items-center gap-1.5">
                                        <Building2 class="h-4 w-4 text-gray-400" />
                                        {{ oficio.recipient_institution }}
                                    </span>
                                </div>
                            </div>
                            
                            <Link
                                :href="`/oficios/${oficio.id}`"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:bg-indigo-100 hover:text-indigo-700"
                            >
                                <Eye class="h-4 w-4" />
                                Visualizar
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center justify-center rounded-2xl bg-white py-16 shadow-lg">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-100 mb-4">
                        <FileText class="h-10 w-10 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Nenhum ofício encontrado</h3>
                    <p class="text-gray-500 mb-6">Comece criando seu primeiro ofício</p>
                    <Link
                        href="/oficios/novo"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg"
                    >
                        <Plus class="h-5 w-5" />
                        Criar Primeiro Ofício
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="oficios.links && oficios.links.length > 3" class="mt-8 flex justify-center gap-2">
                    <template v-for="link in oficios.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                            :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
