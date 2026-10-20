<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, LifeBuoy, Plus, Trash2 } from 'lucide-vue-next';

defineProps({
    tickets: Object,
    canManage: Boolean,
    isAdmin: Boolean,
});

const statusLabels = {
    aberto: 'Aberto',
    em_atendimento: 'Em atendimento',
    resolvido: 'Resolvido',
};

const statusStyles = {
    aberto: 'bg-amber-100 text-amber-800',
    em_atendimento: 'bg-blue-100 text-blue-800',
    resolvido: 'bg-emerald-100 text-emerald-800',
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

const formatDate = (value) => new Date(value).toLocaleDateString('pt-BR');

const deleteTicket = (ticket) => {
    if (
        window.confirm(
            `Tem certeza que deseja excluir o chamado #${ticket.id}?`,
        )
    ) {
        router.delete(`/chamados/${ticket.id}`);
    }
};
</script>

<template>
    <Head title="Chamados" />

    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <header
                    class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg"
                        >
                            <LifeBuoy class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold tracking-tight text-gray-900"
                            >
                                Chamados
                            </h1>
                            <p class="text-gray-600">
                                {{
                                    canManage
                                        ? 'Chamados recebidos pela Gestão de Contratos'
                                        : 'Acompanhe suas solicitações à Gestão de Contratos'
                                }}
                            </p>
                        </div>
                    </div>
                    <Link
                        href="/chamados/novo"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-indigo-700"
                    >
                        <Plus class="h-5 w-5" />
                        Abrir chamado
                    </Link>
                </header>

                <div v-if="tickets.data.length" class="space-y-4">
                    <article
                        v-for="ticket in tickets.data"
                        :key="ticket.id"
                        class="rounded-2xl bg-white p-6 shadow-lg shadow-gray-200/50 ring-1 ring-gray-100"
                    >
                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="min-w-0 flex-1">
                                <div
                                    class="mb-2 flex flex-wrap items-center gap-2"
                                >
                                    <span
                                        class="text-sm font-semibold text-gray-500"
                                        >Chamado #{{ ticket.id }}</span
                                    >
                                    <span
                                        :class="[
                                            statusStyles[ticket.status],
                                            'rounded-full px-3 py-1 text-xs font-semibold',
                                        ]"
                                    >
                                        {{ statusLabels[ticket.status] }}
                                    </span>
                                </div>
                                <h2
                                    class="truncate text-lg font-semibold text-gray-900"
                                >
                                    {{ ticket.subject }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ categoryLabels[ticket.category] }} ·
                                    {{ formatDate(ticket.created_at) }}
                                    <span v-if="canManage">
                                        · Aberto por
                                        {{ ticket.user.name }}</span
                                    >
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Responsável:
                                    {{
                                        ticket.responsible?.name ??
                                        'Não definido'
                                    }}
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2">
                                <Link
                                    :href="`/chamados/${ticket.id}`"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-indigo-100 hover:text-indigo-700"
                                >
                                    <Eye class="h-4 w-4" />
                                    Ver chamado
                                </Link>
                                <button
                                    v-if="isAdmin"
                                    type="button"
                                    @click="deleteTicket(ticket)"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Excluir
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-2xl bg-white px-6 py-16 text-center shadow-lg"
                >
                    <LifeBuoy class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h2 class="text-lg font-semibold text-gray-900">
                        Nenhum chamado encontrado
                    </h2>
                    <p class="mt-1 text-gray-500">
                        Abra um chamado para entrar em contato com a Gestão de
                        Contratos.
                    </p>
                    <Link
                        href="/chamados/novo"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        <Plus class="h-4 w-4" />
                        Abrir chamado
                    </Link>
                </div>

                <nav
                    v-if="tickets.links.length > 3"
                    class="mt-8 flex justify-center gap-2"
                    aria-label="Paginação"
                >
                    <template v-for="link in tickets.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-lg px-4 py-2 text-sm font-medium"
                            :class="
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-100'
                            "
                        >
                            {{
                                link.label
                                    .replace('Previous', 'Anterior')
                                    .replace('Next', 'Próxima')
                                    .replace('&laquo;', '«')
                                    .replace('&raquo;', '»')
                            }}
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
