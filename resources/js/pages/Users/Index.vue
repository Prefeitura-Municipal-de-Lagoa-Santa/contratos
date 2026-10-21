<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, ShieldCheck, Users } from 'lucide-vue-next';
import { reactive } from 'vue';

interface ManagedUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    users: {
        data: ManagedUser[];
        links: PaginationLink[];
    };
    roles: Record<string, string>;
}>();

const forms = reactive<
    Record<number, { role: string; processing: boolean; error: string }>
>({});

const formFor = (user: ManagedUser) => {
    forms[user.id] ??= { role: user.role, processing: false, error: '' };

    return forms[user.id];
};

const updateRole = (user: ManagedUser) => {
    const form = formFor(user);
    form.processing = true;
    form.error = '';

    router.patch(
        `/usuarios/${user.id}/perfil`,
        { role: form.role },
        {
            preserveScroll: true,
            onError: (errors) => {
                form.error =
                    errors.role ?? 'Não foi possível atualizar o perfil.';
            },
            onSuccess: () => {
                form.error = '';
            },
            onFinish: () => {
                form.processing = false;
            },
        },
    );
};
</script>

<template>
    <Head title="Usuários e perfis" />

    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8"
        >
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <Link
                    href="/dashboard"
                    class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Voltar ao painel
                </Link>

                <header class="mb-7 flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg"
                    >
                        <Users class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-gray-900"
                        >
                            Usuários e perfis
                        </h1>
                        <p class="text-gray-600">
                            Defina quem administra o sistema, gerencia as
                            demandas ou usa os serviços.
                        </p>
                    </div>
                </header>

                <section
                    class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-100"
                >
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2
                            class="flex items-center gap-2 font-semibold text-gray-900"
                        >
                            <ShieldCheck class="h-5 w-5 text-indigo-600" />
                            Perfis de acesso
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Gestores atendem chamados e aprovam férias. Apenas
                            administradores podem alterar perfis ou excluir
                            chamados.
                        </p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="user in props.users.data"
                            :key="user.id"
                            class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">
                                    {{ user.name }}
                                </p>
                                <p class="truncate text-sm text-gray-500">
                                    {{ user.email }}
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 sm:w-72">
                                <div class="flex gap-2">
                                    <select
                                        v-model="formFor(user).role"
                                        :aria-label="`Perfil de ${user.name}`"
                                        class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option
                                            v-for="(label, role) in props.roles"
                                            :key="role"
                                            :value="role"
                                        >
                                            {{ label }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        :disabled="
                                            formFor(user).processing ||
                                            formFor(user).role === user.role
                                        "
                                        @click="updateRole(user)"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {{
                                            formFor(user).processing
                                                ? 'Salvando...'
                                                : 'Salvar'
                                        }}
                                    </button>
                                </div>
                                <p
                                    v-if="formFor(user).error"
                                    class="text-sm text-red-600"
                                >
                                    {{ formFor(user).error }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <nav
                        v-if="props.users.links.length > 3"
                        aria-label="Paginação de usuários"
                        class="flex flex-wrap justify-center gap-2 border-t border-gray-100 px-6 py-4"
                    >
                        <Link
                            v-for="(link, index) in props.users.links"
                            :key="index"
                            :href="link.url ?? '#'"
                            :preserve-scroll="true"
                            :class="[
                                'rounded-lg px-3 py-2 text-sm',
                                link.active
                                    ? 'bg-indigo-600 font-semibold text-white'
                                    : 'bg-gray-50 text-gray-700 hover:bg-gray-100',
                                !link.url && 'pointer-events-none opacity-50',
                            ]"
                        >
                            {{ link.label }}
                        </Link>
                    </nav>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
