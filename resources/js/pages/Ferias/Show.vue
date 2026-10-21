<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    CalendarDays,
    ArrowLeft,
    User,
    Clock,
    Calendar,
    CheckCircle2,
    XCircle,
    Clock3,
    Ban,
    MessageSquare,
    AlertTriangle,
} from 'lucide-vue-next';

const props = defineProps({
    vacationRequest: Object,
});

const showCancelModal = ref(false);
const cancelling = ref(false);

const statusConfig = {
    pendente: {
        bg: 'bg-yellow-100',
        text: 'text-yellow-700',
        border: 'border-yellow-200',
        icon: Clock3,
        label: 'Aguardando Aprovação',
    },
    aprovado: {
        bg: 'bg-green-100',
        text: 'text-green-700',
        border: 'border-green-200',
        icon: CheckCircle2,
        label: 'Aprovado',
    },
    rejeitado: {
        bg: 'bg-red-100',
        text: 'text-red-700',
        border: 'border-red-200',
        icon: XCircle,
        label: 'Rejeitado',
    },
    cancelado: {
        bg: 'bg-gray-100',
        text: 'text-gray-600',
        border: 'border-gray-200',
        icon: Ban,
        label: 'Cancelado',
    },
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
};

const cancelRequest = () => {
    cancelling.value = true;
    router.patch(`/ferias/${props.vacationRequest.id}/cancelar`, {}, {
        onFinish: () => {
            cancelling.value = false;
            showCancelModal.value = false;
        },
    });
};

const currentStatus = statusConfig[props.vacationRequest.status];
</script>

<template>
    <Head title="Detalhes da Solicitação" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <Link
                        href="/ferias"
                        class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Voltar para Minhas Férias
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                            <CalendarDays class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Detalhes da Solicitação</h1>
                            <p class="text-sm text-gray-600">Solicitação #{{ vacationRequest.id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div :class="[currentStatus.bg, currentStatus.border, 'mb-6 rounded-2xl border p-4']">
                    <div class="flex items-center gap-3">
                        <component :is="currentStatus.icon" :class="[currentStatus.text, 'h-6 w-6']" />
                        <div>
                            <span :class="[currentStatus.text, 'text-lg font-semibold']">{{ currentStatus.label }}</span>
                            <p v-if="vacationRequest.status === 'pendente'" class="text-sm text-yellow-600">
                                Sua solicitação está aguardando aprovação do gestor.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Main Info -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="p-6 space-y-6">
                        <!-- Period -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                                    <Calendar class="h-5 w-5 text-emerald-600" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Data de Início</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatDate(vacationRequest.start_date) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                    <Calendar class="h-5 w-5 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Data de Término</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatDate(vacationRequest.end_date) }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200" />

                        <!-- Duration -->
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                <Clock class="h-5 w-5 text-purple-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Duração</p>
                                <p class="text-lg font-semibold text-gray-900">{{ vacationRequest.period_days }} dias</p>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div v-if="vacationRequest.observations" class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                <MessageSquare class="h-5 w-5 text-gray-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Observações</p>
                                <p class="text-gray-900">{{ vacationRequest.observations }}</p>
                            </div>
                        </div>

                        <!-- Rejection Reason -->
                        <div v-if="vacationRequest.rejection_reason" class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <XCircle class="h-5 w-5 text-red-500 mt-0.5" />
                                <div>
                                    <p class="font-medium text-red-700">Motivo da Rejeição</p>
                                    <p class="text-sm text-red-600">{{ vacationRequest.rejection_reason }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Approver Info -->
                        <div v-if="vacationRequest.approver" class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                <User class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ vacationRequest.status === 'aprovado' ? 'Aprovado por' : 'Processado por' }}
                                </p>
                                <p class="font-semibold text-gray-900">{{ vacationRequest.approver.name }}</p>
                                <p class="text-xs text-gray-500">em {{ formatDateTime(vacationRequest.approved_at) }}</p>
                            </div>
                        </div>

                        <hr class="border-gray-200" />

                        <!-- Metadata -->
                        <div class="text-sm text-gray-500">
                            Solicitado em {{ formatDateTime(vacationRequest.created_at) }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="vacationRequest.status === 'pendente'" class="flex items-center justify-end gap-4 border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <button
                            @click="showCancelModal = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-red-300 bg-white px-5 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                        >
                            <Ban class="h-4 w-4" />
                            Cancelar Solicitação
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <Teleport to="body">
            <div v-if="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="showCancelModal = false"></div>
                <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                            <AlertTriangle class="h-6 w-6 text-red-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Cancelar Solicitação</h3>
                            <p class="text-sm text-gray-500">Esta ação não pode ser desfeita.</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Tem certeza que deseja cancelar sua solicitação de férias?
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="showCancelModal = false"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Voltar
                        </button>
                        <button
                            @click="cancelRequest"
                            :disabled="cancelling"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            <Ban class="h-4 w-4" />
                            {{ cancelling ? 'Cancelando...' : 'Sim, Cancelar' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
