<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, FilePenLine, LifeBuoy, Package } from 'lucide-vue-next';

const page = usePage();

const shortcuts = [
    {
        title: 'Minhas férias',
        description: 'Consulte suas solicitações de férias.',
        href: '/ferias',
        icon: CalendarDays,
        style: 'bg-orange-50 text-orange-700 hover:bg-orange-100',
        availableToUser: false,
    },
    {
        title: 'Patrimônio',
        description: 'Acesse a carga patrimonial.',
        href: '/patrimonios',
        icon: Package,
        style: 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
        availableToUser: false,
    },
    {
        title: 'Requisições',
        description: 'Acompanhe suas requisições para a Gestão de Contratos.',
        href: '/chamados',
        icon: LifeBuoy,
        style: 'bg-blue-50 text-blue-700 hover:bg-blue-100',
        availableToUser: true,
    },
    {
        title: 'Abrir Requisição',
        description: 'Envie uma solicitação para a equipe de gestão.',
        href: '/chamados/novo',
        icon: FilePenLine,
        style: 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
        availableToUser: true,
    },
];

const visibleShortcuts =
    page.props.auth.isRegularUser
        ? shortcuts.filter((shortcut) => shortcut.availableToUser)
        : shortcuts;
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <main
            class="min-h-screen w-full min-w-0 space-y-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-4 sm:p-6 lg:p-8"
        >
            <header>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Bem-vindo
                </h1>
                <p class="mt-2 text-gray-600">
                    Acesse rapidamente os serviços disponíveis.
                </p>
            </header>

            <section aria-labelledby="quick-access-heading">
                <h2
                    id="quick-access-heading"
                    class="mb-5 text-lg font-semibold text-gray-900"
                >
                    Acesso rápido
                </h2>
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <Link
                        v-for="shortcut in visibleShortcuts"
                        :key="shortcut.title"
                        :href="shortcut.href"
                        class="group rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div
                            :class="shortcut.style"
                            class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl transition-colors"
                        >
                            <component :is="shortcut.icon" class="h-6 w-6" />
                        </div>
                        <h3
                            class="font-semibold text-gray-900 group-hover:text-indigo-700"
                        >
                            {{ shortcut.title }}
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            {{ shortcut.description }}
                        </p>
                    </Link>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
