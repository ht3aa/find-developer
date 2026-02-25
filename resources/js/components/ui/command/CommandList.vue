<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { ComboboxViewport } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<{ class?: HTMLAttributes["class"] }>()
</script>

<template>
  <ComboboxViewport
    data-slot="command-list"
    :class="cn('max-h-[300px] overflow-y-auto overflow-x-hidden', props.class)"
  >
    <slot />
  </ComboboxViewport>
</template>
