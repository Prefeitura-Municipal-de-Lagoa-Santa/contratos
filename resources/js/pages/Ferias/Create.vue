<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
    CalendarDays,
    CalendarPlus,
    ArrowLeft,
    AlertCircle,
    Info,
    Clock,
    Calendar,
    UserCheck,
} from 'lucide-vue-next';

const props = defineProps({
    balance: Object,
    allowedPeriods: Array,
    minimumAdvanceDays: Number,
    minDate: String,
    users: Array,
});

const form = useForm({
    start_date: '',
    period_days: 30,
    supervisor_id: '',
    observations: '',
});

const endDate = computed(() => {
    if (!form.start_date) return '';
    const start = new Date(form.start_date);
    start.setDate(start.getDate() + form.period_days - 1);
    return start.toLocaleDateString('pt-BR');
});

const hasEnoughBalance = computed(() => {
    return props.balance.remaining_days >= form.period_days;
});

const submit = () => {
    form.post('/ferias', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Solicitar Férias" />

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
                            <CalendarPlus class="h-7 w-7 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Solicitar Férias</h1>
                            <p class="text-sm text-gray-600">Preencha os dados para sua solicitação</p>
                        </div>
                    </div>
                </div>

                <!-- Balance Info -->
                <div class="mb-6 overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100">
                            <CalendarDays class="h-6 w-6 text-emerald-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Saldo Disponível</h3>
                            <p class="text-sm text-gray-500">
                                Você possui <span class="font-bold text-emerald-600">{{ balance.remaining_days }} dias</span> disponíveis para solicitar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-start gap-3">
                        <Info class="h-5 w-5 text-blue-500 mt-0.5" />
                        <div class="text-sm text-blue-700">
                            <p class="font-medium">Informações importantes:</p>
                            <ul class="mt-1 list-disc list-inside space-y-1">
                                <li>A solicitação deve ser feita com no mínimo <strong>{{ minimumAdvanceDays }} dias</strong> de antecedência.</li>
                                <li>São permitidos períodos de <strong>15</strong> ou <strong>30 dias</strong>.</li>
                                <li>Máximo de <strong>2 funcionários</strong> de férias simultaneamente.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                    <div class="p-6 space-y-6">
                        <!-- Period Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Período de Férias</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button
                                    type="button"
                                    @click="form.period_days = 15"
                                    :class="[
                                        'relative flex flex-col items-center rounded-xl border-2 p-4 transition-all duration-200',
                                        form.period_days === 15
                                            ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500/20'
                                            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    <Clock class="h-8 w-8 mb-2" :class="form.period_days === 15 ? 'text-emerald-600' : 'text-gray-400'" />
                                    <span class="text-lg font-bold" :class="form.period_days === 15 ? 'text-emerald-900' : 'text-gray-700'">15 dias</span>
                                    <span class="text-xs text-gray-500">Meio período</span>
                                </button>
                                <button
                                    type="button"
                                    @click="form.period_days = 30"
                                    :class="[
                                        'relative flex flex-col items-center rounded-xl border-2 p-4 transition-all duration-200',
                                        form.period_days === 30
                                            ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500/20'
                                            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    <Calendar class="h-8 w-8 mb-2" :class="form.period_days === 30 ? 'text-emerald-600' : 'text-gray-400'" />
                                    <span class="text-lg font-bold" :class="form.period_days === 30 ? 'text-emerald-900' : 'text-gray-700'">30 dias</span>
                                    <span class="text-xs text-gray-500">Período completo</span>
                                </button>
                            </div>
                            <p v-if="form.errors.period_days" class="mt-2 text-sm text-red-600">{{ form.errors.period_days }}</p>
                            <p v-if="form.errors.period_days" class="mt-2 text-sm text-red-600">{{ form.errors.period_days }}</p>
                        </div>

                        <!-- Supervisor Selection -->
                        <div>
                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">Chefia Imediata</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <UserCheck class="h-5 w-5 text-gray-400" />
                                </div>
                                <select
                                    id="supervisor_id"
                                    v-model="form.supervisor_id"
                                    class="block w-full rounded-xl border-gray-200 py-3 pl-12 pr-4 shadow-sm transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                >
                                    <option value="" disabled>Selecione sua chefia imediata...</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Selecione o responsável por aprovar suas férias.</p>
                            <p v-if="form.errors.supervisor_id" class="mt-2 text-sm text-red-600">{{ form.errors.supervisor_id }}</p>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Início</label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                :min="minDate"
                                class="block w-full rounded-xl border-gray-200 py-3 px-4 shadow-sm transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                            />
                            <p v-if="form.errors.start_date" class="mt-2 text-sm text-red-600">{{ form.errors.start_date }}</p>
                        </div>

                        <!-- End Date Preview -->
                        <div v-if="form.start_date" class="rounded-xl bg-gray-50 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Data de Término:</span>
                                <span class="text-lg font-semibold text-gray-900">{{ endDate }}</span>
                            </div>
                        </div>

                        <!-- Balance Warning -->
                        <div v-if="!hasEnoughBalance" class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-center gap-3">
                                <AlertCircle class="h-5 w-5 text-red-500" />
                                <p class="text-sm text-red-700">
                                    Saldo insuficiente. Você possui apenas <strong>{{ balance.remaining_days }} dias</strong> disponíveis.
                                </p>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div>
                            <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                                Observações (opcional)
                            </label>
                            <textarea
                                id="observations"
                                v-model="form.observations"
                                rows="3"
                                placeholder="Adicione informações relevantes para sua solicitação..."
                                class="block w-full rounded-xl border-gray-200 py-3 px-4 shadow-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <Link
                            href="/ferias"
                            class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !hasEnoughBalance || !form.start_date || !form.supervisor_id"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all duration-200 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <CalendarPlus class="h-4 w-4" />
                            Enviar Solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
