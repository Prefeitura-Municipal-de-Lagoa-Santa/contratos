<script setup lang="ts">
import { FileText, ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <main class="min-h-screen bg-slate-50 md:grid md:grid-cols-[1.05fr_0.95fr]">
        <section
            class="relative isolate flex min-h-[220px] items-center overflow-hidden bg-slate-950 px-6 py-8 text-white sm:px-10 md:min-h-screen md:justify-center md:px-12 md:py-16"
        >
            <div
                aria-hidden="true"
                class="absolute inset-0 -z-10 overflow-hidden"
            >
                <div
                    class="absolute -left-40 -top-40 h-[30rem] w-[30rem] rounded-full bg-blue-600/20 blur-[100px]"
                />
                <div
                    class="absolute -bottom-48 -right-32 h-[32rem] w-[32rem] rounded-full bg-indigo-500/20 blur-[110px]"
                />
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] bg-[size:28px_28px] opacity-30"
                />
            </div>

            <div class="w-full max-w-xl">
                <div
                    class="mb-6 flex items-center gap-4 md:mb-12 md:flex-col md:items-start"
                >
                    <img
                        src="/images/logos-completas.png"
                        alt="Prefeitura Municipal de Lagoa Santa"
                        class="h-14 w-auto object-contain drop-shadow-xl md:h-20"
                    />
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-300"
                        >
                            Prefeitura Municipal
                        </p>
                        <h1
                            class="mt-1 text-xl font-bold tracking-tight sm:text-2xl md:mt-3 md:text-4xl"
                        >
                            Lagoa Santa
                        </h1>
                    </div>
                </div>

                <div class="hidden md:block">
                    <p
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-blue-100"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                        Plataforma institucional
                    </p>
                    <h2
                        class="max-w-lg text-4xl font-bold leading-tight tracking-tight lg:text-5xl"
                    >
                        Processos licitatórios
                        <span class="mt-1 block text-blue-400">
                            com mais clareza e agilidade.
                        </span>
                    </h2>
                    <p class="mt-5 max-w-lg text-base leading-7 text-slate-300">
                        Um espaço único para organizar contratos, solicitações e
                        documentos da administração municipal.
                    </p>

                    <div
                        class="mt-10 inline-flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-4 shadow-2xl shadow-black/10 backdrop-blur"
                    >
                        <span
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300"
                        >
                            <FileText class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">
                                Gestão em um só lugar
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Acesso seguro para servidores
                            </p>
                        </div>
                        <ShieldCheck class="ml-4 h-5 w-5 text-emerald-300" />
                    </div>
                </div>

                <p
                    class="absolute bottom-6 left-6 hidden text-xs text-slate-500 md:left-12 md:block"
                >
                    © {{ new Date().getFullYear() }} Prefeitura Municipal de
                    Lagoa Santa
                </p>
            </div>
        </section>

        <section
            class="relative flex min-h-[calc(100vh-220px)] items-center justify-center overflow-hidden px-5 py-10 sm:px-8 md:min-h-screen md:px-10 md:py-14"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -right-24 top-10 h-72 w-72 rounded-full bg-blue-100/70 blur-3xl"
            />
            <div
                class="relative z-10 w-full max-w-[440px] animate-[fade-in_500ms_ease-out]"
            >
                <slot />
                <p
                    class="mt-6 text-center text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400"
                >
                    Desenvolvido pela Prefeitura Municipal de Lagoa Santa
                </p>
            </div>
        </section>
    </main>
</template>
