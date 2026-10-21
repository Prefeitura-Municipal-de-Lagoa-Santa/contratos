<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    ArrowLeft,
    FileText, 
    Calendar,
    DollarSign,
    Building2,
    User,
    Edit,
    Upload,
    Download,
    Trash2,
    File,
    Paperclip,
    Eye,
    FileDown,
    Mail
} from 'lucide-vue-next';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    contract: Object,
    fileTypeOptions: Object,
});

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
        'Ativo': 'bg-green-100 text-green-800 border-green-200',
        'Pendente': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Em Análise': 'bg-blue-100 text-blue-800 border-blue-200',
        'Suspenso': 'bg-orange-100 text-orange-800 border-orange-200',
        'Encerrado': 'bg-gray-100 text-gray-800 border-gray-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

// Export to PDF function
const exportToPdf = () => {
    const doc = new jsPDF();
    const contract = props.contract;
    
    // Header
    doc.setFontSize(20);
    doc.setTextColor(79, 70, 229);
    doc.text('Detalhes do Documento', 105, 20, { align: 'center' });
    
    // Código do contrato
    doc.setFontSize(14);
    doc.setTextColor(60);
    doc.text(contract.code, 105, 30, { align: 'center' });
    
    // Linha separadora
    doc.setDrawColor(79, 70, 229);
    doc.setLineWidth(0.5);
    doc.line(20, 35, 190, 35);
    
    // Data de geração
    doc.setFontSize(9);
    doc.setTextColor(150);
    doc.text(`Gerado em: ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR')}`, 20, 42);
    
    // Título
    doc.setFontSize(14);
    doc.setTextColor(40);
    doc.text('Título:', 20, 55);
    doc.setFontSize(12);
    doc.setTextColor(80);
    const titleLines = doc.splitTextToSize(contract.title || 'N/A', 160);
    doc.text(titleLines, 20, 62);
    
    let yPos = 62 + (titleLines.length * 6) + 5;
    
    // Descrição
    if (contract.description) {
        doc.setFontSize(14);
        doc.setTextColor(40);
        doc.text('Descrição:', 20, yPos);
        doc.setFontSize(10);
        doc.setTextColor(80);
        const descLines = doc.splitTextToSize(contract.description, 160);
        doc.text(descLines, 20, yPos + 7);
        yPos += 7 + (descLines.length * 5) + 8;
    }
    
    // Tabela de informações principais
    autoTable(doc, {
        startY: yPos,
        head: [['Informação', 'Valor']],
        body: [
            ['Status', contract.status || 'N/A'],
            ['Fornecedor', contract.supplier?.trade_name || 'N/A'],
            ['Tipo de Contrato', contract.contract_type?.name || 'N/A'],
            ['Data de Início', formatDate(contract.start_date)],
            ['Data de Término', formatDate(contract.end_date)],
            ['Valor Total', formatCurrency(contract.total)],
        ],
        theme: 'striped',
        headStyles: {
            fillColor: [79, 70, 229],
            textColor: 255,
            fontSize: 11,
            fontStyle: 'bold'
        },
        bodyStyles: {
            fontSize: 10
        },
        columnStyles: {
            0: { cellWidth: 60, fontStyle: 'bold' },
            1: { cellWidth: 110 }
        },
        margin: { left: 20, right: 20 }
    });
    
    // Responsáveis
    yPos = doc.lastAutoTable.finalY + 10;
    
    autoTable(doc, {
        startY: yPos,
        head: [['Responsável', 'Nome', 'E-mail']],
        body: [
            ['Gestor', contract.manager || 'N/A', contract.manager_email || 'N/A'],
            ['Gestor Suplente', contract.deputy_manager || 'N/A', contract.deputy_manager_email || 'N/A'],
            ['Fiscal', contract.inspector || 'N/A', contract.inspector_email || 'N/A'],
            ['Fiscal Suplente', contract.deputy_inspector || 'N/A', contract.deputy_inspector_email || 'N/A'],
        ],
        theme: 'striped',
        headStyles: {
            fillColor: [99, 102, 241],
            textColor: 255,
            fontSize: 11,
            fontStyle: 'bold'
        },
        bodyStyles: {
            fontSize: 10
        },
        columnStyles: {
            0: { cellWidth: 45, fontStyle: 'bold' },
            1: { cellWidth: 60 },
            2: { cellWidth: 65 }
        },
        margin: { left: 20, right: 20 }
    });
    
    // Arquivos anexados (se houver)
    if (contract.files?.length > 0) {
        yPos = doc.lastAutoTable.finalY + 10;
        
        const filesData = contract.files.map(file => [
            file.original_name,
            props.fileTypeOptions[file.file_type] || file.file_type,
            formatFileSize(file.file_size)
        ]);
        
        autoTable(doc, {
            startY: yPos,
            head: [['Arquivos Anexados', 'Tipo', 'Tamanho']],
            body: filesData,
            theme: 'striped',
            headStyles: {
                fillColor: [34, 197, 94],
                textColor: 255,
                fontSize: 11,
                fontStyle: 'bold'
            },
            bodyStyles: {
                fontSize: 9
            },
            margin: { left: 20, right: 20 }
        });
    }
    
    // Rodapé
    const pageCount = doc.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(
            `Página ${i} de ${pageCount} - Sistema de Documentos`,
            105,
            doc.internal.pageSize.height - 10,
            { align: 'center' }
        );
    }
    
    // Download
    doc.save(`documento_${contract.code}_${new Date().toISOString().split('T')[0]}.pdf`);
};

