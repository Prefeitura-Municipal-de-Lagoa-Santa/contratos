<script setup lang="ts">
import { FileText } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row overflow-hidden bg-white">
        <!-- Left Panel: Institutional Branding -->
        <div class="hidden md:flex md:w-[55%] lg:w-[60%] relative overflow-hidden bg-gradient-to-br from-[#1e293b] via-[#0f172a] to-[#020617] items-center justify-center p-12">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-500 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[10%] right-[10%] w-[30%] h-[30%] bg-indigo-500 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative z-10 text-center max-w-xl">
                <!-- Unified Institutional Logo -->
                <div class="flex items-center justify-center mb-12">
                    <img src="/images/logos-completas.png" alt="Prefeitura Municipal de Lagoa Santa" class="h-32 w-auto drop-shadow-2xl brightness-110" />
                </div>

                <!-- Titles -->
                <div class="space-y-4 mb-16">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                        Prefeitura Municipal de <br/>
                        <span class="text-blue-400">Lagoa Santa</span>
                    </h1>
                    <div class="h-1 w-24 bg-blue-500 mx-auto rounded-full"></div>
                    <h2 class="text-xl lg:text-2xl font-medium text-slate-300">
                        Sistema de Processos Licitatórios
                    </h2>
                    <p class="text-slate-400 text-lg">
                        Gerencie e organize contratos, comunicações e ofícios de forma simples e eficiente.
                    </p>
                </div>

                <!-- Illustration placeholder - using a clean SVG/Icon group -->
                <div class="relative mt-auto">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-2xl inline-block transform -rotate-1 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 text-white/80 opacity-60">
                            <div class="p-4 bg-blue-500/20 rounded-2xl">
                                <FileText class="w-12 h-12 text-blue-400" />
                            </div>
                            <div class="flex flex-col text-left">
                                <span class="text-sm font-bold uppercase tracking-wider text-blue-400">Módulo Administrativo</span>
                                <span class="text-xl font-medium">Controle de Documentos 3.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer for Left Column -->
            <div class="absolute bottom-8 left-0 w-full text-center">
                <p class="text-slate-500 text-sm font-medium tracking-wide">
                    Prefeitura Municipal de Lagoa Santa © 2026
                </p>
            </div>
        </div>

        <!-- Right Panel: Login Form Area -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-slate-50 relative">
            <div class="absolute inset-0 bg-grid-slate-200/[0.3] pointer-events-none"></div>
            
            <div class="w-full max-w-md relative z-10">
                <!-- Mobile Logo -->
                <div class="md:hidden text-center mb-8">
                    <img src="/images/logos-completas.png" alt="Logo" class="h-20 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-slate-900">Processos Licitatórios</h2>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-grid-slate-200 {
    background-image: radial-gradient(circle, rgba(15, 23, 42, 0.05) 1px, transparent 1px);
    background-size: 30px 30px;
}

/* Base animations */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
</style>
