<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    Package, 
    ChevronLeft,
    Save,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    users: Array,
});

const form = useForm({
    numero_patrimonio: '',
    nome: '',
    descricao: '',
    user_id: '',
    data_atribuicao: '',
});

const submit = () => {
    form.post(route('patrimonios.store'));
};
</script>

<template>
    <Head title="Novo Patrimônio" />

    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                            <Link :href="route('patrimonios.index')" class="hover:text-indigo-600 transition">Patrimônios</Link>
                            <ChevronLeft class="h-4 w-4 rotate-180" />
                            <span>Novo</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Cadastrar Patrimônio</h1>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Número do Patrimônio -->
                            <div class="sm:col-span-1">
                                <label for="numero_patrimonio" class="block text-sm font-medium text-gray-700">Número do Patrimônio</label>
                                <input
                                    id="numero_patrimonio"
                                    v-model="form.numero_patrimonio"
                                    type="text"
                                    required
                                    placeholder="Ex: 012345"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.numero_patrimonio }"
                                />
                                <div v-if="form.errors.numero_patrimonio" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.numero_patrimonio }}
                                </div>
                            </div>

                            <!-- Nome -->
                            <div class="sm:col-span-1">
                                <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Bem</label>
                                <input
                                    id="nome"
                                    v-model="form.nome"
                                    type="text"
                                    required
                                    placeholder="Ex: Notebook Dell"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.nome }"
                                />
                                <div v-if="form.errors.nome" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.nome }}
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div class="sm:col-span-2">
                                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição/Detalhes (Opcional)</label>
                                <textarea
                                    id="descricao"
                                    v-model="form.descricao"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Detalhes adicionais sobre o bem..."
                                ></textarea>
                                <div v-if="form.errors.descricao" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.descricao }}
                                </div>
                            </div>

                            <div class="sm:col-span-2 border-t border-gray-100 pt-4">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <AlertCircle class="h-4 w-4 text-amber-500" />
                                    Atribuição (Opcional)
                                </h3>
                            </div>

                            <!-- Servidor -->
                            <div class="sm:col-span-1">
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Servidor Responsável</label>
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Selecione um servidor (opcional)</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.user_id" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.user_id }}
                                </div>
                            </div>

                            <!-- Data de Atribuição -->
                            <div class="sm:col-span-1">
                                <label for="data_atribuicao" class="block text-sm font-medium text-gray-700">Data de Entrega</label>
                                <input
                                    id="data_atribuicao"
                                    v-model="form.data_atribuicao"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                                <div v-if="form.errors.data_atribuicao" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.data_atribuicao }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                            <Link
                                :href="route('patrimonios.index')"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                <Save class="h-4 w-4" />
                                Salvar Patrimônio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
