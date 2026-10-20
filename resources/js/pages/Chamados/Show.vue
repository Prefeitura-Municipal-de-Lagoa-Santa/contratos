<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    Download,
    LifeBuoy,
    Paperclip,
    X,
} from 'lucide-vue-next';

const props = defineProps({
    ticket: Object,
    canManage: Boolean,
    isAdmin: Boolean,
    statuses: Array,
    responsibles: Array,
});

const statusLabels = {
    aberto: 'Aberto',
    em_atendimento: 'Em atendimento',
    resolvido: 'Resolvido',
    aprovado: 'Aprovado',
    recusado: 'Recusado',
};

const categoryLabels = {
    contrato: 'Contrato',
    novo_contrato: 'Novo contrato',
    aditivo: 'Aditivo',
    compra_direta: 'Compra direta',
    renovacao: 'Renovação',
    documentacao: 'Documentação',
    fiscalizacao: 'Fiscalização',
    pagamento: 'Pagamento',
    outro: 'Outro assunto',
};

const form = useForm({
    status: props.ticket.status,
    management_response: props.ticket.management_response ?? '',
    responsible_user_id: props.ticket.responsible_user_id ?? '',
});

const isFinalDecision = ['aprovado', 'recusado'].includes(props.ticket.status);
const decisionForm = useForm({ status: '' });
const formatDate = (value) => new Date(value).toLocaleString('pt-BR');
const submit = () => form.patch(`/chamados/${props.ticket.id}`);
const decide = (status) =>
    decisionForm
        .transform(() => ({ status }))
        .patch(`/chamados/${props.ticket.id}/decisao`, {
            preserveScroll: true,
        });
</script>

<template>
    <Head :title="`Requisição #${ticket.id}`" />

    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8"
        >
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <Link
                    href="/chamados"
                    class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Voltar para Requisições
                </Link>

                <header class="mb-7 flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg"
                    >
                        <LifeBuoy class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Requisição #{{ ticket.id }}
                        </p>
                        <h1
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            {{ ticket.subject }}
                        </h1>
                    </div>
                </header>

                <section
                    class="space-y-6 rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-100 sm:p-8"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-800"
                        >
                            {{ categoryLabels[ticket.category] }}
                        </span>
                        <span
                            :class="[
                                'rounded-full px-3 py-1 text-sm font-semibold',
                                ticket.status === 'aprovado'
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : ticket.status === 'recusado'
                                      ? 'bg-red-100 text-red-800'
                                      : 'bg-gray-100 text-gray-700',
                            ]"
                        >
                            {{ statusLabels[ticket.status] }}
                        </span>
                    </div>

                    <div
                        v-if="canManage && !isFinalDecision"
                        class="flex flex-wrap gap-3 border-b border-gray-100 pb-6"
                    >
                        <button
                            type="button"
                            :disabled="decisionForm.processing"
                            @click="decide('aprovado')"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <Check class="h-4 w-4" />
                            Aprovar Requisição
                        </button>
                        <button
                            type="button"
                            :disabled="decisionForm.processing"
                            @click="decide('recusado')"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <X class="h-4 w-4" />
                            Recusar Requisição
                        </button>
                    </div>

                    <div
                        class="border-b border-gray-100 pb-5 text-sm text-gray-500"
                    >
                        Aberto por
                        <span class="font-medium text-gray-700">{{
                            ticket.user.name
                        }}</span>
                        em {{ formatDate(ticket.created_at) }}
                        <p class="mt-1">
                            Responsável:
                            <span class="font-medium text-gray-700">{{
                                ticket.responsible?.name ?? 'Não definido'
                            }}</span>
                        </p>
                    </div>

                    <div>
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Descrição
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.description }}
                        </p>
                    </div>

                    <div v-if="ticket.specifications">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Especificações do objeto
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.specifications }}
                        </p>
                    </div>

                    <div v-if="ticket.justification">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Justificativa
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.justification }}
                        </p>
                    </div>

                    <div v-if="ticket.ficha">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Ficha
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.ficha }}
                        </p>
                    </div>

                    <div v-if="ticket.attachments.length">
                        <h2
                            class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700"
                        >
                            <Paperclip class="h-4 w-4" />
                            Anexos
                        </h2>
                        <ul class="space-y-2">
                            <li
                                v-for="attachment in ticket.attachments"
                                :key="attachment.id"
                                class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 px-4 py-3"
                            >
                                <span
                                    class="min-w-0 truncate text-sm text-gray-700"
                                    >{{ attachment.original_name }}</span
                                >
                                <a
                                    :href="`/chamados/anexos/${attachment.id}/download`"
                                    class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                                >
                                    <Download class="h-4 w-4" />
                                    Baixar
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="ticket.management_response"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-5"
                    >
                        <h2 class="mb-2 text-sm font-semibold text-emerald-900">
                            Resposta da Gestão de Contratos
                        </h2>
                        <p class="whitespace-pre-wrap text-emerald-950">
                            {{ ticket.management_response }}
                        </p>
                    </div>

                    <form
                        v-if="canManage && !isFinalDecision"
                        @submit.prevent="submit"
                        class="space-y-5 border-t border-gray-100 pt-6"
                    >
                        <h2 class="text-lg font-semibold text-gray-900">
                            Atendimento da Requisição
                        </h2>
                        <div>
                            <label
                                for="status"
                                class="mb-2 block text-sm font-medium text-gray-700"
                                >Status</label
                            >
                            <select
                                id="status"
                                v-model="form.status"
                                required
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option
                                    v-for="status in props.statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ statusLabels[status] }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                        <div>
                            <label
                                for="responsible_user_id"
                                class="mb-2 block text-sm font-medium text-gray-700"
                                >Responsável</label
                            >
                            <select
                                id="responsible_user_id"
                                v-model="form.responsible_user_id"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Não definido</option>
                                <option
                                    v-for="responsible in props.responsibles"
                                    :key="responsible.id"
                                    :value="responsible.id"
                                >
                                    {{ responsible.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.responsible_user_id"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.responsible_user_id }}
                            </p>
                        </div>
                        <div>
                            <label
                                for="management_response"
                                class="mb-2 block text-sm font-medium text-gray-700"
                                >Resposta ao servidor</label
                            >
                            <textarea
                                id="management_response"
                                v-model="form.management_response"
                                rows="5"
                                maxlength="10000"
                                placeholder="Escreva uma resposta ou orientação para o servidor."
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p
                                v-if="form.errors.management_response"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.management_response }}
                            </p>
                        </div>
                        <div class="text-right">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{
                                    form.processing
                                        ? 'Salvando...'
                                        : 'Salvar atendimento'
                                }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
