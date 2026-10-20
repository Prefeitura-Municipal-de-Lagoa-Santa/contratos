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
            <label :for="fieldId" class="text-sm font-medium text-gray-700">{{
                label
            }}</label>
            <button
                type="button"
                :aria-label="`Ajuda sobre ${label.toLowerCase()}`"
                :aria-expanded="isVisible"
                :aria-controls="`${fieldId}-help`"
                @focus="isVisible = true"
                @blur="isVisible = false"
                class="rounded-full text-gray-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <CircleHelp class="h-4 w-4" />
            </button>
        </div>
        <p
            v-if="isVisible"
            :id="`${fieldId}-help`"
            class="mt-2 max-w-prose rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2 text-sm leading-relaxed text-indigo-900"
        >
            {{ description }}
        </p>
    </div>
</template>
