<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate } from '@/lib/dateFormat';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarDays,
    Eye,
    LifeBuoy,
    Plus,
    Trash2,
    UserRound,
    UserRoundCog,
} from 'lucide-vue-next';

defineProps({
    tickets: Object,
    canManage: Boolean,
    isAdmin: Boolean,
});

const statusStyles = {
    aberto: 'bg-amber-50 text-amber-800 ring-amber-200',
    em_atendimento: 'bg-blue-50 text-blue-800 ring-blue-200',
    resolvido: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
    aprovado: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
    recusado: 'bg-red-50 text-red-800 ring-red-200',
};

const statusAccentStyles = {
    aberto: 'bg-amber-400',
    em_atendimento: 'bg-blue-500',
    resolvido: 'bg-emerald-500',
    aprovado: 'bg-emerald-500',
    recusado: 'bg-red-500',
};

const deleteTicket = (ticket) => {
    if (
        window.confirm(
            `Tem certeza que deseja excluir a Demanda #${ticket.id}?`,
        )
    ) {
        router.delete(`/chamados/${ticket.id}`);
    }
};
</script>

<template>
    <Head title="Demandas" />

    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-5 sm:py-6"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <header
                    class="mb-5 flex flex-col gap-4 rounded-2xl border border-white/80 bg-white/70 p-4 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:p-5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg shadow-indigo-200"
                        >
                            <LifeBuoy class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <p
                                class="mb-1 text-xs font-bold uppercase tracking-[0.16em] text-indigo-600"
                            >
                                Gestão de solicitações
                            </p>
                            <h1
                                class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl"
                            >
                                Demandas
                            </h1>
                            <p class="mt-0.5 text-sm text-slate-600">
                                {{
                                    canManage
                                        ? 'Demandas recebidas pela Gestão de Contratos'
                                        : 'Acompanhe suas solicitações à Gestão de Contratos'
                                }}
                            </p>
                        </div>
                    </div>
                    <Link
                        href="/chamados/novo"
                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    >
                        <Plus class="h-5 w-5" />
                        Abrir Demanda
                    </Link>
                </header>

                <div v-if="tickets.data.length" class="space-y-3">
                    <article
                        v-for="ticket in tickets.data"
                        :key="ticket.id"
                        class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-lg hover:shadow-slate-200/60"
                    >
                        <span
                            class="absolute inset-y-0 left-0 w-1"
                            :class="statusAccentStyles[ticket.status]"
                            aria-hidden="true"
                        />
                        <div
                            class="flex flex-col gap-3 p-4 pl-5 sm:flex-row sm:items-center sm:justify-between sm:p-4 sm:pl-5"
                        >
                            <div class="min-w-0 flex-1">
                                <div
                                    class="mb-1.5 flex flex-wrap items-center gap-2"
                                >
                                    <span
                                        class="text-xs font-bold uppercase tracking-wide text-slate-500"
                                        >Demanda #{{ ticket.id }}</span
                                    >
                                    <span
                                        :class="[
                                            statusStyles[ticket.status],
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset',
                                        ]"
                                    >
                                        {{ ticketStatusLabels[ticket.status] }}
                                    </span>
                                </div>
                                <h2
                                    class="truncate text-lg font-bold tracking-tight text-slate-900 sm:text-xl"
                                >
                                    {{ ticket.subject }}
                                </h2>
                                <div
                                    class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500"
                                >
                                    <span
                                        class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700"
                                    >
                                        {{ ticketCategoryLabels[ticket.category] }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <CalendarDays class="h-3.5 w-3.5" />
                                        {{ formatDate(ticket.created_at) }}
                                    </span>
                                    <span
                                        v-if="canManage"
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <UserRound class="h-3.5 w-3.5" />
                                        Aberto por {{ ticket.user.name }}
                                    </span>
                                </div>
                                <p
                                    class="mt-2 inline-flex items-center gap-1.5 text-sm text-slate-500"
                                >
                                    <UserRoundCog
                                        class="h-4 w-4 text-slate-400"
                                    />
                                    <span>Responsável:</span>
                                    <span class="font-medium text-slate-700">
                                        {{
                                            ticket.responsible?.name ??
                                            'Não definido'
                                        }}
                                    </span>
                                </p>
                            </div>
                            <div
                                class="flex shrink-0 flex-wrap gap-2 border-t border-slate-100 pt-3 sm:border-0 sm:pt-0"
                            >
                                <Link
                                    :href="`/chamados/${ticket.id}`"
                                    class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-indigo-50 hover:text-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                >
                                    <Eye class="h-4 w-4" />
                                    Ver Demanda
                                </Link>
                                <button
                                    v-if="isAdmin"
                                    type="button"
                                    @click="deleteTicket(ticket)"
                                    class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
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
                    class="rounded-2xl bg-white px-5 py-10 text-center shadow-lg"
                >
                    <LifeBuoy class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h2 class="text-lg font-semibold text-gray-900">
                        Nenhuma Demanda encontrada
                    </h2>
                    <p class="mt-1 text-gray-500">
                        Abra uma Demanda para entrar em contato com a Gestão de
                        Contratos.
                    </p>
                    <Link
                        href="/chamados/novo"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        <Plus class="h-4 w-4" />
                        Abrir Demanda
                    </Link>
                </div>

                <nav
                    v-if="tickets.links.length > 3"
                    class="mt-5 flex justify-center gap-2"
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
