<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { ComboboxContent } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<{ class?: HTMLAttributes["class"] }>()
</script>

<template>
  <ComboboxContent
    position="inline"
    :class="cn(
      'max-h-[min(var(--reka-popover-content-available-height),300px)] overflow-hidden p-1',
      props.class,
    )"
  >
    <slot />
  </ComboboxContent>
</template>
