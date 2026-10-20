<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, Info, Lock, User as UserIcon } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    ldapEnabled: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <div
            class="rounded-3xl border border-slate-100 bg-white p-8 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] sm:p-12"
        >
            <!-- Header Section -->
            <div class="mb-10 text-left">
                <h1 class="mb-2 text-3xl font-extrabold text-slate-900">
                    Acesse sua conta
                </h1>
                <p class="font-medium text-slate-500">
                    Digite suas credenciais para continuar
                </p>
            </div>

            <!-- Status Message -->
            <div
                v-if="status"
                class="mb-8 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700"
            >
                {{ status }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- User/Email Field -->
                <div class="space-y-2">
                    <label
                        for="username"
                        class="ml-1 block text-sm font-bold text-slate-700"
                    >
                        Usuário de rede
                    </label>
                    <div class="group relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 transition-colors duration-300 group-focus-within:text-blue-600"
                        >
                            <UserIcon :size="20" class="text-slate-400" />
                        </div>
                        <input
                            id="username"
                            type="text"
                            class="w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-300 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-0"
                            v-model="form.username"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Digite seu usuário de rede"
                        />
                    </div>
                    <InputError
                        class="mt-2 text-xs font-medium text-rose-600"
                        :message="form.errors.username"
                    />
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label
                        for="password"
                        class="ml-1 block text-sm font-bold text-slate-700"
                    >
                        Senha
                    </label>
                    <div class="group relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 transition-colors duration-300 group-focus-within:text-blue-600"
                        >
                            <Lock :size="20" class="text-slate-400" />
                        </div>
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-4 pl-12 pr-12 text-slate-900 placeholder-slate-400 shadow-sm transition-all duration-300 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-0"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-slate-600 focus:outline-none"
                        >
                            <component
                                :is="showPassword ? EyeOff : Eye"
                                :size="20"
                            />
                        </button>
                    </div>
                    <InputError
                        class="mt-2 text-xs font-medium text-rose-600"
                        :message="form.errors.password"
                    />
                </div>

                <!-- Remember Me -->
                <div class="ml-1 flex items-center justify-between pt-2">
                    <label class="group flex cursor-pointer items-center">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="h-5 w-5 cursor-pointer rounded border-slate-300 text-slate-900 transition-all duration-300 focus:ring-slate-900"
                        />
                        <span
                            class="ml-3 text-sm font-medium text-slate-600 transition-colors group-hover:text-slate-900"
                        >
                            Manter-me conectado
                        </span>
                    </label>
                    <Link
                        v-if="canResetPassword && !ldapEnabled"
                        :href="route('password.request')"
                        class="text-sm font-bold text-blue-600 transition-colors hover:text-blue-700"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <!-- Login Button -->
                <div class="pt-4">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-800 px-6 py-4 text-lg font-bold text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-900 hover:shadow-xl active:translate-y-0 disabled:opacity-70"
                        :disabled="form.processing"
                    >
                        <div class="flex items-center justify-center gap-3">
                            <span
                                v-if="form.processing"
                                class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"
                            ></span>
                            <span v-else>Entrar</span>
                        </div>
                    </button>
                </div>
            </form>

            <!-- LDAP Notice Box -->
            <div
                v-if="ldapEnabled"
                class="mt-10 flex items-start gap-4 rounded-2xl border border-blue-100 bg-blue-50 p-5"
            >
                <div class="mt-1 shrink-0 rounded-full bg-blue-500 p-2">
                    <Info :size="16" class="text-white" />
                </div>
                <div>
                    <h4
                        class="mb-1 text-sm font-extrabold uppercase tracking-tight text-blue-900"
                    >
                        Autenticação via LDAP
                    </h4>
                    <p class="text-xs leading-relaxed text-blue-800">
                        Use o mesmo
                        <span class="font-bold">usuário e senha</span> que você
                        utiliza para fazer login no seu computador.
                    </p>
                </div>
            </div>

            <!-- Footer developed by -->
            <div class="mt-12 text-center">
                <p
                    class="text-[11px] font-bold uppercase tracking-widest text-slate-400"
                >
                    Desenvolvido por
                    <span class="text-slate-600"
                        >Prefeitura Municipal de Lagoa Santa</span
                    >
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Spinner for loading state */
.spinner {
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Staggered animations */
.animate-slide-up {
    animation: slideUp 0.5s ease-out backwards;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out backwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
