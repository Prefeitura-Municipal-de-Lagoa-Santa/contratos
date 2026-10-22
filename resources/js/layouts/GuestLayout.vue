<script setup lang="ts">
withDefaults(
    defineProps<{
        variant?: 'default' | 'login';
    }>(),
    {
        variant: 'default',
    },
);
</script>

<template>
    <main
        v-if="variant === 'login'"
        class="login-background relative flex min-h-screen items-center justify-center overflow-hidden bg-black px-4 py-10 text-white sm:px-8"
    >
        <div
            class="absolute left-5 top-5 z-10 flex items-center gap-3 sm:left-8 sm:top-7 sm:gap-4"
        >
            <img
                src="/images/logos-completas.png"
                alt="Brasão da Prefeitura Municipal de Lagoa Santa"
                class="h-10 w-auto object-contain sm:h-12"
            />
            <span aria-hidden="true" class="h-8 w-px bg-white/25 sm:h-10" />
            <p
                class="text-[10px] font-semibold leading-tight tracking-[0.12em] text-white/90 sm:text-xs"
            >
                PREFEITURA MUNICIPAL
                <span
                    class="mt-1 block text-[11px] font-bold tracking-[0.16em] text-white sm:text-sm"
                >
                    DE LAGOA SANTA
                </span>
            </p>
        </div>

        <svg
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 h-full w-full"
            viewBox="0 0 1440 900"
            preserveAspectRatio="xMidYMid slice"
            fill="none"
        >
            <defs>
                <linearGradient
                    id="login-wave-warm"
                    x1="0"
                    y1="0"
                    x2="1"
                    y2="1"
                >
                    <stop stop-color="#ff493f" />
                    <stop offset="1" stop-color="#ffc978" />
                </linearGradient>
                <linearGradient
                    id="login-wave-pink"
                    x1="0"
                    y1="1"
                    x2="1"
                    y2="0"
                >
                    <stop stop-color="#763cff" />
                    <stop offset=".55" stop-color="#ed35d1" />
                    <stop offset="1" stop-color="#ff68dc" />
                </linearGradient>
                <filter
                    id="login-wave-glow"
                    x="-30%"
                    y="-30%"
                    width="160%"
                    height="160%"
                >
                    <feGaussianBlur stdDeviation="7" />
                </filter>
            </defs>

            <g
                class="login-wave login-wave-warm"
                stroke="url(#login-wave-warm)"
                stroke-width="1"
            >
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 7)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 14)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 21)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 28)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 35)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 42)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 49)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 56)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 63)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 70)"
                />
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    transform="translate(0 77)"
                />
            </g>

            <g
                class="login-wave login-wave-pink"
                stroke="url(#login-wave-pink)"
                stroke-width="1"
            >
                <path d="M650 360c170-90 265 10 405-55S1255 105 1490 35" />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 8)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 16)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 24)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 32)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 40)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 48)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 56)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 64)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 72)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 80)"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    transform="translate(0 88)"
                />
            </g>

            <g
                class="login-wave-glow"
                filter="url(#login-wave-glow)"
                opacity=".45"
            >
                <path
                    d="M-180 665C60 560 150 505 340 570s265 174 475 45 310-180 520-86 260 73 360-15"
                    stroke="url(#login-wave-warm)"
                    stroke-width="5"
                />
                <path
                    d="M650 360c170-90 265 10 405-55S1255 105 1490 35"
                    stroke="url(#login-wave-pink)"
                    stroke-width="5"
                />
            </g>
        </svg>

        <div
            aria-hidden="true"
            class="login-glass absolute left-1/2 top-1/2 aspect-square w-[min(88vw,500px)] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/20"
        />

        <div
            class="relative z-10 w-full max-w-[360px] px-3 py-8 sm:max-w-[380px] sm:px-6"
        >
            <slot />
        </div>

        <p
            class="absolute bottom-5 left-4 right-4 z-10 text-center text-[10px] font-medium uppercase tracking-[0.16em] text-white/55 sm:bottom-7 sm:text-[11px]"
        >
            Desenvolvido pela Prefeitura Municipal de Lagoa Santa
        </p>
    </main>

    <main
        v-else
        class="guest-auth-background relative flex min-h-screen items-center justify-center overflow-hidden bg-[#08090f] px-4 py-28 sm:px-8 sm:py-32"
    >
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div
                class="absolute -left-40 top-1/4 h-[28rem] w-[28rem] rounded-full bg-rose-600/15 blur-[110px]"
            />
            <div
                class="absolute -right-40 bottom-0 h-[30rem] w-[30rem] rounded-full bg-violet-600/20 blur-[120px]"
            />
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.06)_1px,transparent_0)] bg-[size:32px_32px]"
            />
        </div>

        <div
            class="absolute left-5 top-5 z-10 flex items-center gap-3 sm:left-8 sm:top-7 sm:gap-4"
        >
            <img
                src="/images/logos-completas.png"
                alt="Brasão da Prefeitura Municipal de Lagoa Santa"
                class="h-10 w-auto object-contain sm:h-12"
            />
            <span aria-hidden="true" class="h-8 w-px bg-white/25 sm:h-10" />
            <p
                class="text-[10px] font-semibold leading-tight tracking-[0.12em] text-white/90 sm:text-xs"
            >
                PREFEITURA MUNICIPAL
                <span
                    class="mt-1 block text-[11px] font-bold tracking-[0.16em] text-white sm:text-sm"
                >
                    DE LAGOA SANTA
                </span>
            </p>
        </div>

        <section
            class="relative z-10 w-full max-w-md rounded-3xl border border-white/60 bg-white/95 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-8"
        >
            <slot />
        </section>

        <p
            class="absolute bottom-5 left-4 right-4 z-10 text-center text-[10px] font-medium uppercase tracking-[0.16em] text-white/55 sm:bottom-7 sm:text-[11px]"
        >
            Desenvolvido pela Prefeitura Municipal de Lagoa Santa
        </p>
    </main>
</template>

<style scoped>
.login-glass {
    background:
        radial-gradient(
            circle at 68% 67%,
            rgb(20 116 119 / 18%),
            transparent 34%
        ),
        radial-gradient(
            circle at 84% 48%,
            rgb(141 42 128 / 20%),
            transparent 28%
        ),
        radial-gradient(
            circle at 25% 28%,
            rgb(255 255 255 / 10%),
            transparent 40%
        ),
        rgb(255 255 255 / 9%);
    box-shadow:
        inset 0 1px 1px rgb(255 255 255 / 18%),
        0 30px 100px rgb(0 0 0 / 50%);
    backdrop-filter: blur(28px);
}

.guest-auth-background {
    background-image:
        radial-gradient(
            ellipse at 50% 50%,
            rgb(255 255 255 / 3%),
            transparent 58%
        ),
        linear-gradient(135deg, #08090f, #11101c 55%, #08090f);
}

.login-wave {
    opacity: 0.8;
}

.login-wave-warm {
    animation: wave-drift 12s ease-in-out infinite alternate;
}

.login-wave-pink {
    animation: wave-drift 15s ease-in-out infinite alternate-reverse;
}

@keyframes wave-drift {
    from {
        transform: translate3d(-8px, 5px, 0);
    }

    to {
        transform: translate3d(8px, -5px, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .login-wave {
        animation: none;
    }
}
</style>
