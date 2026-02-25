<script setup lang="ts">
import { resolveBadgeIcon } from '@/composables/useBadgeIcon';

const props = withDefaults(
    defineProps<{
        icon: string | null | undefined;
        iconClass?: string;
    }>(),
    { iconClass: 'size-4.5 shrink-0' },
);

const IconComponent = resolveBadgeIcon(props.icon);
</script>

<template>
    <component
        v-if="IconComponent"
        :is="IconComponent"
        :class="props.iconClass"
    />
</template>
