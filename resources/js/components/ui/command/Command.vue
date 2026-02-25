<script setup lang="ts">
import type { ComboboxRootEmits, ComboboxRootProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ComboboxRoot, useForwardPropsEmits } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<ComboboxRootProps & { class?: HTMLAttributes["class"] }>()
const emits = defineEmits<ComboboxRootEmits>()

const delegatedProps = { ...props }
const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <ComboboxRoot
    v-bind="forwarded"
    :class="cn('flex h-full w-full flex-col overflow-hidden rounded-md', props.class)"
  >
    <slot />
  </ComboboxRoot>
</template>
