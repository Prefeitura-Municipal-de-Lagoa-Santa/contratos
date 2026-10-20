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
import { computed } from 'vue';
import {
    LayoutDashboard, 
    FileText, 
    Settings,
    User,
    Lock,
    Palette,
    Shield,
    Users,
    CalendarDays,
    CalendarPlus,
    ClipboardCheck,
    Package,
    LifeBuoy,
    MessageSquarePlus,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin ?? false);

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

    if (isAdmin.value) {
        rhItems.push({
            title: 'Aprovar Férias',
            href: '/ferias/aprovacoes',
            icon: ClipboardCheck,
        });
    }

    return [
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
                    title: 'Listar Documentos',
                    href: '/contratos',
                    icon: FileText,
                },
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
        {
            title: 'Configurações',
            icon: Settings,
            items: [
                {
                    title: 'Perfil',
                    href: '/settings/profile',
                    icon: User,
                },
                {
                    title: 'Senha',
                    href: '/settings/password',
                    icon: Lock,
                },
                {
                    title: 'Aparência',
                    href: '/settings/appearance',
                    icon: Palette,
                },
                {
                    title: 'Autenticação 2FA',
                    href: '/settings/two-factor',
                    icon: Shield,
                },
            ],
        },
    ];
});
</script>

<template>
    <Sidebar collapsible="icon" class="sidebar-modern">
        <SidebarHeader class="sidebar-header">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="sidebar-brand group/brand">
                        <Link :href="dashboard()">
                            <div class="flex items-center gap-3 w-full overflow-hidden">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-600 to-indigo-700 shadow-md group-hover/brand:scale-110 transition-transform duration-300">
                                    <AppLogo class="h-6 w-6 text-white" />
                                </div>
                                <div class="flex flex-col flex-1 min-w-0 opacity-100 group-data-[collapsible=icon]:opacity-0 transition-opacity duration-300">
                                    <span class="text-[9px] uppercase font-black text-primary-400 tracking-[0.2em] leading-none mb-1">
                                        Gestão de Documentos
                                    </span>
                                    <span class="truncate font-black text-xl leading-none bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                                        DocManager
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

        <SidebarFooter v-if="page.props.auth?.user" class="sidebar-footer border-t border-gray-100 p-4">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
.sidebar-modern {
    @apply border-r border-gray-100 bg-white shadow-sm !important;
}

.sidebar-modern :deep([data-sidebar="sidebar"]) {
    @apply bg-white !important;
}

.sidebar-header {
    @apply px-4 pt-8 pb-2 border-none bg-white !important;
}

.sidebar-brand {
    @apply h-auto py-2 px-1 hover:bg-transparent !important;
}

.sidebar-content {
    @apply flex-1 overflow-y-auto px-2 bg-white !important;
}

.sidebar-content::-webkit-scrollbar {
    width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-content::-webkit-scrollbar-thumb {
    @apply bg-gray-200 rounded-full hover:bg-gray-300;
}

.sidebar-footer {
    @apply bg-white border-t border-gray-50 p-2 mt-auto !important;
}
</style>
