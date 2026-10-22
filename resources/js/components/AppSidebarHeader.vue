<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="app-sidebar-header group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 flex h-16 shrink-0 items-center justify-between gap-2 border-b border-white/10 bg-white/[0.04] px-6 backdrop-blur-xl transition-[width,height] ease-linear md:px-4"
    >
        <div class="flex min-w-0 flex-1 items-center gap-2">
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
    </header>
</template>

<style scoped>
.app-sidebar-header :deep([data-slot='breadcrumb-page']) {
    color: #f8fafc;
}

.app-sidebar-header :deep([data-slot='breadcrumb-link']) {
    color: #cbd5e1;
}
</style>
