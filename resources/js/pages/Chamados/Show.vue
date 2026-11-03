<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    ticketCategoryLabels,
    ticketStatusLabels,
} from '@/lib/contractTicketLabels';
import { formatDateTime } from '@/lib/dateFormat';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    Download,
    FileDown,
    LifeBuoy,
    Paperclip,
    X,
} from 'lucide-vue-next';

type Ticket = {
    id: number;
    subject: string;
    category: string;
    status: string;
    management_response: string | null;
    responsible_user_id: number | null;
    user: { name: string };
    responsible: { name: string } | null;
    created_at: string;
    demandante: string | null;
    demandado: string | null;
    responsavel: string | null;
    matricula: string | null;
    valor_estimado: string | null;
    data_demanda: string | null;
    duracao_contrato: string | null;
    description: string;
    solucao_sugerida: string | null;
    specifications: string | null;
    justification: string | null;
    quantity: number | null;
    unit: string | null;
    ficha: string | null;
    dotacao: string | null;
    in_pca: boolean | null;
    involves_other_secretariats: boolean | null;
    other_fichas: string[] | null;
    attachments: { id: number; original_name: string }[];
    messages: {
        id: number;
        message: string;
        created_at: string;
        author: { id: number; name: string; is_manager: boolean };
    }[];
};

const props = defineProps<{
    ticket: Ticket;
    canManage: boolean;
    isAdmin: boolean;
    statuses: string[];
    responsibles: { id: number; name: string }[];
}>();

const form = useForm({
    status: props.ticket.status,
    management_response: props.ticket.management_response ?? '',
    responsible_user_id: props.ticket.responsible_user_id ?? '',
});
const messageForm = useForm({ message: '' });

const isFinalDecision = ['aprovado', 'recusado'].includes(props.ticket.status);
const decisionForm = useForm({ status: '' });
const submit = () => form.patch(`/chamados/${props.ticket.id}`);
const sendMessage = () =>
    messageForm.post(`/chamados/${props.ticket.id}/mensagens`, {
        preserveScroll: true,
        onSuccess: () => messageForm.reset('message'),
    });
const decide = (status: string) =>
    decisionForm
        .transform(() => ({ status }))
        .patch(`/chamados/${props.ticket.id}/decisao`, {
            preserveScroll: true,
        });
</script>

