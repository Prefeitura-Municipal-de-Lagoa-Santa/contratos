<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    CalendarDays,
    Plus,
    Eye,
    Search,
    Calendar,
    Clock,
    ChevronLeft,
    ChevronRight,
    Filter,
    CheckCircle2,
    XCircle,
    Clock3,
    Ban,
} from 'lucide-vue-next';

const props = defineProps({
    vacationRequests: Object,
    balance: Object,
    currentYear: String,
    years: Array,
});

const searchQuery = ref('');
const selectedYear = ref(props.currentYear);

const statusColors = {
    pendente: { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: Clock3 },
    aprovado: { bg: 'bg-green-100', text: 'text-green-700', icon: CheckCircle2 },
    rejeitado: { bg: 'bg-red-100', text: 'text-red-700', icon: XCircle },
    cancelado: { bg: 'bg-gray-100', text: 'text-gray-600', icon: Ban },
};

const statusLabels = {
    pendente: 'Pendente',
    aprovado: 'Aprovado',
    rejeitado: 'Rejeitado',
    cancelado: 'Cancelado',
};

const filteredRequests = computed(() => {
    if (!searchQuery.value) {
        return props.vacationRequests.data;
    }
    const query = searchQuery.value.toLowerCase();
    return props.vacationRequests.data.filter(req =>
        req.status.toLowerCase().includes(query) ||
        formatDate(req.start_date).includes(query)
    );
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
};

const changeYear = (year) => {
    selectedYear.value = year;
    router.get('/ferias', { year }, { preserveState: true });
};
</script>

<template>
    <Head title="Minhas Férias" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                            <CalendarDays class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Minhas Férias</h1>
                            <p class="text-sm text-gray-600">Gerencie suas solicitações de férias</p>
                        </div>
                    </div>
                    <Link
                        href="/ferias/nova"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all duration-200 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl hover:shadow-emerald-500/40"
                    >
                        <Plus class="h-5 w-5" />
                        Solicitar Férias
                    </Link>
                </div>

                <!-- Balance Card -->
                <div class="mb-6 overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Saldo de Férias {{ currentYear }}</h2>
                            <p class="text-sm text-gray-500">Dias disponíveis para solicitação</p>
                        </div>
                        <div class="flex gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-emerald-600">{{ balance.remaining_days }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Disponíveis</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ balance.used_days }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Utilizados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-400">{{ balance.total_days }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Total</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por status ou data..."
                            class="block w-full rounded-xl border-gray-200 bg-white py-3.5 pl-12 pr-4 text-sm shadow-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <Filter class="h-5 w-5 text-gray-400" />
                        <select
                            v-model="selectedYear"
                            @change="changeYear(selectedYear)"
                            class="rounded-xl border-gray-200 bg-white py-3.5 px-4 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Período
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Dias
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Status
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
                                    v-for="request in filteredRequests"
                                    :key="request.id"
                                    class="transition-colors duration-150 hover:bg-gray-50"
                                >
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="h-4 w-4 text-gray-400" />
                                            <span class="text-sm font-medium text-gray-900">
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
                                        <span
                                            :class="[
                                                statusColors[request.status].bg,
                                                statusColors[request.status].text,
                                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold'
                                            ]"
                                        >
                                            <component :is="statusColors[request.status].icon" class="h-3.5 w-3.5" />
                                            {{ statusLabels[request.status] }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-500">{{ formatDate(request.created_at) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <Link
                                            :href="`/ferias/${request.id}`"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-200"
                                        >
                                            <Eye class="h-4 w-4" />
                                            Ver
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="filteredRequests.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <CalendarDays class="h-12 w-12 text-gray-300" />
                                            <p class="mt-4 text-sm text-gray-500">Nenhuma solicitação de férias encontrada</p>
                                            <Link
                                                href="/ferias/nova"
                                                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-emerald-700"
                                            >
                                                <Plus class="h-4 w-4" />
                                                Solicitar férias
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="vacationRequests.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="text-sm text-gray-600">
                            Mostrando <span class="font-medium">{{ vacationRequests.from }}</span> a <span class="font-medium">{{ vacationRequests.to }}</span> de <span class="font-medium">{{ vacationRequests.total }}</span> resultados
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                v-if="vacationRequests.prev_page_url"
                                :href="vacationRequests.prev_page_url"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                            >
                                <ChevronLeft class="h-4 w-4" />
                                Anterior
                            </Link>
                            <Link
                                v-if="vacationRequests.next_page_url"
                                :href="vacationRequests.next_page_url"
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
    </AppLayout>
</template>
