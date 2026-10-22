<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import { usePage } from '@inertiajs/vue3';
import { Moon } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';

const page = usePage();
const user = page.props.auth.user;
const { isMobile, state } = useSidebar();
const { updateAppearance } = useAppearance();
const toggleTheme = () => {
    updateAppearance(
        document.documentElement.classList.contains('dark') ? 'light' : 'dark',
    );
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem class="flex items-center gap-1">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="default"
                        class="user-menu-button !h-auto min-w-0 flex-1 !gap-2 !px-2 !py-2"
                        data-test="sidebar-menu-button"
                    >
                        <UserInfo :user="user" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg border-white/10 bg-slate-900 text-slate-100"
                    :side="
                        isMobile
                            ? 'bottom'
                            : state === 'collapsed'
                              ? 'left'
                              : 'bottom'
                    "
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
            <SidebarMenuButton
                class="w-9 shrink-0 justify-center !text-white"
                tooltip="Alternar tema claro/escuro"
                aria-label="Alternar tema claro/escuro"
                @click="toggleTheme"
            >
                <Moon class="size-4 !text-white" />
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>

<style scoped>
.user-menu-button {
    @apply flex items-center gap-3 rounded-xl px-3 py-2 font-medium text-slate-200 transition-all duration-200 hover:bg-white/10 !important;
}

.user-menu-button[data-state='open'] {
    @apply bg-white/10 shadow-sm !important;
}
</style>
