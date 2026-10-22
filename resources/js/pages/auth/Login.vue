<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EyeOff, KeyRound, UserRound } from 'lucide-vue-next';
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

const identifierError = () => form.errors.username || form.errors.email;
</script>

<template>
    <GuestLayout variant="login">
        <Head title="Login" />

        <section class="w-full text-white">
            <header class="mb-7 text-center">
                <h1
                    class="text-xl font-semibold leading-tight tracking-wide text-white sm:text-2xl"
                >
                    Central de Demandas - CD
                </h1>
                <p class="mt-1 text-sm text-white/65">
                    Acesse sua conta institucional
                </p>
            </header>

            <div
                v-if="status"
                class="mb-5 rounded-xl border border-white/20 bg-black/20 px-4 py-3 text-sm font-medium text-white"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="username" class="sr-only">
                        Usuário de rede
                    </label>
                    <div
                        class="login-input group relative flex items-center rounded-2xl border border-white/25 transition focus-within:border-teal-200/80 focus-within:bg-teal-700/25 focus-within:ring-4 focus-within:ring-teal-300/10"
                    >
                        <input
                            id="username"
                            v-model="form.username"
                            type="text"
                            class="min-w-0 flex-1 rounded-2xl border-0 bg-transparent py-4 pl-5 pr-2 text-sm text-white placeholder:text-white/75 focus:outline-none focus:ring-0"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Usuário"
                        />
                        <div
                            class="pointer-events-none flex w-14 shrink-0 justify-center text-white"
                        >
                            <UserRound
                                class="h-7 w-7 fill-white/90 stroke-slate-700"
                            />
                        </div>
                    </div>
                    <InputError
                        class="mt-2 text-xs font-medium text-rose-200"
                        :message="identifierError()"
                    />
                </div>

                <div>
                    <label for="password" class="sr-only">Senha</label>
                    <div
                        class="login-input group relative flex items-center rounded-2xl border border-white/25 transition focus-within:border-teal-200/80 focus-within:bg-teal-700/25 focus-within:ring-4 focus-within:ring-teal-300/10"
                    >
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="min-w-0 flex-1 rounded-2xl border-0 bg-transparent py-4 pl-5 pr-2 text-sm text-white placeholder:text-white/75 focus:outline-none focus:ring-0"
                            required
                            autocomplete="current-password"
                            placeholder="Senha"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="flex w-14 shrink-0 items-center justify-center text-white transition hover:text-teal-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            :aria-label="
                                showPassword ? 'Ocultar senha' : 'Mostrar senha'
                            "
                        >
                            <component
                                :is="showPassword ? EyeOff : KeyRound"
                                class="h-7 w-7"
                            />
                        </button>
                    </div>
                    <InputError
                        class="mt-2 text-xs font-medium text-rose-200"
                        :message="form.errors.password"
                    />
                </div>

                <div
                    class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 pt-1 text-xs sm:text-sm"
                >
                    <label
                        class="group flex cursor-pointer items-center text-white/90"
                    >
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 cursor-pointer rounded border-white/50 bg-white/10 text-fuchsia-500 focus:ring-fuchsia-300"
                        />
                        <span
                            class="ml-2.5 transition-colors group-hover:text-white"
                        >
                            Manter-me conectado
                        </span>
                    </label>
                    <Link
                        v-if="canResetPassword && !ldapEnabled"
                        :href="route('password.request')"
                        class="font-medium text-white/90 underline decoration-white/35 underline-offset-4 transition hover:text-white hover:decoration-white"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <div class="flex justify-center pt-1">
                    <button
                        type="submit"
                        class="min-w-32 rounded-xl border border-white/30 bg-black/30 px-7 py-3 text-sm font-semibold tracking-wide text-white shadow-lg shadow-black/30 transition duration-200 hover:-translate-y-0.5 hover:border-white/55 hover:bg-white/15 active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-70"
                        :disabled="form.processing"
                    >
                        <div class="flex items-center justify-center gap-3">
                            <span
                                v-if="form.processing"
                                class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"
                            ></span>
                            <span v-else>LOGIN</span>
                        </div>
                    </button>
                </div>
            </form>

            <div
                v-if="ldapEnabled"
                class="mt-5 text-center text-xs leading-5 text-white/70"
            >
                Use seu usuário e senha de rede para entrar.
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.login-input {
    background:
        radial-gradient(
            circle at 70% 50%,
            rgb(13 148 136 / 22%),
            transparent 58%
        ),
        rgb(255 255 255 / 9%);
    box-shadow:
        inset 0 1px 1px rgb(255 255 255 / 12%),
        0 10px 25px rgb(0 0 0 / 12%);
    backdrop-filter: blur(12px);
}
</style>
