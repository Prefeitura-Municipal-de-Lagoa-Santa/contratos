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
            class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-[0_24px_70px_-28px_rgba(15,23,42,0.28)] sm:p-9"
        >
            <div class="mb-8">
                <div
                    class="mb-6 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100"
                >
                    <Lock class="h-5 w-5" />
                </div>
                <h1
                    class="mb-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl"
                >
                    Acesse sua conta
                </h1>
                <p class="text-sm leading-6 text-slate-500">
                    Digite suas credenciais para continuar
                </p>
            </div>

            <div
                v-if="status"
                class="mb-6 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="space-y-2">
                    <label
                        for="username"
                        class="block text-sm font-semibold text-slate-700"
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
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-400 transition duration-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10"
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

                <div class="space-y-2">
                    <label
                        for="password"
                        class="block text-sm font-semibold text-slate-700"
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
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-12 text-sm text-slate-900 placeholder-slate-400 transition duration-200 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10"
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

                <div class="flex items-center justify-between pt-1">
                    <label class="group flex cursor-pointer items-center">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span
                            class="ml-2.5 text-sm text-slate-600 transition-colors group-hover:text-slate-900"
                        >
                            Manter-me conectado
                        </span>
                    </label>
                    <Link
                        v-if="canResetPassword && !ldapEnabled"
                        :href="route('password.request')"
                        class="text-sm font-semibold text-blue-700 transition-colors hover:text-blue-800"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition duration-200 hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-blue-700/20 active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-70"
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

            <div
                v-if="ldapEnabled"
                class="mt-6 flex items-start gap-3 rounded-xl border border-blue-100/90 bg-blue-50/80 p-4"
            >
                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white"
                >
                    <Info class="h-4 w-4" />
                </div>
                <div>
                    <h4
                        class="mb-1 text-xs font-bold uppercase tracking-wide text-blue-950"
                    >
                        Autenticação via LDAP
                    </h4>
                    <p class="text-xs leading-5 text-blue-800">
                        Use o mesmo
                        <span class="font-bold">usuário e senha</span> que você
                        utiliza para fazer login no seu computador.
                    </p>
                </div>
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
</style>
