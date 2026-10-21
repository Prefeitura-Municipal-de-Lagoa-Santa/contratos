<script setup lang="ts">
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
const expandedItems = ref<Set<string>>(new Set(['Contratos']));

const toggleItem = (title: string) => {
    if (expandedItems.value.has(title)) {
        expandedItems.value.delete(title);
    } else {
        expandedItems.value.add(title);
    }
};

const isExpanded = (title: string) => expandedItems.value.has(title);
</script>

<template>
    <SidebarGroup class="px-0 py-0">
        <SidebarMenu class="space-y-1">
            <template v-for="item in items" :key="item.title">
                <!-- Item com submenu -->
                <SidebarMenuItem v-if="item.items && item.items.length > 0">
                    <SidebarMenuButton
                        @click="toggleItem(item.title)"
                        :tooltip="item.title"
                        class="nav-item group/item"
                        :data-state="isExpanded(item.title) ? 'open' : 'closed'"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-transparent group-hover/item:bg-primary-50 group-hover/item:text-primary-600 transition-colors">
                            <component :is="item.icon" class="nav-icon" />
                        </div>
                        <span class="nav-text">{{ item.title }}</span>
                        <ChevronRight 
                            class="nav-chevron"
                            :class="isExpanded(item.title) ? 'rotate-90' : ''"
                        />
                    </SidebarMenuButton>
                    <div class="overflow-hidden">
                        <Transition
                            enter-active-class="transition-all duration-300 ease-in-out"
                            enter-from-class="max-h-0 opacity-0"
                            enter-to-class="max-h-[500px] opacity-100"
                            leave-active-class="transition-all duration-250 ease-in-out"
                            leave-from-class="max-h-[500px] opacity-100"
                            leave-to-class="max-h-0 opacity-0"
                        >
                            <SidebarMenuSub v-show="isExpanded(item.title)" class="nav-submenu">
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="urlIsActive(subItem.href!, page.url)"
                                        class="nav-subitem"
                                    >
                                        <Link :href="subItem.href!">
                                            <component :is="subItem.icon" class="nav-subicon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </Transition>
                    </div>
                </SidebarMenuItem>

                <!-- Item simples sem submenu -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="urlIsActive(item.href!, page.url)"
                        :tooltip="item.title"
                        class="nav-item group/item"
                    >
                        <Link :href="item.href!">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-transparent group-active:bg-primary-50 group-hover/item:bg-primary-50 group-hover/item:text-primary-600 transition-colors">
                                <component :is="item.icon" class="nav-icon" />
                            </div>
                            <span class="nav-text">{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>

<style scoped>
.nav-item {
    @apply flex items-center gap-3 px-3 py-2 rounded-xl text-gray-500 font-medium transition-all duration-200 hover:bg-gray-50 hover:text-gray-900 !important;
}

.nav-item[data-active="true"] {
    @apply bg-primary-50 text-primary-700 font-bold shadow-sm !important;
}

.nav-item[data-active="true"] .nav-icon {
    @apply text-primary-600;
}

.nav-icon {
    @apply w-5 h-5 flex-shrink-0 transition-transform duration-200;
}

.nav-item:hover .nav-icon {
    @apply scale-110;
}

.nav-text {
    @apply flex-1 text-sm;
}

.nav-chevron {
    @apply w-4 h-4 opacity-40 transition-transform duration-300;
}

.nav-submenu {
    @apply ml-7 mt-1 border-l border-gray-100 pl-4 space-y-1 mb-2 !important;
}

.nav-subitem {
    @apply flex items-center gap-3 px-3 py-2 rounded-lg text-gray-500 text-[13px] transition-all hover:bg-gray-50 hover:text-gray-900 !important;
}

.nav-subitem[data-active="true"] {
    @apply text-primary-600 font-bold bg-primary-50/50 !important;
}

.nav-subicon {
    @apply w-4 h-4 shrink-0 opacity-60 transition-opacity;
}

.nav-subitem:hover .nav-subicon {
    @apply opacity-100 text-primary-600 scale-110;
}
</style>
