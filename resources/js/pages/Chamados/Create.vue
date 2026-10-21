<script setup lang="ts">
import FieldHelp from '@/components/FieldHelp.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    ChevronDown,
    LifeBuoy,
    Paperclip,
    Send,
    ShieldCheck,
    X,
} from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps({
    categories: Array,
});

const categoryLabels = {
    contrato: 'Contrato',
    novo_processo: 'Novo processo',
    novo_contrato: 'Novo processo',
    aditivo: 'Aditivo',
    compra_direta: 'Compra direta',
    renovacao: 'Renovação',
    documentacao: 'Documentação',
    fiscalizacao: 'Fiscalização',
    pagamento: 'Pagamento',
    outro: 'Outro assunto',
};

const fieldControlClass =
    'w-full rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-3.5 font-sans text-[15px] font-normal leading-6 tracking-[0.01em] text-slate-800 shadow-sm shadow-slate-900/[0.03] transition duration-200 placeholder:text-sm placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 hover:border-indigo-200 hover:bg-white focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:hover:border-indigo-700 dark:hover:bg-slate-800';

const form = useForm<{
    subject: string;
    category: string;
    description: string;
    specifications: string;
    justification: string;
    ficha: string;
    dotacao: string;
    in_pca: boolean | null;
    involves_other_secretariats: boolean | null;
    other_fichas: string[];
    attachments: File[];
}>({
    subject: '',
    category: '',
    description: '',
    specifications: '',
    justification: '',
    ficha: '',
    dotacao: '',
    in_pca: null,
    involves_other_secretariats: null,
    other_fichas: [],
    attachments: [],
});

const needsContractDetails = computed(() =>
    ['novo_processo', 'compra_direta'].includes(form.category),
);

const sanitizeNumericInput = (
    event: Event,
    updateValue: (value: string) => void,
) => {
    const input = event.target;

    if (input instanceof HTMLInputElement) {
        const numericValue = input.value.replace(/\D/g, '');
        input.value = numericValue;
        updateValue(numericValue);
    }
};

watch(needsContractDetails, (isRequired) => {
    if (!isRequired) {
        form.specifications = '';
        form.justification = '';
        form.ficha = '';
        form.dotacao = '';
        form.in_pca = null;
        form.involves_other_secretariats = null;
        form.other_fichas = [];
    }
});

watch(
    () => form.involves_other_secretariats,
    (involvesOtherSecretariats) => {
        if (!involvesOtherSecretariats) {
            form.other_fichas = [];
        } else if (form.other_fichas.length === 0) {
            form.other_fichas = [''];
        }
    },
);

const selectFiles = (event: Event) => {
    const target = event.target;

    if (target instanceof HTMLInputElement) {
        form.attachments = Array.from(target.files ?? []);
    }
};

const removeFile = (index: number) => {
    form.attachments.splice(index, 1);
};

const submit = () => {
    if (needsContractDetails.value && form.in_pca === false) {
        return;
    }

    form.post('/chamados', { forceFormData: true });
};
</script>