// File upload form
const uploadForm = useForm({
    file: null,
    file_type: 'outros',
});

const fileInputRef = ref(null);
const selectedFileName = ref('');

const onFileSelected = (event) => {
    const file = event.target.files[0];
    if (file) {
        uploadForm.file = file;
        selectedFileName.value = file.name;
    }
};

const uploadFile = () => {
    if (!uploadForm.file) return;
    
    uploadForm.post(route('contract-files.store', props.contract.id), {
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            selectedFileName.value = '';
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        },
    });
};

const deleteFile = (fileId) => {
    if (confirm('Tem certeza que deseja excluir este arquivo?')) {
        router.delete(route('contract-files.destroy', fileId), {
            preserveScroll: true,
        });
    }
};

const formatFileSize = (bytes) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let index = 0;
    while (bytes >= 1024 && index < units.length - 1) {
        bytes /= 1024;
        index++;
    }
    return bytes.toFixed(2) + ' ' + units[index];
};

const getFileIcon = (mimeType) => {
    if (mimeType?.includes('pdf')) return 'text-red-500';
    if (mimeType?.includes('word') || mimeType?.includes('document')) return 'text-blue-500';
    if (mimeType?.includes('excel') || mimeType?.includes('spreadsheet')) return 'text-green-500';
    if (mimeType?.includes('image')) return 'text-purple-500';
    return 'text-gray-500';
};
</script>

