<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { ComboboxEmpty } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<{ class?: HTMLAttributes["class"] }>()
</script>

<template>
  <ComboboxEmpty
    data-slot="command-empty"
    :class="cn('py-6 text-center text-sm', props.class)"
  >
    <slot />
  </ComboboxEmpty>
</template>
