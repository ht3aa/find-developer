<script setup lang="ts">
import type { ComboboxItemProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ComboboxItem, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<ComboboxItemProps & { class?: HTMLAttributes["class"] }>()

const forwardedProps = useForwardProps(props)
</script>

<template>
  <ComboboxItem
    v-bind="forwardedProps"
    :class="cn(
      'relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none',
      'data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground',
      'data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      props.class,
    )"
  >
    <slot />
  </ComboboxItem>
</template>
