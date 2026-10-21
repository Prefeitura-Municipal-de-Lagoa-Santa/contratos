<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    FileText, 
    Plus, 
    Eye, 
    Edit, 
    Trash2,
    Search,
    Calendar,
    DollarSign,
    Building2,
    Download
} from 'lucide-vue-next';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    contracts: Object,
});

const searchQuery = ref('');

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusClass = (status) => {
    const classes = {
        'Ativo': 'bg-green-100 text-green-800',
        'Pendente': 'bg-yellow-100 text-yellow-800',
        'Em Análise': 'bg-blue-100 text-blue-800',
        'Suspenso': 'bg-orange-100 text-orange-800',
        'Encerrado': 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const deleteContract = (contractId) => {
    if (confirm('Tem certeza que deseja excluir este contrato?')) {
        router.delete(route('contracts.destroy', contractId));
    }
};

const exportToPdf = () => {
    const doc = new jsPDF('landscape');
    
    // Título
    doc.setFontSize(18);
    doc.setTextColor(79, 70, 229); // Indigo
    doc.text('Relatório de Documentos', 14, 22);
    
    // Data de geração
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text(`Gerado em: ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR')}`, 14, 30);
    
    // Dados da tabela
    const tableData = props.contracts.data.map(contract => [
        contract.code || 'N/A',
        contract.title || 'N/A',
        contract.supplier?.trade_name || 'N/A',
        contract.contract_type?.name || 'N/A',
        contract.status || 'N/A',
        `${formatDate(contract.start_date)} - ${formatDate(contract.end_date)}`,
        formatCurrency(contract.total)
    ]);
    
    // Gerar tabela
    autoTable(doc, {
        startY: 38,
        head: [['Código', 'Título', 'Fornecedor', 'Tipo', 'Status', 'Período', 'Valor']],
        body: tableData,
        theme: 'striped',
        headStyles: {
            fillColor: [79, 70, 229],
            textColor: 255,
            fontSize: 10,
            fontStyle: 'bold'
        },
        bodyStyles: {
            fontSize: 9
        },
        alternateRowStyles: {
            fillColor: [245, 245, 250]
        },
        columnStyles: {
            0: { cellWidth: 30 },
            1: { cellWidth: 50 },
            2: { cellWidth: 40 },
            3: { cellWidth: 30 },
            4: { cellWidth: 25 },
            5: { cellWidth: 45 },
            6: { cellWidth: 30 }
        },
        margin: { top: 38 }
    });
    
    // Rodapé
    const pageCount = doc.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(
            `Página ${i} de ${pageCount} - Sistema de Documentos`,
            doc.internal.pageSize.width / 2,
            doc.internal.pageSize.height - 10,
            { align: 'center' }
        );
    }
    
    // Download
    doc.save(`documentos_${new Date().toISOString().split('T')[0]}.pdf`);
};
</script>

<template>
    <Head title="Contratos" />

    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Contratos</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie todos os contratos do sistema
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="exportToPdf"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            <Download class="h-4 w-4" />
                            Exportar PDF
                        </button>
                        <Link
                            :href="route('contracts.create')"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <Plus class="h-4 w-4" />
                            Novo Contrato
                        </Link>
                    </div>
                </div>

                <!-- Contracts Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="contracts.data.length === 0" class="p-12 text-center">
                        <FileText class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhum contrato encontrado</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comece criando seu primeiro contrato.
                        </p>
                        <Link
                            :href="route('contracts.create')"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            <Plus class="h-4 w-4" />
                            Criar Contrato
                        </Link>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Código / Título
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Fornecedor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Período
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Valor
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr 
                                    v-for="contract in contracts.data" 
                                    :key="contract.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                                <FileText class="h-5 w-5 text-indigo-600" />
                                            </div>
                                            <div>
                                                <div class="font-mono text-sm font-medium text-gray-900">
                                                    {{ contract.code }}
                                                </div>
                                                <div class="text-sm text-gray-500 max-w-xs truncate">
                                                    {{ contract.title }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Building2 class="h-4 w-4 text-gray-400" />
                                            <span class="text-sm text-gray-900">
                                                {{ contract.supplier?.trade_name || 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-700">
                                            {{ contract.contract_type?.name || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span 
                                            :class="getStatusClass(contract.status)"
                                            class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        >
                                            {{ contract.status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-500">
                                            <Calendar class="h-4 w-4" />
                                            <span>{{ formatDate(contract.start_date) }} - {{ formatDate(contract.end_date) }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-1 text-sm font-medium text-gray-900">
                                            <DollarSign class="h-4 w-4 text-green-500" />
                                            {{ formatCurrency(contract.total) }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('contracts.show', contract.id)"
                                                class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-indigo-600 transition"
                                                title="Visualizar"
                                            >
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                            <Link
                                                :href="route('contracts.edit', contract.id)"
                                                class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-yellow-600 transition"
                                                title="Editar"
                                            >
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                            <button
                                                @click="deleteContract(contract.id)"
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
                    <div v-if="contracts.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ contracts.from }}</span> a 
                                <span class="font-medium">{{ contracts.to }}</span> de 
                                <span class="font-medium">{{ contracts.total }}</span> contratos
                            </div>
                            <nav class="flex gap-1">
                                <Link
                                    v-for="link in contracts.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-md transition',
                                        link.active 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'text-gray-700 hover:bg-gray-100',
                                        !link.url && 'opacity-50 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
