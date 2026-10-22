<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div
        class="min-h-svh bg-[radial-gradient(ellipse_at_top_right,rgba(91,33,182,0.16),transparent_42%),radial-gradient(ellipse_at_bottom_left,rgba(190,24,93,0.12),transparent_38%),#08090f]"
    >
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent
                variant="sidebar"
                class="flex flex-col overflow-x-hidden !bg-transparent"
            >
                <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                <div class="min-w-0 flex-1">
                    <slot />
                </div>
                <AppFooter />
            </AppContent>
        </AppShell>
    </div>
</template>
