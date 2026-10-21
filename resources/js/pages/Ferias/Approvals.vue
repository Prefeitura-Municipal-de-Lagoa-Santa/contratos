<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ClipboardCheck,
    Calendar,
    User,
    Clock,
    Check,
    X,
    ChevronLeft,
    ChevronRight,
    CalendarDays,
} from 'lucide-vue-next';

const props = defineProps({
    pendingRequests: Object,
});

const rejectForm = useForm({
    rejection_reason: '',
});

const selectedRequest = ref(null);
const showRejectModal = ref(false);

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

const approve = (id) => {
    router.patch(`/ferias/${id}/aprovar`, {}, {
        preserveScroll: true,
    });
};

const openRejectModal = (request) => {
    selectedRequest.value = request;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const reject = () => {
    rejectForm.patch(`/ferias/${selectedRequest.value.id}/rejeitar`, {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            selectedRequest.value = null;
        },
    });
};
</script>

<template>
    <Head title="Aprovação de Férias" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                        <ClipboardCheck class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Aprovação de Férias</h1>
                        <p class="text-sm text-gray-600">Gerencie solicitações pendentes de aprovação</p>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Funcionário
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Período
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Dias
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Solicitado em
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="request in pendingRequests.data"
                                    :key="request.id"
                                    class="transition-colors duration-150 hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                                <User class="h-5 w-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ request.user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ request.user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="h-4 w-4 text-gray-400" />
                                            <span class="text-sm text-gray-900">
                                                {{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Clock class="h-4 w-4 text-gray-400" />
                                            <span class="text-sm text-gray-600">{{ request.period_days }} dias</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-500">{{ formatDate(request.created_at) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="approve(request.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-green-100 px-3 py-2 text-sm font-medium text-green-700 transition-all duration-200 hover:bg-green-200"
                                            >
                                                <Check class="h-4 w-4" />
                                                Aprovar
                                            </button>
                                            <button
                                                @click="openRejectModal(request)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 px-3 py-2 text-sm font-medium text-red-700 transition-all duration-200 hover:bg-red-200"
                                            >
                                                <X class="h-4 w-4" />
                                                Rejeitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="pendingRequests.data.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <CalendarDays class="h-12 w-12 text-gray-300" />
                                            <p class="mt-4 text-sm text-gray-500">Nenhuma solicitação pendente de aprovação</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="pendingRequests.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="text-sm text-gray-600">
                            Mostrando <span class="font-medium">{{ pendingRequests.from }}</span> a <span class="font-medium">{{ pendingRequests.to }}</span> de <span class="font-medium">{{ pendingRequests.total }}</span> resultados
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                v-if="pendingRequests.prev_page_url"
                                :href="pendingRequests.prev_page_url"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                            >
                                <ChevronLeft class="h-4 w-4" />
                                Anterior
                            </Link>
                            <Link
                                v-if="pendingRequests.next_page_url"
                                :href="pendingRequests.next_page_url"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                            >
                                Próximo
                                <ChevronRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <Teleport to="body">
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="showRejectModal = false"></div>
                <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rejeitar Solicitação</h3>
                    <form @submit.prevent="reject">
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo da Rejeição *
                            </label>
                            <textarea
                                id="rejection_reason"
                                v-model="rejectForm.rejection_reason"
                                rows="3"
                                required
                                placeholder="Informe o motivo da rejeição..."
                                class="block w-full rounded-xl border-gray-200 py-3 px-4 shadow-sm transition-all duration-200 placeholder:text-gray-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                            ></textarea>
                            <p v-if="rejectForm.errors.rejection_reason" class="mt-2 text-sm text-red-600">
                                {{ rejectForm.errors.rejection_reason }}
                            </p>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showRejectModal = false"
                                class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="rejectForm.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                            >
                                <X class="h-4 w-4" />
                                {{ rejectForm.processing ? 'Rejeitando...' : 'Confirmar Rejeição' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
