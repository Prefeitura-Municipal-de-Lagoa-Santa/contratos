<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { FileSignature } from 'lucide-vue-next';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <FileSignature :class="className" aria-label="Processos Licitatórios" v-bind="$attrs" />
</template>
