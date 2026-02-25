<script setup lang="ts">
import type { ComboboxInputProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ComboboxAnchor, ComboboxInput, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = withDefaults(
  defineProps<ComboboxInputProps & { class?: HTMLAttributes["class"] }>(),
  {
    placeholder: "Search...",
  },
)

const forwardedProps = useForwardProps(props)
</script>

<template>
  <ComboboxAnchor
    :class="cn(
      'flex h-9 w-full items-center gap-2 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors',
      'focus-within:outline-none focus-within:ring-1 focus-within:ring-ring',
      props.class,
    )"
  >
    <ComboboxInput
      v-bind="forwardedProps"
      class="flex h-9 w-full min-w-0 flex-1 rounded-md bg-transparent py-3 pl-1 pr-2 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
    />
  </ComboboxAnchor>
</template>