<template>
    <Head :title="`Contrato ${contract.code}`" />

    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('contracts.index')"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                        >
                            <ArrowLeft class="h-5 w-5" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ contract.code }}</h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Detalhes do contrato
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="exportToPdf"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            <FileDown class="h-4 w-4" />
                            Exportar PDF
                        </button>
                        <Link
                            :href="route('contracts.edit', contract.id)"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <Edit class="h-4 w-4" />
                            Editar Contrato
                        </Link>
                    </div>
                </div>

                <!-- Contract Details Card -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="mb-6">
                            <span 
                                :class="getStatusClass(contract.status)"
                                class="inline-flex rounded-full border px-4 py-1.5 text-sm font-medium"
                            >
                                {{ contract.status }}
                            </span>
                        </div>

                        <!-- Title and Description -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900">{{ contract.title }}</h2>
                            <p v-if="contract.description" class="mt-2 text-gray-600">
                                {{ contract.description }}
                            </p>
                            <p v-else class="mt-2 text-gray-400 italic">
                                Sem descrição
                            </p>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Supplier -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                        <Building2 class="h-5 w-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Fornecedor</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.supplier?.trade_name || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contract Type -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                        <FileText class="h-5 w-5 text-purple-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Tipo de Contrato</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.contract_type?.name || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                        <Calendar class="h-5 w-5 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Data de Início</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ formatDate(contract.start_date) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                                        <Calendar class="h-5 w-5 text-red-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Data de Término</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ formatDate(contract.end_date) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Value -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                                        <DollarSign class="h-5 w-5 text-emerald-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Valor Total</p>
                                        <p class="text-lg font-bold text-emerald-600">
                                            {{ formatCurrency(contract.total) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                        <User class="h-5 w-5 text-indigo-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Gestor</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.manager || 'N/A' }}
                                        </p>
                                        <a v-if="contract.manager_email" :href="`mailto:${contract.manager_email}`" class="flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 mt-1">
                                            <Mail class="h-3 w-3" />
                                            {{ contract.manager_email }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Deputy Manager -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50">
                                        <User class="h-5 w-5 text-indigo-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Gestor Suplente</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.deputy_manager || 'N/A' }}
                                        </p>
                                        <a v-if="contract.deputy_manager_email" :href="`mailto:${contract.deputy_manager_email}`" class="flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 mt-1">
                                            <Mail class="h-3 w-3" />
                                            {{ contract.deputy_manager_email }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Inspector -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                                        <User class="h-5 w-5 text-amber-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Fiscal</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.inspector || 'N/A' }}
                                        </p>
                                        <a v-if="contract.inspector_email" :href="`mailto:${contract.inspector_email}`" class="flex items-center gap-1 text-xs text-amber-600 hover:text-amber-800 mt-1">
                                            <Mail class="h-3 w-3" />
                                            {{ contract.inspector_email }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Deputy Inspector -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50">
                                        <User class="h-5 w-5 text-amber-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Fiscal Suplente</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.deputy_inspector || 'N/A' }}
                                        </p>
                                        <a v-if="contract.deputy_inspector_email" :href="`mailto:${contract.deputy_inspector_email}`" class="flex items-center gap-1 text-xs text-amber-600 hover:text-amber-800 mt-1">
                                            <Mail class="h-3 w-3" />
                                            {{ contract.deputy_inspector_email }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Created By -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                        <User class="h-5 w-5 text-gray-600" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">Criado por</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ contract.user?.name || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files Section -->
                <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                <Paperclip class="h-5 w-5 text-indigo-600" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Arquivos do Contrato</h3>
                        </div>

                        <!-- Upload Form -->
                        <form @submit.prevent="uploadFile" class="mb-6 rounded-lg border-2 border-dashed border-gray-300 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo</label>
                                    <div class="flex items-center gap-3">
                                        <input
                                            ref="fileInputRef"
                                            type="file"
                                            @change="onFileSelected"
                                            class="hidden"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                                        />
                                        <button
                                            type="button"
                                            @click="fileInputRef?.click()"
                                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            <Upload class="h-4 w-4" />
                                            Selecionar Arquivo
                                        </button>
                                        <span v-if="selectedFileName" class="text-sm text-gray-600 truncate max-w-xs">
                                            {{ selectedFileName }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full sm:w-48">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                                    <select
                                        v-model="uploadForm.file_type"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option v-for="(label, value) in fileTypeOptions" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="!uploadForm.file || uploadForm.processing"
                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <Upload class="h-4 w-4" />
                                    {{ uploadForm.processing ? 'Enviando...' : 'Enviar' }}
                                </button>
                            </div>
                            <p v-if="uploadForm.errors.file" class="mt-2 text-sm text-red-600">{{ uploadForm.errors.file }}</p>
                        </form>

                        <!-- Files List -->
                        <div v-if="contract.files?.length" class="space-y-3">
                            <div
                                v-for="file in contract.files"
                                :key="file.id"
                                class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:bg-gray-50 transition"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <File :class="['h-8 w-8', getFileIcon(file.mime_type)]" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ file.original_name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5">
                                                {{ fileTypeOptions[file.file_type] || file.file_type }}
                                            </span>
                                            <span>{{ formatFileSize(file.file_size) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <a
                                        :href="route('contract-files.preview', file.id)"
                                        target="_blank"
                                        class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-green-600 transition"
                                        title="Visualizar"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </a>
                                    <a
                                        :href="route('contract-files.download', file.id)"
                                        class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-600 transition"
                                        title="Download"
                                    >
                                        <Download class="h-4 w-4" />
                                    </a>
                                    <button
                                        @click="deleteFile(file.id)"
                                        class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-red-600 transition"
                                        title="Excluir"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <File class="mx-auto h-12 w-12 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500">Nenhum arquivo anexado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

