<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Mail,
    User,
    Building2,
    FileText,
    Calendar,
    ArrowLeft,
    FileDown,
    Edit,
    Hash,
    Send,
} from 'lucide-vue-next';

const props = defineProps({
    communication: Object,
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

const formatFullDate = (dateString) => {
    const date = new Date(dateString);
    const months = [
        'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
        'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
    ];
    const day = date.getDate().toString().padStart(2, '0');
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    return `Lagoa Santa, ${day} de ${month} de ${year}`;
};
</script>

<template>
    <Head :title="`CI ${communication.code}`" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            href="/comunicacoes-internas"
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md transition-all duration-200 hover:shadow-lg"
                        >
                            <ArrowLeft class="h-5 w-5 text-gray-600" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                                Comunicação Interna
                            </h1>
                            <p class="text-sm text-gray-600">Visualização do documento</p>
                        </div>
                    </div>
                    <a
                        :href="`/comunicacoes-internas/${communication.id}/pdf`"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:shadow-blue-500/40"
                    >
                        <FileDown class="h-5 w-5" />
                        Exportar PDF
                    </a>
                </div>

                <!-- Document Preview -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <!-- Document Header -->
                    <div class="border-b border-gray-200 bg-gradient-to-r from-slate-800 to-slate-900 px-8 py-8 text-center">
                        <div class="mb-2 text-4xl">⚜</div>
                        <h2 class="text-xl font-bold uppercase tracking-wide text-white">
                            Prefeitura Municipal de Lagoa Santa
                        </h2>
                        <p class="mt-1 text-sm text-slate-300">Estado de Minas Gerais</p>
                    </div>

                    <!-- Document Number -->
                    <div class="border-b border-gray-100 bg-blue-50 px-8 py-4 text-center">
                        <div class="inline-flex items-center gap-2 rounded-xl bg-blue-100 px-6 py-2">
                            <Hash class="h-5 w-5 text-blue-600" />
                            <span class="text-lg font-bold text-blue-800">
                                COMUNICAÇÃO INTERNA Nº {{ communication.code }}
                            </span>
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="p-8">
                        <!-- Date -->
                        <div class="mb-8 text-right text-sm text-gray-600">
                            <Calendar class="mr-1 inline h-4 w-4" />
                            {{ formatFullDate(communication.date) }}
                        </div>

                        <!-- Sender Info -->
                        <div class="mb-6 rounded-xl bg-emerald-50 p-5">
                            <div class="mb-3 flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                    <Send class="h-4 w-4 text-emerald-600" />
                                </div>
                                <span class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Remetente</span>
                            </div>
                            <div class="space-y-2 pl-10">
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-emerald-600" />
                                    <span class="font-medium text-gray-900">{{ communication.sender_name }}</span>
                                </div>
                                <div v-if="communication.sender_role" class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="text-emerald-600">Cargo:</span>
                                    {{ communication.sender_role }}
                                </div>
                                <div v-if="communication.department" class="flex items-center gap-2 text-sm text-gray-600">
                                    <Building2 class="h-4 w-4 text-emerald-600" />
                                    {{ communication.department }}
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Info -->
                        <div class="mb-6 rounded-xl bg-purple-50 p-5">
                            <div class="mb-3 flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                                    <User class="h-4 w-4 text-purple-600" />
                                </div>
                                <span class="text-sm font-semibold uppercase tracking-wide text-purple-800">Destinatário</span>
                            </div>
                            <div class="space-y-2 pl-10">
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-purple-600" />
                                    <span class="font-medium text-gray-900">{{ communication.recipient_name }}</span>
                                </div>
                                <div v-if="communication.recipient_role" class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="text-purple-600">Cargo:</span>
                                    {{ communication.recipient_role }}
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="mb-6 rounded-xl bg-amber-50 p-5">
                            <div class="mb-2 flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100">
                                    <FileText class="h-4 w-4 text-amber-600" />
                                </div>
                                <span class="text-sm font-semibold uppercase tracking-wide text-amber-800">Assunto</span>
                            </div>
                            <div class="pl-10">
                                <p class="text-lg font-semibold text-gray-900">{{ communication.subject }}</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="my-8 flex items-center">
                            <div class="flex-1 border-t border-gray-200"></div>
                            <div class="px-4">
                                <Mail class="h-5 w-5 text-gray-300" />
                            </div>
                            <div class="flex-1 border-t border-gray-200"></div>
                        </div>

                        <!-- Content -->
                        <div class="prose prose-gray max-w-none">
                            <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ communication.content }}</div>
                        </div>

                        <!-- Signature Area -->
                        <div class="mt-12 text-center">
                            <p class="mb-12 italic text-gray-600">Atenciosamente,</p>
                            <div class="inline-block border-t border-gray-400 pt-3 px-8">
                                <p class="font-semibold text-gray-900">{{ communication.sender_name }}</p>
                                <p v-if="communication.sender_role" class="text-sm text-gray-600">{{ communication.sender_role }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Document Footer -->
                    <div class="border-t border-gray-200 bg-gray-50 px-8 py-4 text-center text-sm text-gray-500">
                        <p class="font-medium">Prefeitura Municipal de Lagoa Santa</p>
                        <p>Rua São João, 290 - Centro - Lagoa Santa/MG - CEP: 33400-000</p>
                        <p>www.lagoasanta.mg.gov.br</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
                    <Link
                        href="/comunicacoes-internas"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50"
                    >
                        <ArrowLeft class="h-5 w-5" />
                        Voltar para Lista
                    </Link>
                    <a
                        :href="`/comunicacoes-internas/${communication.id}/pdf`"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:shadow-blue-500/40"
                    >
                        <FileDown class="h-5 w-5" />
                        Baixar PDF
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
