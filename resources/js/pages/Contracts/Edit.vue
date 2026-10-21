<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/components/InputLabel.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import { Input } from '@/components/ui/input';
import { ArrowLeft } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    contract: Object,
    suppliers: Array,
    contractTypes: Array,
    statusOptions: Array,
});

const form = useForm({
    title: props.contract.title,
    description: props.contract.description || '',
    supplier_id: props.contract.supplier_id,
    contract_type_id: props.contract.contract_type_id,
    status: props.contract.status,
    start_date: props.contract.start_date.split('T')[0],
    end_date: props.contract.end_date.split('T')[0],
    total: props.contract.total,
    manager: props.contract.manager || '',
    manager_email: props.contract.manager_email || '',
    deputy_manager: props.contract.deputy_manager || '',
    deputy_manager_email: props.contract.deputy_manager_email || '',
    inspector: props.contract.inspector || '',
    inspector_email: props.contract.inspector_email || '',
    deputy_inspector: props.contract.deputy_inspector || '',
    deputy_inspector_email: props.contract.deputy_inspector_email || '',
});

const submit = () => {
    form.patch(route('contracts.update', props.contract.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar ${contract.code}`" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Editar Contrato</h1>
                    <p class="mt-2 text-gray-600">{{ contract.code }}</p>
                    <Link
                        :href="route('contracts.index')"
                        class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Voltar para lista de contratos
                    </Link>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Informações Básicas</h2>
                                <p class="text-sm text-gray-500">Dados principais do contrato</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <!-- Contract Code (read-only) -->
                            <div class="group/field">
                                <InputLabel for="code" value="Código do Contrato" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                    <Input
                                        id="code"
                                        :value="contract.code"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-100 py-3 pl-10 font-mono text-sm cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-gray-500">
                                    <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    O código do contrato não pode ser alterado
                                </p>
                            </div>

                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Título *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                        required
                                        placeholder="Digite o título do contrato"
                                    />
                                </div>
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Objeto" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-4 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                        placeholder="Digite uma descrição detalhada do contrato..."
                                    ></textarea>
                                </div>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Supplier and Contract Type -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Supplier -->
                                <div>
                                    <InputLabel for="supplier_id" value="Fornecedor *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <select
                                            id="supplier_id"
                                            v-model="form.supplier_id"
                                            class="block w-full cursor-pointer appearance-none rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 pr-10 text-sm transition-all duration-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                            required
                                        >
                                            <option value="">Selecione um fornecedor</option>
                                            <option
                                                v-for="supplier in suppliers"
                                                :key="supplier.id"
                                                :value="supplier.id"
                                            >
                                                {{ supplier.trade_name }}
                                            </option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.supplier_id" class="mt-2" />
                                </div>

                                <!-- Contract Type -->
                                <div>
                                    <InputLabel for="contract_type_id" value="Tipo de Contrato *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <select
                                            id="contract_type_id"
                                            v-model="form.contract_type_id"
                                            class="block w-full cursor-pointer appearance-none rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 pr-10 text-sm transition-all duration-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                            required
                                        >
                                            <option value="">Selecione um tipo</option>
                                            <option
                                                v-for="type in contractTypes"
                                                :key="type.id"
                                                :value="type.id"
                                            >
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.contract_type_id" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Management Team Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Equipe de Gestão</h2>
                                <p class="text-sm text-gray-500">Responsáveis pelo acompanhamento do contrato</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Manager Section -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Manager Name -->
                                <div>
                                    <InputLabel for="manager" value="Gestor" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="manager"
                                            v-model="form.manager"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="Digite o nome do gestor"
                                        />
                                    </div>
                                    <InputError :message="form.errors.manager" class="mt-2" />
                                </div>

                                <!-- Manager Email -->
                                <div>
                                    <InputLabel for="manager_email" value="E-mail do Gestor" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="manager_email"
                                            v-model="form.manager_email"
                                            type="email"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="email@exemplo.com"
                                        />
                                    </div>
                                    <InputError :message="form.errors.manager_email" class="mt-2" />
                                </div>
                            </div>

                            <!-- Deputy Manager Section -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Deputy Manager Name -->
                                <div>
                                    <InputLabel for="deputy_manager" value="Gestor Suplente" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="deputy_manager"
                                            v-model="form.deputy_manager"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="Digite o nome do gestor suplente"
                                        />
                                    </div>
                                    <InputError :message="form.errors.deputy_manager" class="mt-2" />
                                </div>

                                <!-- Deputy Manager Email -->
                                <div>
                                    <InputLabel for="deputy_manager_email" value="E-mail do Gestor Suplente" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="deputy_manager_email"
                                            v-model="form.deputy_manager_email"
                                            type="email"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="email@exemplo.com"
                                        />
                                    </div>
                                    <InputError :message="form.errors.deputy_manager_email" class="mt-2" />
                                </div>
                            </div>

                            <!-- Inspector Section -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Inspector Name -->
                                <div>
                                    <InputLabel for="inspector" value="Fiscal" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="inspector"
                                            v-model="form.inspector"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="Digite o nome do fiscal"
                                        />
                                    </div>
                                    <InputError :message="form.errors.inspector" class="mt-2" />
                                </div>

                                <!-- Inspector Email -->
                                <div>
                                    <InputLabel for="inspector_email" value="E-mail do Fiscal" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="inspector_email"
                                            v-model="form.inspector_email"
                                            type="email"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="email@exemplo.com"
                                        />
                                    </div>
                                    <InputError :message="form.errors.inspector_email" class="mt-2" />
                                </div>
                            </div>

                            <!-- Deputy Inspector Section -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Deputy Inspector Name -->
                                <div>
                                    <InputLabel for="deputy_inspector" value="Fiscal Suplente" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="deputy_inspector"
                                            v-model="form.deputy_inspector"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="Digite o nome do fiscal suplente"
                                        />
                                    </div>
                                    <InputError :message="form.errors.deputy_inspector" class="mt-2" />
                                </div>

                                <!-- Deputy Inspector Email -->
                                <div>
                                    <InputLabel for="deputy_inspector_email" value="E-mail do Fiscal Suplente" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="deputy_inspector_email"
                                            v-model="form.deputy_inspector_email"
                                            type="email"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="email@exemplo.com"
                                        />
                                    </div>
                                    <InputError :message="form.errors.deputy_inspector_email" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Details Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100">
                                <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Detalhes do Contrato</h2>
                                <p class="text-sm text-gray-500">Vigência, status e valores</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="Status *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="block w-full cursor-pointer appearance-none rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 pr-10 text-sm transition-all duration-200 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                        required
                                    >
                                        <option
                                            v-for="status in statusOptions"
                                            :key="status"
                                            :value="status"
                                        >
                                            {{ status }}
                                        </option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <!-- Dates -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Start Date -->
                                <div>
                                    <InputLabel for="start_date" value="Data de Início *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="start_date"
                                            v-model="form.start_date"
                                            type="date"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                            required
                                        />
                                    </div>
                                    <InputError :message="form.errors.start_date" class="mt-2" />
                                </div>

                                <!-- End Date -->
                                <div>
                                    <InputLabel for="end_date" value="Data de Término *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <Input
                                            id="end_date"
                                            v-model="form.end_date"
                                            type="date"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                            required
                                        />
                                    </div>
                                    <InputError :message="form.errors.end_date" class="mt-2" />
                                </div>
                            </div>

                            <!-- Total Value -->
                            <div>
                                <InputLabel for="total" value="Valor Total *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-sm font-medium text-gray-500">R$</span>
                                    </div>
                                    <Input
                                        id="total"
                                        v-model="form.total"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-12 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                        required
                                        placeholder="0,00"
                                    />
                                </div>
                                <InputError :message="form.errors.total" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100">
                        <SecondaryButton
                            type="button"
                            @click="$inertia.visit(route('contracts.index'))"
                            class="rounded-xl px-6 py-3"
                        >
                            Cancelar
                        </SecondaryButton>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl hover:shadow-indigo-500/40 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="form.processing" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
