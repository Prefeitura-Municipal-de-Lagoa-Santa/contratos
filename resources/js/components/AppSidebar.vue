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
                    title: 'Requisições',
                    href: '/chamados',
                    icon: LifeBuoy,
                },
                {
                    title: 'Abrir Requisição',
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
                                <img
                                    src="/images/logos-completas.png"
                                    alt="Prefeitura Municipal de Lagoa Santa"
                                    class="h-10 w-[88px] shrink-0 object-contain transition-transform duration-300 group-hover/brand:scale-105"
                                />
                                <div
                                    class="flex min-w-0 flex-1 flex-col opacity-100 transition-opacity duration-300 group-data-[collapsible=icon]:opacity-0"
                                >
                                    <span
                                        class="mb-1 text-[8px] font-black uppercase leading-tight tracking-[0.1em] text-blue-300"
                                    >
                                        PREFEITURA MUNICIPAL
                                    </span>
                                    <span
                                        class="text-sm font-black leading-tight tracking-tight text-white"
                                    >
                                        Requisições de Processos
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="sidebar-content !mt-0 bg-slate-950 pt-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter
            v-if="page.props.auth?.user"
            class="sidebar-footer"
        >
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
.sidebar-modern {
    @apply border-r-0 bg-slate-950 shadow-xl shadow-black/20 !important;
}

.sidebar-modern :deep([data-sidebar='sidebar']) {
    @apply bg-slate-950 text-slate-100 !important;
}

.sidebar-header {
    @apply border-none bg-slate-950 px-4 pb-2 pt-8 !important;
}

.sidebar-brand {
    @apply h-auto px-1 py-2 hover:bg-transparent !important;
}

.sidebar-content {
    @apply mt-0 flex-1 overflow-y-auto bg-slate-950 px-2 pt-3 !important;
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
    @apply mt-auto bg-slate-950 p-2 !important;
}
</style>
