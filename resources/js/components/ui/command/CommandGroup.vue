<script setup lang="ts">
import type { ComboboxGroupProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ComboboxGroup, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<ComboboxGroupProps & { class?: HTMLAttributes["class"] }>()

const forwardedProps = useForwardProps(props)
</script>

<template>
  <ComboboxGroup
    v-bind="forwardedProps"
    :class="cn('overflow-hidden p-1 text-foreground [&_[data-slot=command-group-label]]:px-2 [&_[data-slot=command-group-label]]:py-1.5 [&_[data-slot=command-group-label]]:text-xs [&_[data-slot=command-group-label]]:font-medium [&_[data-slot=command-group-label]]:text-muted-foreground', props.class)"
  >
    <slot />
  </ComboboxGroup>
</template>
