<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { FileText, LayoutDashboard, LifeBuoy } from 'lucide-vue-next';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer
        class="mt-auto border-t border-slate-700/70 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-slate-300"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6"
        >
            <div class="flex items-center gap-2.5">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400"
                >
                    <FileText class="h-4 w-4" />
                </span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-white">
                        Processos Licitatórios
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">
                        Prefeitura de Lagoa Santa
                    </p>
                </div>
            </div>

            <nav
                aria-label="Links do rodapé"
                class="flex items-center gap-1 text-xs font-medium"
            >
                <Link
                    href="/dashboard"
                    class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-2 text-slate-300 transition hover:bg-white/5 hover:text-white"
                >
                    <LayoutDashboard class="h-3.5 w-3.5" />
                    Painel
                </Link>
                <Link
                    href="/chamados"
                    class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-2 text-slate-300 transition hover:bg-white/5 hover:text-white"
                >
                    <LifeBuoy class="h-3.5 w-3.5" />
                    Suporte
                </Link>
            </nav>
        </div>

        <div class="border-t border-white/5">
            <p
                class="mx-auto max-w-7xl px-4 py-2 text-center text-[11px] text-slate-500 sm:px-6 sm:text-left"
            >
                © {{ currentYear }} Prefeitura Municipal de Lagoa Santa
            </p>
        </div>
    </footer>
</template>
