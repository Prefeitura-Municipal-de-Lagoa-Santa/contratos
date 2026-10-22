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
                        class="nav-item group/item !text-slate-200"
                        :data-state="isExpanded(item.title) ? 'open' : 'closed'"
                    >
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-transparent text-slate-200 transition-colors group-hover/item:bg-white/10 group-hover/item:text-white"
                        >
                            <component
                                :is="item.icon"
                                class="nav-icon !text-slate-300"
                            />
                        </div>
                        <span class="nav-text !text-slate-200">{{
                            item.title
                        }}</span>
                        <ChevronRight
                            class="nav-chevron !text-white !opacity-100"
                            :style="{ color: '#fff', stroke: '#fff' }"
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
                            <SidebarMenuSub
                                v-show="isExpanded(item.title)"
                                class="nav-submenu"
                            >
                                <SidebarMenuSubItem
                                    v-for="subItem in item.items"
                                    :key="subItem.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="
                                            urlIsActive(subItem.href!, page.url)
                                        "
                                        class="nav-subitem !text-slate-300"
                                    >
                                        <Link :href="subItem.href!">
                                            <component
                                                :is="subItem.icon"
                                                class="nav-subicon !text-slate-300"
                                            />
                                            <span class="!text-slate-300">{{
                                                subItem.title
                                            }}</span>
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
                        class="nav-item group/item !text-slate-200"
                    >
                        <Link :href="item.href!">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-transparent text-slate-200 transition-colors group-hover/item:bg-white/10 group-hover/item:text-white group-active:bg-white/10"
                            >
                                <component
                                    :is="item.icon"
                                    class="nav-icon !text-slate-300"
                                />
                            </div>
                            <span class="nav-text !text-slate-200">{{
                                item.title
                            }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>

<style scoped>
.nav-item {
    @apply flex items-center gap-3 rounded-xl px-3 py-2 font-medium text-slate-200 opacity-100 transition-all duration-200 hover:bg-white/10 hover:text-white !important;
}

.nav-item[data-active='true'] {
    @apply bg-blue-500/15 font-bold text-blue-100 shadow-sm !important;
}

.nav-item[data-active='true'] .nav-icon {
    @apply text-blue-200;
}

.nav-icon {
    @apply h-5 w-5 flex-shrink-0 transition-transform duration-200;
}

.nav-item:hover .nav-icon {
    @apply scale-110;
}

.nav-text {
    @apply flex-1 text-sm text-slate-200 opacity-100;
}

.nav-chevron {
    @apply h-4 w-4 text-slate-300 opacity-70 transition-transform duration-300;
}

.nav-submenu {
    @apply mb-2 ml-7 mt-1 space-y-1 border-l border-white/10 pl-4 !important;
}

.nav-subitem {
    @apply flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] text-slate-400 transition-all hover:bg-white/10 hover:text-white !important;
}

.nav-subitem[data-active='true'] {
    @apply bg-blue-500/10 font-bold text-blue-200 !important;
}

.nav-subicon {
    @apply h-4 w-4 shrink-0 opacity-60 transition-opacity;
}

.nav-subitem:hover .nav-subicon {
    @apply scale-110 text-blue-300 opacity-100;
}
</style>
