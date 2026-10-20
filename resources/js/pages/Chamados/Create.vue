<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, LifeBuoy, Paperclip, X } from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps({
    categories: Array,
});

const categoryLabels = {
    contrato: 'Contrato',
    novo_contrato: 'Novo contrato',
    aditivo: 'Aditivo',
    documentacao: 'Documentação',
    fiscalizacao: 'Fiscalização',
    pagamento: 'Pagamento',
    outro: 'Outro assunto',
};

const form = useForm<{
    subject: string;
    category: string;
    description: string;
    specifications: string;
    justification: string;
    attachments: File[];
}>({
    subject: '',
    category: '',
    description: '',
    specifications: '',
    justification: '',
    attachments: [],
});

const needsContractDetails = computed(() => ['novo_contrato', 'aditivo'].includes(form.category));

watch(needsContractDetails, (isRequired) => {
    if (!isRequired) {
        form.specifications = '';
        form.justification = '';
    }
});

const selectFiles = (event: Event) => {
    const target = event.target;

    if (target instanceof HTMLInputElement) {
        form.attachments = Array.from(target.files ?? []);
    }
};

const removeFile = (index: number) => {
    form.attachments.splice(index, 1);
};

const submit = () => form.post('/chamados', { forceFormData: true });
</script>

<template>
    <Head title="Abrir chamado" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <Link href="/chamados" class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                    <ArrowLeft class="h-4 w-4" />
                    Voltar para chamados
                </Link>

                <div class="mb-7 flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                        <LifeBuoy class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Abrir chamado</h1>
                        <p class="text-gray-600">Descreva sua solicitação para a Gestão de Contratos.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-100 sm:p-8">
                    <div>
                        <label for="subject" class="mb-2 block text-sm font-medium text-gray-700">Assunto</label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            maxlength="150"
                            required
                            placeholder="Ex.: Dúvida sobre o contrato"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <label for="category" class="mb-2 block text-sm font-medium text-gray-700">Tipo de solicitação</label>
                        <select
                            id="category"
                            v-model="form.category"
                            required
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="" disabled>Selecione uma categoria</option>
                            <option v-for="category in props.categories" :key="category" :value="category">
                                {{ categoryLabels[category] }}
                            </option>
                        </select>
                        <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                    </div>

                    <template v-if="needsContractDetails">
                        <div>
                            <label for="specifications" class="mb-2 block text-sm font-medium text-gray-700">Especificações</label>
                            <textarea
                                id="specifications"
                                v-model="form.specifications"
                                rows="5"
                                maxlength="10000"
                                required
                                placeholder="Informe os detalhes, requisitos e características do novo contrato ou aditivo."
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.specifications" class="mt-1 text-sm text-red-600">{{ form.errors.specifications }}</p>
                        </div>

                        <div>
                            <label for="justification" class="mb-2 block text-sm font-medium text-gray-700">Justificativa</label>
                            <textarea
                                id="justification"
                                v-model="form.justification"
                                rows="5"
                                maxlength="10000"
                                required
                                placeholder="Explique a necessidade e o motivo da solicitação."
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.justification" class="mt-1 text-sm text-red-600">{{ form.errors.justification }}</p>
                        </div>
                    </template>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Descreva o que precisa</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="7"
                            maxlength="10000"
                            required
                            placeholder="Inclua informações que ajudem a equipe a entender e atender sua solicitação."
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label for="attachments" class="mb-2 block text-sm font-medium text-gray-700">Anexos (opcional)</label>
                        <input
                            id="attachments"
                            type="file"
                            multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                            @change="selectFiles"
                            class="block w-full rounded-xl border border-gray-300 text-sm text-gray-700 file:mr-4 file:rounded-l-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-3 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <p class="mt-2 text-xs text-gray-500">Até 5 arquivos, máximo de 10 MB cada. Formatos: PDF, DOC, DOCX, XLS, XLSX, PNG e JPG.</p>
                        <p v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">{{ form.errors.attachments }}</p>
                        <ul v-if="form.attachments.length" class="mt-3 space-y-2">
                            <li
                                v-for="(file, index) in form.attachments"
                                :key="`${file.name}-${file.lastModified}`"
                                class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700"
                            >
                                <span class="flex min-w-0 items-center gap-2 truncate">
                                    <Paperclip class="h-4 w-4 shrink-0 text-gray-400" />
                                    <span class="truncate">{{ file.name }}</span>
                                </span>
                                <button type="button" :aria-label="`Remover ${file.name}`" @click="removeFile(index)" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600">
                                    <X class="h-4 w-4" />
                                </button>
                            </li>
                        </ul>
                        <p v-for="(_, index) in form.attachments" :key="`attachment-error-${index}`" class="mt-1 text-sm text-red-600">
                            {{ form.errors[`attachments.${index}`] }}
                        </p>
                    </div>

                    <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                        <Link href="/chamados" class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Enviando...' : 'Enviar chamado' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
