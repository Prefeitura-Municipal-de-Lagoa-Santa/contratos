<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { FileText, LifeBuoy } from 'lucide-vue-next';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer
        class="mt-auto border-t border-slate-200/80 bg-white/80 px-4 py-3 backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/80 sm:px-6"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 sm:flex-row"
        >
            <div class="flex items-center gap-2 text-sm">
                <span
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-300"
                >
                    <FileText class="h-4 w-4" />
                </span>
                <span class="font-semibold text-slate-700 dark:text-slate-200">
                    Processos Licitatórios
                </span>
                <span class="hidden text-slate-400 sm:inline">·</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    © {{ currentYear }} Prefeitura de Lagoa Santa
                </span>
            </div>

            <Link
                href="/chamados"
                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
            >
                <LifeBuoy class="h-3.5 w-3.5" />
                Suporte
            </Link>
        </div>
    </footer>
</template>
