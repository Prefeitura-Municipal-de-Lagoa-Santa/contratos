<script setup lang="ts">
import { CircleHelp } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    label: string;
    fieldId: string;
    description: string;
}>();

const isVisible = ref(false);
</script>

<template>
    <div
        class="mb-2"
        @mouseenter="isVisible = true"
        @mouseleave="isVisible = false"
    >
        <div class="flex items-center gap-1.5">
            <label
                :for="fieldId"
                class="text-sm font-medium tracking-normal text-slate-700 dark:text-slate-300"
                >{{ label }}</label
            >
            <span class="relative inline-flex">
                <button
                    type="button"
                    :aria-label="`Ajuda sobre ${label.toLowerCase()}`"
                    :aria-expanded="isVisible"
                    :aria-describedby="
                        isVisible ? `${fieldId}-help` : undefined
                    "
                    @focus="isVisible = true"
                    @blur="isVisible = false"
                    class="rounded-full text-slate-400 transition hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                >
                    <CircleHelp class="h-4 w-4" />
                </button>
                <span
                    v-if="isVisible"
                    :id="`${fieldId}-help`"
                    role="tooltip"
                    class="absolute bottom-full left-0 z-50 mb-3 w-max max-w-[min(20rem,calc(100vw-3rem))] rounded-xl border border-slate-700/80 bg-slate-900 px-3.5 py-2.5 text-left text-xs font-normal leading-relaxed text-white shadow-xl shadow-slate-900/20"
                >
                    {{ description }}
                    <span
                        aria-hidden="true"
                        class="absolute -bottom-1 left-2 h-2 w-2 rotate-45 border-b border-r border-slate-700/80 bg-slate-900"
                    />
                </span>
            </span>
        </div>
    </div>
</template>
