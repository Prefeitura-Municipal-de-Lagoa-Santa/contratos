<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/components/InputLabel.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import {
    FileStack,
    User,
    Building2,
    FileText,
    Calendar,
    Send,
    ArrowLeft,
    Hash,
    Landmark,
} from 'lucide-vue-next';

const props = defineProps({
    nextCode: String,
});

const form = useForm({
    code: props.nextCode || '',
    date: new Date().toISOString().split('T')[0],
    sender_name: '',
    sender_role: '',
    department: '',
    recipient_name: '',
    recipient_role: '',
    recipient_institution: '',
    subject: '',
    content: '',
});

const submit = () => {
    form.post(route('oficios.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Novo Ofício" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                        <FileStack class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Novo Ofício</h1>
                    <p class="mt-2 text-gray-600">Preencha os campos abaixo para criar um novo ofício</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Identification Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100">
                                <Hash class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Identificação</h2>
                                <p class="text-sm text-gray-500">Número e data do ofício</p>
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <!-- Ofício Code -->
                            <div>
                                <InputLabel for="code" value="Número do Ofício *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <FileStack class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 font-mono text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                        required
                                        :placeholder="nextCode"
                                    />
                                </div>
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-gray-500">
                                    <span class="text-indigo-500">ℹ</span>
                                    Sugestão: <span class="font-mono font-medium text-indigo-600">{{ nextCode }}</span>
                                </p>
                                <InputError :message="form.errors.code" class="mt-2" />
                            </div>

                            <!-- Date -->
                            <div>
                                <InputLabel for="date" value="Data *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Calendar class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="date"
                                        v-model="form.date"
                                        type="date"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                        required
                                    />
                                </div>
                                <InputError :message="form.errors.date" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Sender Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                                <Send class="h-5 w-5 text-emerald-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Remetente</h2>
                                <p class="text-sm text-gray-500">Quem está enviando o ofício</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Sender Name -->
                                <div>
                                    <InputLabel for="sender_name" value="Nome *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <User class="h-5 w-5 text-gray-400" />
                                        </div>
                                        <Input
                                            id="sender_name"
                                            v-model="form.sender_name"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            required
                                            placeholder="Nome do remetente"
                                        />
                                    </div>
                                    <InputError :message="form.errors.sender_name" class="mt-2" />
                                </div>

                                <!-- Sender Role -->
                                <div>
                                    <InputLabel for="sender_role" value="Cargo" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <User class="h-5 w-5 text-gray-400" />
                                        </div>
                                        <Input
                                            id="sender_role"
                                            v-model="form.sender_role"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                            placeholder="Ex: Secretário, Prefeito, etc."
                                        />
                                    </div>
                                    <InputError :message="form.errors.sender_role" class="mt-2" />
                                </div>
                            </div>

                            <!-- Department -->
                            <div>
                                <InputLabel for="department" value="Setor de Origem" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Building2 class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="department"
                                        v-model="form.department"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                                        placeholder="Ex: Secretaria de Administração"
                                    />
                                </div>
                                <InputError :message="form.errors.department" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Recipient Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-500 via-violet-500 to-fuchsia-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100">
                                <User class="h-5 w-5 text-purple-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Destinatário</h2>
                                <p class="text-sm text-gray-500">Para quem o ofício é destinado</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <!-- Recipient Name -->
                                <div>
                                    <InputLabel for="recipient_name" value="Nome *" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <User class="h-5 w-5 text-gray-400" />
                                        </div>
                                        <Input
                                            id="recipient_name"
                                            v-model="form.recipient_name"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-500/20"
                                            required
                                            placeholder="Nome do destinatário"
                                        />
                                    </div>
                                    <InputError :message="form.errors.recipient_name" class="mt-2" />
                                </div>

                                <!-- Recipient Role -->
                                <div>
                                    <InputLabel for="recipient_role" value="Cargo" class="text-sm font-medium text-gray-700" />
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <User class="h-5 w-5 text-gray-400" />
                                        </div>
                                        <Input
                                            id="recipient_role"
                                            v-model="form.recipient_role"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-500/20"
                                            placeholder="Ex: Diretor, Gerente, etc."
                                        />
                                    </div>
                                    <InputError :message="form.errors.recipient_role" class="mt-2" />
                                </div>
                            </div>

                            <!-- Recipient Institution -->
                            <div>
                                <InputLabel for="recipient_institution" value="Instituição / Empresa" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Landmark class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="recipient_institution"
                                        v-model="form.recipient_institution"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-500/20"
                                        placeholder="Ex: Tribunal de Contas, Empresa XYZ, etc."
                                    />
                                </div>
                                <InputError :message="form.errors.recipient_institution" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Content Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-xl shadow-gray-200/50 ring-1 ring-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-200/60">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500"></div>
                        
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100">
                                <FileText class="h-5 w-5 text-amber-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Conteúdo</h2>
                                <p class="text-sm text-gray-500">Assunto e corpo do ofício</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <!-- Subject -->
                            <div>
                                <InputLabel for="subject" value="Assunto *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <FileText class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="subject"
                                        v-model="form.subject"
                                        type="text"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-10 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                        required
                                        placeholder="Assunto do ofício"
                                    />
                                </div>
                                <InputError :message="form.errors.subject" class="mt-2" />
                            </div>

                            <!-- Content -->
                            <div>
                                <InputLabel for="content" value="Conteúdo *" class="text-sm font-medium text-gray-700" />
                                <div class="relative mt-2">
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        rows="10"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-4 text-sm transition-all duration-200 placeholder:text-gray-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/20"
                                        required
                                        placeholder="Digite o conteúdo do ofício..."
                                    ></textarea>
                                </div>
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <Link
                            href="/oficios"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50"
                        >
                            <ArrowLeft class="h-5 w-5" />
                            Voltar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:from-indigo-700 hover:to-purple-700 hover:shadow-xl hover:shadow-indigo-500/40 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Send class="h-5 w-5" />
                            {{ form.processing ? 'Salvando...' : 'Criar Ofício' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
