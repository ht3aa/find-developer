<script setup lang="ts">
import { resolveBadgeIcon } from '@/composables/useBadgeIcon';

const props = defineProps<{
    icon: string | null | undefined;
}>();

const IconComponent = resolveBadgeIcon(props.icon);
</script>

<template>
    <component
        v-if="IconComponent"
        :is="IconComponent"
        class="size-4.5 shrink-0"
    />
</template>