<template>
    <Head :title="`Demanda #${ticket.id}`" />

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
                    Voltar para Demandas
                </Link>

                <header class="mb-7 flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg"
                    >
                        <LifeBuoy class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Demanda #{{ ticket.id }}
                        </p>
                        <h1
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            {{ ticket.subject }}
                        </h1>
                    </div>
                </header>

                <div class="mb-6">
                    <a
                        :href="`/chamados/${ticket.id}/pdf`"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        <FileDown class="h-4 w-4" />
                        {{
                            ticket.category === 'novo_processo'
                                ? 'Gerar DFD em PDF'
                                : 'Exportar para PDF'
                        }}
                    </a>
                </div>

                <section
                    class="space-y-6 rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-100 sm:p-8"
                >
                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-800"
                        >
                            {{ ticketCategoryLabels[ticket.category] }}
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
                            {{ ticketStatusLabels[ticket.status] }}
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
                            Aprovar Demanda
                        </button>
                        <button
                            type="button"
                            :disabled="decisionForm.processing"
                            @click="decide('recusado')"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <X class="h-4 w-4" />
                            Recusar Demanda
                        </button>
                    </div>

                    <div
                        class="border-b border-gray-100 pb-5 text-sm text-gray-500"
                    >
                        Aberto por
                        <span class="font-medium text-gray-700">{{
                            ticket.user.name
                        }}</span>
                        em {{ formatDateTime(ticket.created_at) }}
                        <p class="mt-1">
                            Responsável:
                            <span class="font-medium text-gray-700">{{
                                ticket.responsible?.name ?? 'Não definido'
                            }}</span>
                        </p>
                    </div>

                    <div
                        v-if="ticket.category === 'novo_processo'"
                        class="grid gap-5 border-b border-gray-100 pb-5 sm:grid-cols-2"
                    >
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Demandante
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ ticket.demandante }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Demandado
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ ticket.demandado }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Responsável
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ ticket.responsavel }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Matrícula
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ ticket.matricula || 'Não cadastrada' }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Valor
                            </h2>
                            <p class="text-gray-800">
                                {{
                                    Number(
                                        ticket.valor_estimado,
                                    ).toLocaleString('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL',
                                    })
                                }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Data
                            </h2>
                            <p class="text-gray-800">
                                {{
                                    ticket.data_demanda
                                        ? ticket.data_demanda
                                              .slice(0, 10)
                                              .split('-')
                                              .reverse()
                                              .join('/')
                                        : ''
                                }}
                            </p>
                        </div>
                        <div>
                            <h2
                                class="mb-2 text-sm font-semibold text-gray-700"
                            >
                                Duração do contrato
                            </h2>
                            <p class="whitespace-pre-wrap text-gray-800">
                                {{ ticket.duracao_contrato }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            {{
                                ticket.category === 'novo_processo'
                                    ? 'Problema Identificado'
                                    : 'Descrição'
                            }}
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.description }}
                        </p>
                    </div>

                    <div v-if="ticket.solucao_sugerida">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Solução sugerida
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.solucao_sugerida }}
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

                    <div v-if="ticket.quantity !== null">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Quantidade
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.quantity
                            }}<span v-if="ticket.unit"> {{ ticket.unit }}</span>
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

                    <div v-if="ticket.dotacao">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Dotação
                        </h2>
                        <p class="whitespace-pre-wrap text-gray-800">
                            {{ ticket.dotacao }}
                        </p>
                    </div>

                    <div v-if="ticket.in_pca !== null">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Prevista no PCA
                        </h2>
                        <p class="text-gray-800">
                            {{ ticket.in_pca ? 'Sim' : 'Não' }}
                        </p>
                    </div>

                    <div v-if="ticket.involves_other_secretariats !== null">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Envolve outras secretarias
                        </h2>
                        <p class="text-gray-800">
                            {{
                                ticket.involves_other_secretariats
                                    ? 'Sim'
                                    : 'Não'
                            }}
                        </p>
                    </div>

                    <div v-if="ticket.other_fichas?.length">
                        <h2 class="mb-2 text-sm font-semibold text-gray-700">
                            Fichas de outras secretarias
                        </h2>
                        <ul
                            class="list-inside list-disc space-y-1 text-gray-800"
                        >
                            <li
                                v-for="(ficha, index) in ticket.other_fichas"
                                :key="index"
                            >
                                {{ ficha }}
                            </li>
                        </ul>
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
                            Resposta anterior da Gestão de Contratos
                        </h2>
                        <p class="whitespace-pre-wrap text-emerald-950">
                            {{ ticket.management_response }}
                        </p>
                    </div>

                    <section
                        class="space-y-5 border-t border-gray-100 pt-6"
                        aria-labelledby="conversation-heading"
                    >
                        <div>
                            <h2
                                id="conversation-heading"
                                class="text-base font-semibold text-gray-900"
                            >
                                Conversa do atendimento
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Envie mensagens para conversar com o usuário ou
                                com a Gestão de Contratos.
                            </p>
                        </div>

                        <div
                            v-if="ticket.messages.length"
                            class="space-y-4"
                            aria-live="polite"
                        >
                            <article
                                v-for="message in ticket.messages"
                                :key="message.id"
                                :class="[
                                    'rounded-xl border p-4',
                                    message.author.is_manager
                                        ? 'border-indigo-100 bg-indigo-50/70'
                                        : 'border-slate-200 bg-slate-50',
                                ]"
                            >
                                <header
                                    class="mb-2 flex flex-wrap items-center justify-between gap-2"
                                >
                                    <div
                                        class="flex items-center gap-2 text-sm"
                                    >
                                        <span
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ message.author.name }}
                                        </span>
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                                message.author.is_manager
                                                    ? 'bg-indigo-100 text-indigo-700'
                                                    : 'bg-slate-200 text-slate-700',
                                            ]"
                                        >
                                            {{
                                                message.author.is_manager
                                                    ? 'Gestão'
                                                    : 'Solicitante'
                                            }}
                                        </span>
                                    </div>
                                    <time
                                        :datetime="message.created_at"
                                        class="text-xs text-gray-500"
                                    >
                                        {{ formatDateTime(message.created_at) }}
                                    </time>
                                </header>
                                <p
                                    class="whitespace-pre-wrap break-words text-sm text-gray-800"
                                >
                                    {{ message.message }}
                                </p>
                            </article>
                        </div>
                        <p
                            v-else
                            class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500"
                        >
                            Ainda não há mensagens nesta conversa.
                        </p>

                        <form @submit.prevent="sendMessage" class="space-y-3">
                            <div>
                                <label
                                    for="conversation-message"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Escreva uma mensagem
                                </label>
                                <textarea
                                    id="conversation-message"
                                    v-model="messageForm.message"
                                    rows="4"
                                    maxlength="5000"
                                    required
                                    placeholder="Digite sua mensagem para o atendimento."
                                    class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <p
                                    v-if="messageForm.errors.message"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ messageForm.errors.message }}
                                </p>
                            </div>
                            <div class="text-right">
                                <button
                                    type="submit"
                                    :disabled="
                                        messageForm.processing ||
                                        !messageForm.message.trim()
                                    "
                                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {{
                                        messageForm.processing
                                            ? 'Enviando...'
                                            : 'Enviar mensagem'
                                    }}
                                </button>
                            </div>
                        </form>
                    </section>

                    <form
                        v-if="canManage && !isFinalDecision"
                        @submit.prevent="submit"
                        class="space-y-5 border-t border-gray-100 pt-6"
                    >
                        <h2 class="text-base font-semibold text-gray-900">
                            Atendimento da Demanda
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
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option
                                    v-for="status in props.statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ ticketStatusLabels[status] }}
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
                                class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
