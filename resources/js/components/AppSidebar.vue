<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    CalendarDays,
    CalendarPlus,
    ClipboardCheck,
    FileText,
    LayoutDashboard,
    LifeBuoy,
    MessageSquarePlus,
    Package,
    Shield,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'administrator');
const canManage = computed(() =>
    ['administrator', 'manager'].includes(page.props.auth?.user?.role ?? ''),
);

const mainNavItems = computed<NavItem[]>(() => {
    const rhItems: NavItem[] = [
        {
            title: 'Minhas Férias',
            href: '/ferias',
            icon: CalendarDays,
        },
        {
            title: 'Solicitar Férias',
            href: '/ferias/nova',
            icon: CalendarPlus,
        },
    ];

    if (canManage.value) {
        rhItems.push({
            title: 'Aprovar Férias',
            href: '/ferias/aprovacoes',
            icon: ClipboardCheck,
        });
    }

    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutDashboard,
        },
        {
            title: 'Documentos',
            icon: FileText,
            items: [
                {
                    title: 'Carga Patrimonial',
                    href: '/patrimonios',
                    icon: Package,
                },
                {
                    title: 'Chamados',
                    href: '/chamados',
                    icon: LifeBuoy,
                },
                {
                    title: 'Abrir Chamado',
                    href: '/chamados/novo',
                    icon: MessageSquarePlus,
                },
            ],
        },
        {
            title: 'RH',
            icon: Users,
            items: rhItems,
        },
    ];

    if (isAdmin.value) {
        items.splice(1, 0, {
            title: 'Usuários e perfis',
            href: '/usuarios',
            icon: Shield,
        });
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" class="sidebar-modern">
        <SidebarHeader class="sidebar-header">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="sidebar-brand group/brand"
                    >
                        <Link :href="dashboard()">
                            <div
                                class="flex w-full items-center gap-3 overflow-hidden"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-600 to-indigo-700 shadow-md transition-transform duration-300 group-hover/brand:scale-110"
                                >
                                    <AppLogo class="h-6 w-6 text-white" />
                                </div>
                                <div
                                    class="flex min-w-0 flex-1 flex-col opacity-100 transition-opacity duration-300 group-data-[collapsible=icon]:opacity-0"
                                >
                                    <span
                                        class="mb-1 text-[9px] font-black uppercase leading-none tracking-[0.2em] text-primary-400"
                                    >
                                        PROCESSOS LICITATÓRIOS
                                    </span>
                                    <span
                                        class="whitespace-nowrap bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-base font-black leading-none tracking-tight text-transparent"
                                    >
                                        Processos Licitatórios
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="sidebar-content mt-4">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter
            v-if="page.props.auth?.user"
            class="sidebar-footer border-t border-gray-100 p-4"
        >
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
.sidebar-modern {
    @apply border-r border-gray-100 bg-white shadow-sm !important;
}

.sidebar-modern :deep([data-sidebar='sidebar']) {
    @apply bg-white !important;
}

.dark .sidebar-modern {
    @apply border-gray-700 bg-slate-800 !important;
}

.dark .sidebar-modern :deep([data-sidebar='sidebar']) {
    @apply bg-slate-800 text-slate-100 !important;
}

.sidebar-header {
    @apply border-none bg-white px-4 pb-2 pt-8 !important;
}

.dark .sidebar-header {
    @apply bg-slate-800 !important;
}

.sidebar-brand {
    @apply h-auto px-1 py-2 hover:bg-transparent !important;
}

.sidebar-content {
    @apply flex-1 overflow-y-auto bg-white px-2 !important;
}

.dark .sidebar-content {
    @apply bg-slate-800 !important;
}

.sidebar-content::-webkit-scrollbar {
    width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-content::-webkit-scrollbar-thumb {
    @apply rounded-full bg-gray-200 hover:bg-gray-300;
}

.sidebar-footer {
    @apply mt-auto border-t border-gray-50 bg-white p-2 !important;
}

.dark .sidebar-footer {
    @apply border-gray-700 bg-slate-800 !important;
}
</style>