<template>
    <Head title="Abrir demanda" />

    <AppLayout>
        <div
            class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50/70 to-indigo-50/70 pb-4 pt-1 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 sm:pb-6 sm:pt-2"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -right-24 top-20 h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl dark:bg-indigo-500/10"
            />
            <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <Link
                    href="/chamados"
                    class="mb-2 inline-flex items-center gap-2 rounded-lg px-2 py-1 text-sm font-medium text-slate-500 transition hover:bg-white/70 hover:text-indigo-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Voltar para Demandas
                </Link>

                <div class="mb-3 flex items-center gap-5 sm:mb-4">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg shadow-indigo-600/20 ring-1 ring-white/60 dark:ring-white/10"
                    >
                        <LifeBuoy class="h-8 w-8 text-white" />
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl"
                        >
                            Nova demanda
                        </h1>
                        <p
                            class="mt-1 text-sm text-slate-600 dark:text-slate-400 sm:text-base"
                        >
                            Preencha os dados abaixo para encaminhar sua demanda
                            à Gestão de Contratos.
                        </p>
                    </div>
                </div>

                <form
                    @submit.prevent="submit"
                    class="space-y-6 rounded-3xl border border-white/80 bg-white/95 p-5 shadow-[0_24px_70px_-30px_rgba(15,23,42,0.28)] backdrop-blur dark:border-slate-700 dark:bg-slate-900/95 dark:shadow-black/20 sm:p-8"
                >
                    <div class="space-y-1.5">
                        <FieldHelp
                            field-id="subject"
                            label="Assunto da demanda"
                            description="Escreva um título curto que identifique sua necessidade."
                        />
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            maxlength="150"
                            required
                            placeholder="Ex.: Solicitação de novo processo"
                            :class="fieldControlClass"
                        />
                        <p
                            v-if="form.errors.subject"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.subject }}
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <FieldHelp
                            field-id="category"
                            label="Categoria da demanda"
                            description="Selecione a categoria que melhor descreve sua demanda. Os campos necessários serão exibidos em seguida."
                        />
                        <div class="relative">
                            <select
                                id="category"
                                v-model="form.category"
                                required
                                :class="[
                                    fieldControlClass,
                                    'appearance-none pr-11',
                                ]"
                            >
                                <option value="" disabled>
                                    Selecione uma categoria
                                </option>
                                <option
                                    v-for="category in props.categories"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ categoryLabels[category] }}
                                </option>
                            </select>
                            <ChevronDown
                                aria-hidden="true"
                                class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            />
                        </div>
                        <p
                            v-if="form.errors.category"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.category }}
                        </p>
                    </div>

                    <template v-if="needsContractDetails">
                        <div class="space-y-1.5">
                            <FieldHelp
                                field-id="specifications"
                                label="Especificações do objeto"
                                description="Informe o que precisa ser contratado ou adquirido, incluindo requisitos, quantidades e características."
                            />
                            <textarea
                                id="specifications"
                                v-model="form.specifications"
                                rows="5"
                                maxlength="10000"
                                required
                                placeholder="Informe os detalhes, requisitos e características do objeto solicitado."
                                :class="[
                                    fieldControlClass,
                                    'resize-y leading-6',
                                ]"
                            />
                            <p
                                v-if="form.errors.specifications"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.specifications }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <FieldHelp
                                field-id="justification"
                                label="Justificativa"
                                description="Conte por que essa demanda é necessária e qual necessidade do serviço ela atende."
                            />
                            <textarea
                                id="justification"
                                v-model="form.justification"
                                rows="5"
                                maxlength="10000"
                                required
                                placeholder="Explique a necessidade e o motivo da solicitação."
                                :class="[
                                    fieldControlClass,
                                    'resize-y leading-6',
                                ]"
                            />
                            <p
                                v-if="form.errors.justification"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.justification }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <FieldHelp
                                field-id="ficha"
                                label="Ficha"
                                description="Informe o número ou código da ficha orçamentária vinculada à demanda."
                            />
                            <input
                                id="ficha"
                                v-model="form.ficha"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="100"
                                required
                                placeholder="Digite apenas números."
                                @input="
                                    sanitizeNumericInput(
                                        $event,
                                        (value) => (form.ficha = value),
                                    )
                                "
                                :class="fieldControlClass"
                            />
                            <p
                                v-if="form.errors.ficha"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.ficha }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <FieldHelp
                                field-id="dotacao"
                                label="Dotação"
                                description="Informe a dotação orçamentária vinculada à demanda, se disponível."
                            />
                            <input
                                id="dotacao"
                                v-model="form.dotacao"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="100"
                                placeholder="Digite apenas números."
                                @input="
                                    sanitizeNumericInput(
                                        $event,
                                        (value) => (form.dotacao = value),
                                    )
                                "
                                :class="fieldControlClass"
                            />
                            <p
                                v-if="form.errors.dotacao"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.dotacao }}
                            </p>
                        </div>

                        <fieldset class="space-y-3">
                            <legend
                                class="text-sm font-semibold text-slate-800 dark:text-slate-200"
                            >
                                A demanda está prevista no PCA?
                            </legend>
                            <div class="flex gap-3">
                                <button
                                    v-for="option in [
                                        { label: 'Sim', value: true },
                                        { label: 'Não', value: false },
                                    ]"
                                    :key="option.label"
                                    type="button"
                                    :aria-pressed="form.in_pca === option.value"
                                    @click="form.in_pca = option.value"
                                    :class="
                                        form.in_pca === option.value
                                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-600/20 dark:bg-indigo-950 dark:text-indigo-300'
                                            : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                    "
                                    class="min-w-20 rounded-xl border px-5 py-2.5 text-sm font-semibold transition"
                                >
                                    {{ option.label }}
                                </button>
                            </div>
                            <p
                                v-if="form.errors.in_pca"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.in_pca }}
                            </p>
                            <p
                                v-if="form.in_pca === false"
                                role="alert"
                                class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-200"
                            >
                                Não é possível prosseguir com a demanda, pois
                                ela não está prevista no PCA.
                            </p>
                        </fieldset>

                        <template v-if="form.in_pca !== false">
                            <fieldset class="space-y-3">
                                <legend
                                    class="text-sm font-semibold text-slate-800 dark:text-slate-200"
                                >
                                    A demanda envolve outras secretarias?
                                </legend>
                                <div class="flex gap-3">
                                    <button
                                        v-for="option in [
                                            { label: 'Sim', value: true },
                                            { label: 'Não', value: false },
                                        ]"
                                        :key="option.label"
                                        type="button"
                                        :aria-pressed="
                                            form.involves_other_secretariats ===
                                            option.value
                                        "
                                        @click="
                                            form.involves_other_secretariats =
                                                option.value
                                        "
                                        :class="
                                            form.involves_other_secretariats ===
                                            option.value
                                                ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-600/20 dark:bg-indigo-950 dark:text-indigo-300'
                                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                        "
                                        class="min-w-20 rounded-xl border px-5 py-2.5 text-sm font-semibold transition"
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                                <p
                                    v-if="
                                        form.errors.involves_other_secretariats
                                    "
                                    class="text-sm text-red-600"
                                >
                                    {{
                                        form.errors.involves_other_secretariats
                                    }}
                                </p>
                            </fieldset>

                            <div
                                v-if="form.involves_other_secretariats"
                                class="space-y-4"
                            >
                                <div
                                    v-for="(_, index) in form.other_fichas"
                                    :key="index"
                                    class="space-y-2"
                                >
                                    <label
                                        :for="`other-ficha-${index}`"
                                        class="block text-sm font-medium text-slate-800 dark:text-slate-200"
                                    >
                                        Ficha da outra secretaria
                                        {{ index + 1 }}
                                    </label>
                                    <input
                                        :id="`other-ficha-${index}`"
                                        v-model="form.other_fichas[index]"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        maxlength="100"
                                        required
                                        placeholder="Digite apenas números."
                                        @input="
                                            sanitizeNumericInput(
                                                $event,
                                                (value) =>
                                                    (form.other_fichas[index] =
                                                        value),
                                            )
                                        "
                                        :class="fieldControlClass"
                                    />
                                    <p
                                        v-if="
                                            form.errors[`other_fichas.${index}`]
                                        "
                                        class="text-sm text-red-600"
                                    >
                                        {{
                                            form.errors[`other_fichas.${index}`]
                                        }}
                                    </p>
                                </div>
                                <p
                                    v-if="form.errors.other_fichas"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.other_fichas }}
                                </p>
                                <button
                                    type="button"
                                    @click="form.other_fichas.push('')"
                                    class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-800 dark:text-indigo-300 dark:hover:bg-indigo-950"
                                >
                                    Adicionar outra ficha
                                </button>
                            </div>
                        </template>
                    </template>

                    <div v-if="form.in_pca !== false" class="space-y-1.5">
                        <FieldHelp
                            field-id="description"
                            label="Detalhes da demanda"
                            description="Inclua contexto, prazos, contrato relacionado e outras informações úteis para a análise."
                        />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            maxlength="10000"
                            required
                            placeholder="Descreva o que precisa, por que é necessário e qualquer informação que ajude na análise."
                            :class="[fieldControlClass, 'resize-y leading-6']"
                        />
                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div v-if="form.in_pca !== false">
                        <FieldHelp
                            field-id="attachments"
                            label="Anexos (obrigatório)"
                            description="Anexe o DFD (Documento de Formalização da Demanda) neste campo. O envio de pelo menos um arquivo PDF é obrigatório."
                        />
                        <label
                            for="attachments"
                            class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-4 py-4 text-center transition hover:border-indigo-400 hover:bg-indigo-50/50 dark:border-slate-600 dark:bg-slate-800/60 dark:hover:border-indigo-500 dark:hover:bg-indigo-950/30"
                        >
                            <span
                                class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200 transition group-hover:scale-105 dark:bg-slate-700 dark:text-indigo-300 dark:ring-slate-600"
                            >
                                <Paperclip class="h-5 w-5" />
                            </span>
                            <span
                                class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                Clique para anexar arquivos
                            </span>
                            <span
                                class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Anexe o DFD em PDF · obrigatório
                            </span>
                            <input
                                id="attachments"
                                type="file"
                                multiple
                                accept=".pdf,application/pdf"
                                required
                                @change="selectFiles"
                                class="sr-only"
                            />
                        </label>
                        <p
                            v-if="form.errors.attachments"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.attachments }}
                        </p>
                        <ul
                            v-if="form.attachments.length"
                            class="mt-3 space-y-2"
                        >
                            <li
                                v-for="(file, index) in form.attachments"
                                :key="`${file.name}-${file.lastModified}`"
                                class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <span
                                    class="flex min-w-0 items-center gap-2 truncate"
                                >
                                    <Paperclip
                                        class="h-4 w-4 shrink-0 text-gray-400"
                                    />
                                    <span class="truncate">{{
                                        file.name
                                    }}</span>
                                </span>
                                <button
                                    type="button"
                                    :aria-label="`Remover ${file.name}`"
                                    @click="removeFile(index)"
                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                >
                                    <X class="h-4 w-4" />
                                </button>
                            </li>
                        </ul>
                        <p
                            v-for="(_, index) in form.attachments"
                            :key="`attachment-error-${index}`"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors[`attachments.${index}`] }}
                        </p>
                    </div>

                    <div
                        class="flex flex-col-reverse justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center"
                    >
                        <p
                            class="hidden items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 sm:flex"
                        >
                            <ShieldCheck
                                class="h-4 w-4 text-emerald-600 dark:text-emerald-400"
                            />
                            Seus dados serão enviados com segurança
                        </p>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row">
                            <Link
                                href="/chamados"
                                class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="
                                    form.processing ||
                                    (needsContractDetails &&
                                        form.in_pca === false)
                                "
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-indigo-600/30 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
                            >
                                <Send v-if="!form.processing" class="h-4 w-4" />
                                <CheckCircle2
                                    v-else
                                    class="h-4 w-4 animate-pulse"
                                />
                                {{
                                    form.processing
                                        ? 'Enviando...'
                                        : 'Enviar Demanda'
                                }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
